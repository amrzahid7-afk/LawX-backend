<?php

namespace App\Services;

use App\Models\Legislation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LegislationService
{

    public function getLegislations($requestData)
    {
        $query = Legislation::with('category');

        $query->when(isset($requestData['status']), function ($q) use ($requestData) {
            $q->where('status', $requestData['status']);
        });

        $query->when(isset($requestData['category_id']), function ($q) use ($requestData) {
            $q->where('category_id', $requestData['category_id']);
        });

        $query->when(isset($requestData['country']), function ($q) use ($requestData) {
            $q->where('country', $requestData['country']);
        });

        $legislations =   $query->latest()->paginate(1); //  $query->get(); //
        $legislations->appends([
            'status' => $requestData['status'] ?? null,
            'category_id' => $requestData['category_id'] ?? null,
            'country' => $requestData['country'] ?? null,
        ]);

        //  $formattedLegislations = collect($legislations)->map(function ($legislation) {
        //  $formattedLegislations = $legislations->getCollection()->map(function ($legislation) {
        $formattedLegislations = $legislations->getCollection()->map(function ($legislation) {
            return [
                'id' => $legislation->id,
                'Book' => $legislation->book,
                'part' => $legislation->part,
                'chapter' => $legislation->chapter,
                'title' => $legislation->title,
                'description' => $legislation->content,
                'law_number' => $legislation->law_number,
                'status' => $legislation->status, // == 'active' ? 'ساري المفعول' : 'معدل',
                'country' => $legislation->country, // == 'syria' ? 'سوريا' : ($legislation->country == 'saudi' ? 'المملكة العربية السعودية' : 'الإمارات العربية المتحدة'),
                'source_newspaper' => $legislation->source, // ?? 'الجريدة الرسمية',
                'publish_date' => $legislation->effective_date, //  ? date('Y-m-d', strtotime($legislation->effective_date)) : null,
                'category' => $legislation->category ? [
                    'id' => $legislation->category->id,
                    'name' => $legislation->category->name,
                    'icon' => $legislation->category->icon,
                ] : null,
            ];
        });

        return [
            'success' => true,
            'message' => 'تم جلب التشريعات بنجاح',
            'data' => [
                'legislations' => $formattedLegislations,
                'pagination' => [
                    'current_page' => $legislations->currentPage(),
                    'total_pages' => $legislations->lastPage(),
                    'total_items' => $legislations->total(),
                    'per_page' => $legislations->perPage(),
                    'first_page_url' => $legislations->url(1),
                    'last_page_url' => $legislations->url($legislations->lastPage()),
                    'next_page_url' => $legislations->nextPageUrl(),
                    'prev_page_url' => $legislations->previousPageUrl(),
                ],
            ],
        ];
    }
    public function get_law_details($id)
    {
        $legislation = Legislation::with(['category'])->find($id);
        $relatedLegislations = [];
        if (!empty($legislation->related_legislation_ids)) {
            $relatedLegislations = Legislation::whereIn('id', $legislation->related_legislation_ids)
                ->select('id', 'title')
                ->get()
                ->toArray();
        }
        $formattedData = [
            'id' => $legislation->id,
            'title' => $legislation->title,
            'content' => $legislation->content,
            'law_number' => $legislation->law_number,
            'status' => $legislation->status == 'active' ? 'ساري' : ($legislation->status == 'amended' ? 'معدل' : 'ملغى'),
            'country' => $legislation->country == 'syria' ? 'سوريا' : ($legislation->country == 'saudi' ? 'المملكة العربية السعودية' : 'الإمارات العربية المتحدة'),
            'source_newspaper' => $legislation->source == 'admin' ? 'وزارة العدل' : 'الجريدة الرسمية',
            'publish_date' => $legislation->effective_date ? date('d/m/Y', strtotime($legislation->effective_date)) : null,
            'book' => $legislation->book,
            'part' => $legislation->part,
            'chapter' => $legislation->chapter,
            'category' => $legislation->category ? [
                'id' => $legislation->category->id,
                'name' => $legislation->category->name,
                'icon' => $legislation->category->icon,
            ] : null,
            'articles' => $legislation->articles ?? [],
            'related_legislations' => $relatedLegislations,
        ];
        return [
            'success' => true,
            'message' => 'تم جلب تفاصيل التشريع بنجاح',
            'data'    => $formattedData
        ];
    }
    public function semantic_search($search_word, $limit)
    {
        $legislations = Legislation::with(['category'])
            ->where('title', 'LIKE', '%' . $search_word . '%')
            ->latest()
            ->paginate($limit);

        $formattedLegislations = collect($legislations->items())->map(function ($legislation) {
            return [
                'id' => $legislation->id,
                'Book' => $legislation->book,
                'part' => $legislation->part,
                'chapter' => $legislation->chapter,
                'title' => $legislation->title,
                'description' => $legislation->content,
                'law_number' => $legislation->law_number,
                'status' => $legislation->status,
                'country' => $legislation->country,
                'source_newspaper' => $legislation->source,
                'publish_date' => $legislation->effective_date ? date('Y-m-d', strtotime($legislation->effective_date)) : null,
                'category' => $legislation->category ? [
                    'id' => $legislation->category->id,
                    'name' => $legislation->category->name,
                    'icon' => $legislation->category->icon,
                ] : null,
            ];
        });
        return [
            'success' => true,
            'message' => 'تم جلب التشريعات المطابقة بنجاح',
            'data' => $formattedLegislations
        ];
    }
    // search with semantic 
    // public function semantic_search($search_word, $limit)
    // {
    //     $legislation = Legislation::semanticSearch($search_word, $limit);
    //     $legislation = $legislation->map(fn($r) => [
    //         'legislation' => $r['record'],
    //         'similarity_score' => round($r['similarity'], 4),
    //     ]);
    //     return [
    //         'success' => true,
    //         'message' => 'تم جلب التشريعات بنجاح',
    //         'data'    => $legislation
    //     ];
    // }

    public function create_law(array $validatedData)
    {
        // Add default values
        $validatedData['source'] = 'admin';
        $validatedData['status'] = 'active';
        $legislation = Legislation::create($validatedData);
        return [
            'success' => true,
            'message' => 'تم إنشاء التشريع بنجاح',
            'data'    => $legislation,
        ];
    }
    public function update_law(array $validatedData)
    {
        $law = Legislation::find($validatedData['law_id']);
        $law->fill($validatedData);
        if (!$law->isDirty()) {
            return [
                'success' => true,
                'message' => 'لم يتم إجراء أي تغييرات على البيانات الحالية',
                'data'    => [],
            ];
        }
        $contentChanged = $law->isDirty('content') || $law->isDirty('title');

        $law->save();

        if ($contentChanged) {
            if ($law->content) {
                $law->update(['embedding' => null]);
                dispatch(new \App\Jobs\GenerateEmbeddingJob($law));
            }
            dispatch(new \App\Jobs\NotifyLawyersAboutLegislationUpdate($law));
        }

        return [
            'success' => true,
            'message' => 'تم تحديث التشريع بنجاح',
            'data'    => [
                'id'         => $law->id,
                'title'      => $law->title,
                'law_number' => $law->law_number,
                'status'     => $law->status,
                'country'    => $law->country,
                'book'       => $law->book,
                'part'       => $law->part,
                'chapter'    => $law->chapter,
                'articles'   => $law->articles,
            ],
        ];
    }
    // Old update function 
    // public function update_law(array $validatedData)
    // {
    //     $law = Legislation::find($validatedData['law_id']);
    //     // 1. حفظ القيم القديمة للمقارنة لاحقاً
    //     $oldContent = $law->content;
    //     $oldTitle = $law->title;
    //     $oldParentId = $law->parent_id;
    //     $oldLevel = $law->level;
    //     // 3. التحقق من أن parent_id لا يشير إلى نفسه (Self-Reference)
    //     if (isset($validatedData['parent_id']) && $validatedData['parent_id'] === $law->id) {
    //         throw new HttpResponseException(response()->json([
    //             'success' => false,
    //             'message' => 'لا يمكن أن يكون التشريع أباً لنفسه',
    //         ], 422));
    //     }

    //     // 5. تحديث التشريع
    //     $law->update($validatedData);

    //     // 6. إعادة توليد الـ Embedding فقط إذا تغير المحتوى أو العنوان
    //     $contentChanged = ($oldContent !== $law->content) || ($oldTitle !== $law->title);
    //     if ($contentChanged && $law->content) {
    //         // حذف الـ Embedding القديم وتشغيل Job لتوليد جديد
    //         $law->update(['embedding' => null]);
    //         dispatch(new \App\Jobs\GenerateEmbeddingJob($law));
    //     }

    //     // 7. إرسال إشعار للمحامين إذا تم تحديث قانون (FR-LB-05)
    //     if ($contentChanged) {
    //         dispatch(new \App\Jobs\NotifyLawyersAboutLegislationUpdate($law));
    //     }

    //     // 8. إرجاع البيانات مع العلاقات
    //     return [
    //         'success' => true,
    //         'message' => 'تم تحديث التشريع بنجاح',
    //         'data'    => [
    //             'legislation' => $law->load(['category', 'parent', 'children'])
    //         ],
    //     ];
    // }
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
