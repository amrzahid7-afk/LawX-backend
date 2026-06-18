<?php

namespace App\Services;

use App\Models\Legislation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LegislationService
{
    /**
     * جلب التشريعات مع الفلترة والتحقق من البيانات
     */
    public function getLegislations($requestData)
    {

        $query = Legislation::with('category');
        // status filter
        $query->when(isset($requestData['status']), function ($q) use ($requestData) {
            $q->where('status', $requestData['status']);
        });
        // category_id filter
        $query->when(isset($requestData['category_id']), function ($q) use ($requestData) {
            $q->where('category_id', $requestData['category_id']);
        });
        // country filter
        $query->when(isset($requestData['country']), function ($q) use ($requestData) {
            $q->where('country', $requestData['country']);
        });
        $legislations = $query->latest()->get(); //->paginate(20);
        // return response;
        return [
            'success' => true,
            'message' => 'تم جلب التشريعات بنجاح',
            'data'    => [
                'legislations' =>  $legislations,
                // 'pagination' => [
                //     'current_page' => $legislations->currentPage(),
                //     'total_pages'  => $legislations->lastPage(),
                //     'total_items'  => $legislations->total(),
                //     'per_page'     => $legislations->perPage(),
                // ],
            ],
        ];
    }
    public function get_law_details($id)
    {
        $legislation = Legislation::with(['category', 'children', 'parent'])->find($id);
        return [
            'success' => true,
            'message' => 'تم جلب تفاصيل التشريع بنجاح',
            'data'    => $legislation
        ];
    }
    public function semantic_search($search_word, $limit)
    {
        $legislation = Legislation::semanticSearch($search_word, $limit);
        $legislation = $legislation->map(fn($r) => [
            'legislation' => $r['record'],
            'similarity_score' => round($r['similarity'], 4),
        ]);
        return [
            'success' => true,
            'message' => 'تم جلب التشريعات بنجاح',
            'data'    => $legislation
        ];
    }

    public function create_law(array $validatedData)
    {
        $validatedData['source'] = 'admin';
        if (!isset($validatedData['status'])) {
            $validatedData['status'] = 'active';
        }

        $legislation = Legislation::create($validatedData);

        return [
            'success' => true,
            'message' => 'تم إنشاء التشريع بنجاح',
            'data'    => [],
        ];
    }
    public function update_law(array $validatedData)
    {
        $law = Legislation::find($validatedData['law_id']);
        // 1. حفظ القيم القديمة للمقارنة لاحقاً
        $oldContent = $law->content;
        $oldTitle = $law->title;
        $oldParentId = $law->parent_id;
        $oldLevel = $law->level;
        // 3. التحقق من أن parent_id لا يشير إلى نفسه (Self-Reference)
        if (isset($validatedData['parent_id']) && $validatedData['parent_id'] === $law->id) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'لا يمكن أن يكون التشريع أباً لنفسه',
            ], 422));
        }

        // 5. تحديث التشريع
        $law->update($validatedData);

        // 6. إعادة توليد الـ Embedding فقط إذا تغير المحتوى أو العنوان
        $contentChanged = ($oldContent !== $law->content) || ($oldTitle !== $law->title);
        if ($contentChanged && $law->content) {
            // حذف الـ Embedding القديم وتشغيل Job لتوليد جديد
            $law->update(['embedding' => null]);
            dispatch(new \App\Jobs\GenerateEmbeddingJob($law));
        }

        // 7. إرسال إشعار للمحامين إذا تم تحديث قانون (FR-LB-05)
        if ($contentChanged) {
            dispatch(new \App\Jobs\NotifyLawyersAboutLegislationUpdate($law));
        }

        // 8. إرجاع البيانات مع العلاقات
        return [
            'success' => true,
            'message' => 'تم تحديث التشريع بنجاح',
            'data'    => [
                'legislation' => $law->load(['category', 'parent', 'children'])
            ],
        ];
    }
    public function delete_law(string $law_id)
    {
        $legislation = Legislation::find($law_id);
        // 1. جمع كل الأبناء المتسلسلين (Recursive) للحذف الآمن
        $idsToDelete = $this->getAllDescendantIds($legislation->id);
        $idsToDelete[] = $legislation->id; // إضافة الأب نفسه

        // 2. حفظ معلومات التشريع للإشعارات (قبل الحذف)
        $lawTitle = $legislation->title;
        $lawNumber = $legislation->law_number;
        $categoryId = $legislation->category_id;

        // 3. حذف التشريع وكل أبنائه (Cascade Delete)
        Legislation::whereIn('id', $idsToDelete)->delete();

        // 4. إرسال إشعار للمحامين (FR-LB-05) - في الخلفية
        dispatch(new \App\Jobs\NotifyLawyersAboutLegislationDeletion(
            $lawTitle,
            $lawNumber,
            $categoryId
        ));

        return [
            'success' => true,
            'message' => 'تم حذف التشريع وجميع فروعه بنجاح',
            'data' => [
                'deleted_count' => count($idsToDelete),
            ],
        ];
    }
    private function getAllDescendantIds(string $parentId): array
    {
        $ids = [];
        $queue = [$parentId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);

            // جلب الأبناء المباشرين
            $children = Legislation::where('parent_id', $currentId)->pluck('id')->toArray();

            foreach ($children as $childId) {
                $ids[] = $childId;
                $queue[] = $childId; // إضافة الابن للطابور لمعالجة أبنائه
            }
        }

        return $ids;
    }
}
