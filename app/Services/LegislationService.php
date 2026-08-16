<?php

namespace App\Services;

use App\Models\Legislation;
use App\Jobs\GenerateEmbeddingJob;
use Laravel\Ai\Embeddings;

class LegislationService
{
    /**
     * Real Vector Semantic Search using laravel/ai embeddings.
     */
    public function semantic_search(string $search_word, int $limit = 10): array
    {
        // 1. توليد الرد ثم استخراج المتجه (array) منه
        $response = Embeddings::for([$search_word])
            ->generate('gemini', 'gemini-embedding-001');
        $queryEmbedding = $response->embeddings[0] ?? null;

        if (!$queryEmbedding) {
            return [
                'success' => false,
                'message' => 'فشل في توليد المتجهات للبحث',
                'data'    => [],
            ];
        }

        // 2. جلب التشريعات اللي عندها متجه فقط
        $legislations = Legislation::with(['category'])
            ->whereNotNull('embedding')
            ->get();

        // 3. حساب التشابه الجيبي (cosine) لكل تشريع
        $results = $legislations->map(function ($legislation) use ($queryEmbedding) {
            return [
                'legislation' => $legislation,
                'score'       => Legislation::calculateCosineSimilarity($queryEmbedding, $legislation->embedding),
            ];
        })
            ->filter(fn($item) => $item['score'] > 0.6)
            ->sortByDesc('score')
            ->take($limit);

        // 4. تنسيق الرد
        $formatted = $results->map(function ($item) {
            $legislation = $item['legislation'];
            return [
                'id'               => $legislation->id,
                'Book'             => $legislation->book,
                'part'             => $legislation->part,
                'chapter'          => $legislation->chapter,
                'title'            => $legislation->title,
                'description'      => $legislation->content,
                'law_number'       => $legislation->law_number,
                'status'           => $legislation->status,
                'country'          => $legislation->country,
                'source_newspaper' => $legislation->source,
                'similarity_score' => round($item['score'], 4),
                'publish_date'     => $legislation->effective_date
                    ? $legislation->effective_date->format('Y-m-d')
                    : null,
                'category'         => $legislation->category ? [
                    'id'   => $legislation->category->id,
                    'name' => $legislation->category->name,
                    'icon' => $legislation->category->icon,
                ] : null,
            ];
        })->values();

        return [
            'success' => true,
            'message' => 'تم جلب التشريعات بالبحث الدلالي بنجاح',
            'data'    => $formatted,
        ];
    }

    public function create_law(array $validatedData)
    {
        $validatedData['source'] = 'admin';
        $validatedData['status'] = $validatedData['status'] ?? 'active';

        $legislation = Legislation::create($validatedData);

        // Dispatch job to generate AI embeddings asynchronously
        dispatch(new GenerateEmbeddingJob($legislation));

        return [
            'success' => true,
            'message' => 'تم إنشاء التشريع وجاري توليد المتجهات الذكية بنجاح',
            'data'    => $legislation,
        ];
    }

    public function update_law(array $validatedData)
    {
        $law = Legislation::findOrFail($validatedData['law_id']);
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
            $law->update(['embedding' => null]);
            dispatch(new GenerateEmbeddingJob($law));
        }

        return [
            'success' => true,
            'message' => 'تم تحديث التشريع بنجاح',
            'data'    => $law,
        ];
    }

    public function getLegislations($requestData)
    {
        $query = Legislation::with('category');

        $query->when(isset($requestData['status']), fn($q) => $q->where('status', $requestData['status']));
        $query->when(isset($requestData['category_id']), fn($q) => $q->where('category_id', $requestData['category_id']));
        $query->when(isset($requestData['country']), fn($q) => $q->where('country', $requestData['country']));

        $legislations = $query->latest()->paginate(20);

        $formattedLegislations = $legislations->getCollection()->map(function ($legislation) {
            return [
                'id'               => $legislation->id,
                'Book'             => $legislation->book,
                'part'             => $legislation->part,
                'chapter'          => $legislation->chapter,
                'title'            => $legislation->title,
                'description'      => $legislation->content,
                'law_number'       => $legislation->law_number,
                'status'           => $legislation->status,
                'country'          => $legislation->country,
                'source_newspaper' => $legislation->source,
                'publish_date'     => $legislation->effective_date,
                'category'         => $legislation->category ? [
                    'id'   => $legislation->category->id,
                    'name' => $legislation->category->name,
                    'icon' => $legislation->category->icon,
                ] : null,
            ];
        });

        return [
            'success' => true,
            'message' => 'تم جلب التشريعات بنجاح',
            'data'    => [
                'legislations' => $formattedLegislations,
                'pagination'   => [
                    'current_page' => $legislations->currentPage(),
                    'total_pages'  => $legislations->lastPage(),
                    'total_items'  => $legislations->total(),
                    'per_page'     => $legislations->perPage(),
                ],
            ],
        ];
    }

    public function get_law_details($id)
    {
        $legislation = Legislation::with(['category'])->findOrFail($id);
        $relatedLegislations = [];

        if (!empty($legislation->related_legislation_ids)) {
            $relatedLegislations = Legislation::whereIn('id', $legislation->related_legislation_ids)
                ->select('id', 'title')
                ->get()
                ->toArray();
        }

        return [
            'success' => true,
            'message' => 'تم جلب تفاصيل التشريع بنجاح',
            'data'    => [
                'id'                   => $legislation->id,
                'title'                => $legislation->title,
                'content'              => $legislation->content,
                'law_number'           => $legislation->law_number,
                'status'               => $legislation->status,
                'country'              => $legislation->country,
                'source_newspaper'     => $legislation->source,
                'publish_date'         => $legislation->effective_date,
                'book'                 => $legislation->book,
                'part'                 => $legislation->part,
                'chapter'              => $legislation->chapter,
                'category'             => $legislation->category ? [
                    'id'   => $legislation->category->id,
                    'name' => $legislation->category->name,
                    'icon' => $legislation->category->icon,
                ] : null,
                'articles'             => $legislation->articles ?? [],
                'related_legislations' => $relatedLegislations,
            ]
        ];
    }

    public function delete_law(string $law_id)
    {
        $legislation = Legislation::findOrFail($law_id);
        $legislation->delete();

        return [
            'success' => true,
            'message' => 'تم حذف التشريع بنجاح',
            'data'    => [],
        ];
    }
}
 // old Code 
// namespace App\Services;

// use App\Models\Legislation;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Http\Exceptions\HttpResponseException;

// class LegislationService
// {

//     public function getLegislations($requestData)
//     {
//         $query = Legislation::with('category');

//         $query->when(isset($requestData['status']), function ($q) use ($requestData) {
//             $q->where('status', $requestData['status']);
//         });

//         $query->when(isset($requestData['category_id']), function ($q) use ($requestData) {
//             $q->where('category_id', $requestData['category_id']);
//         });

//         $query->when(isset($requestData['country']), function ($q) use ($requestData) {
//             $q->where('country', $requestData['country']);
//         });

//         $legislations =   $query->latest()->paginate(20); //  $query->get(); //
//         $legislations->appends([
//             'status' => $requestData['status'] ?? null,
//             'category_id' => $requestData['category_id'] ?? null,
//             'country' => $requestData['country'] ?? null,
//         ]);

//         //  $formattedLegislations = collect($legislations)->map(function ($legislation) {
//         //  $formattedLegislations = $legislations->getCollection()->map(function ($legislation) {
//         $formattedLegislations = $legislations->getCollection()->map(function ($legislation) {
//             return [
//                 'id' => $legislation->id,
//                 'Book' => $legislation->book,
//                 'part' => $legislation->part,
//                 'chapter' => $legislation->chapter,
//                 'title' => $legislation->title,
//                 'description' => $legislation->content,
//                 'law_number' => $legislation->law_number,
//                 'status' => $legislation->status, // == 'active' ? 'ساري المفعول' : 'معدل',
//                 'country' => $legislation->country, // == 'syria' ? 'سوريا' : ($legislation->country == 'saudi' ? 'المملكة العربية السعودية' : 'الإمارات العربية المتحدة'),
//                 'source_newspaper' => $legislation->source, // ?? 'الجريدة الرسمية',
//                 'publish_date' => $legislation->effective_date, //  ? date('Y-m-d', strtotime($legislation->effective_date)) : null,
//                 'category' => $legislation->category ? [
//                     'id' => $legislation->category->id,
//                     'name' => $legislation->category->name,
//                     'icon' => $legislation->category->icon,
//                 ] : null,
//             ];
//         });

//         return [
//             'success' => true,
//             'message' => 'تم جلب التشريعات بنجاح',
//             'data' => [
//                 'legislations' => $formattedLegislations,
//                 'pagination' => [
//                     'current_page' => $legislations->currentPage(),
//                     'total_pages' => $legislations->lastPage(),
//                     'total_items' => $legislations->total(),
//                     'per_page' => $legislations->perPage(),
//                     'first_page_url' => $legislations->url(1),
//                     'last_page_url' => $legislations->url($legislations->lastPage()),
//                     'next_page_url' => $legislations->nextPageUrl(),
//                     'prev_page_url' => $legislations->previousPageUrl(),
//                 ],
//             ],
//         ];
//     }
//     public function get_law_details($id)
//     {
//         $legislation = Legislation::with(['category'])->find($id);
//         $relatedLegislations = [];
//         if (!empty($legislation->related_legislation_ids)) {
//             $relatedLegislations = Legislation::whereIn('id', $legislation->related_legislation_ids)
//                 ->select('id', 'title')
//                 ->get()
//                 ->toArray();
//         }
//         $formattedData = [
//             'id' => $legislation->id,
//             'title' => $legislation->title,
//             'content' => $legislation->content,
//             'law_number' => $legislation->law_number,
//             'status' => $legislation->status, // == 'active' ? 'ساري' : ($legislation->status == 'amended' ? 'معدل' : 'ملغى'),
//             'country' => $legislation->country, // == 'syria' ? 'سوريا' : ($legislation->country == 'saudi' ? 'المملكة العربية السعودية' : 'الإمارات العربية المتحدة'),
//             'source_newspaper' => $legislation->source, // == 'admin' ? 'وزارة العدل' : 'الجريدة الرسمية',
//             'publish_date' => $legislation->effective_date, //  ? date('d/m/Y', strtotime($legislation->effective_date)) : null,
//             'book' => $legislation->book,
//             'part' => $legislation->part,
//             'chapter' => $legislation->chapter,
//             'category' => $legislation->category ? [
//                 'id' => $legislation->category->id,
//                 'name' => $legislation->category->name,
//                 'icon' => $legislation->category->icon,
//             ] : null,
//             'articles' => $legislation->articles ?? [],
//             'related_legislations' => $relatedLegislations,
//         ];
//         return [
//             'success' => true,
//             'message' => 'تم جلب تفاصيل التشريع بنجاح',
//             'data'    => $formattedData
//         ];
//     }
//     public function semantic_search($search_word, $limit)
//     {
//         $legislations = Legislation::with(['category'])
//             ->where('title', 'LIKE', '%' . $search_word . '%')
//             ->latest()
//             ->paginate($limit);

//         $formattedLegislations = collect($legislations->items())->map(function ($legislation) {
//             return [
//                 'id' => $legislation->id,
//                 'Book' => $legislation->book,
//                 'part' => $legislation->part,
//                 'chapter' => $legislation->chapter,
//                 'title' => $legislation->title,
//                 'description' => $legislation->content,
//                 'law_number' => $legislation->law_number,
//                 'status' => $legislation->status,
//                 'country' => $legislation->country,
//                 'source_newspaper' => $legislation->source,
//                 'publish_date' => $legislation->effective_date ? date('Y-m-d', strtotime($legislation->effective_date)) : null,
//                 'category' => $legislation->category ? [
//                     'id' => $legislation->category->id,
//                     'name' => $legislation->category->name,
//                     'icon' => $legislation->category->icon,
//                 ] : null,
//             ];
//         });
//         return [
//             'success' => true,
//             'message' => 'تم جلب التشريعات المطابقة بنجاح',
//             'data' => $formattedLegislations
//         ];
//     }
//     public function create_law(array $validatedData)
//     {
//         // Add default values
//         $validatedData['source'] = 'admin';
//         $validatedData['status'] = 'active';
//         $legislation = Legislation::create($validatedData);
//         return [
//             'success' => true,
//             'message' => 'تم إنشاء التشريع بنجاح',
//             'data'    => $legislation,
//         ];
//     }
//     public function update_law(array $validatedData)
//     {
//         $law = Legislation::find($validatedData['law_id']);
//         $law->fill($validatedData);
//         if (!$law->isDirty()) {
//             return [
//                 'success' => true,
//                 'message' => 'لم يتم إجراء أي تغييرات على البيانات الحالية',
//                 'data'    => [],
//             ];
//         }
//         $contentChanged = $law->isDirty('content') || $law->isDirty('title');

//         $law->save();

//         if ($contentChanged) {
//             if ($law->content) {
//                 $law->update(['embedding' => null]);
//                 dispatch(new \App\Jobs\GenerateEmbeddingJob($law));
//             }
//             dispatch(new \App\Jobs\NotifyLawyersAboutLegislationUpdate($law));
//         }

//         return [
//             'success' => true,
//             'message' => 'تم تحديث التشريع بنجاح',
//             'data'    => [
//                 'id'         => $law->id,
//                 'title'      => $law->title,
//                 'law_number' => $law->law_number,
//                 'status'     => $law->status,
//                 'country'    => $law->country,
//                 'book'       => $law->book,
//                 'part'       => $law->part,
//                 'chapter'    => $law->chapter,
//                 'articles'   => $law->articles,
//             ],
//         ];
//     }
//     public function delete_law(string $law_id)
//     {
//         $legislation = Legislation::find($law_id);
//         // 1. جمع كل الأبناء المتسلسلين (Recursive) للحذف الآمن
//         $idsToDelete = $this->getAllDescendantIds($legislation->id);
//         $idsToDelete[] = $legislation->id; // إضافة الأب نفسه

//         // 2. حفظ معلومات التشريع للإشعارات (قبل الحذف)
//         $lawTitle = $legislation->title;
//         $lawNumber = $legislation->law_number;
//         $categoryId = $legislation->category_id;

//         // 3. حذف التشريع وكل أبنائه (Cascade Delete)
//         Legislation::whereIn('id', $idsToDelete)->delete();

//         // 4. إرسال إشعار للمحامين (FR-LB-05) - في الخلفية
//         dispatch(new \App\Jobs\NotifyLawyersAboutLegislationDeletion(
//             $lawTitle,
//             $lawNumber,
//             $categoryId
//         ));

//         return [
//             'success' => true,
//             'message' => 'تم حذف التشريع وجميع فروعه بنجاح',
//             'data' => [
//                 'deleted_count' => count($idsToDelete),
//             ],
//         ];
//     }
//     private function getAllDescendantIds(string $parentId): array
//     {
//         $ids = [];
//         $queue = [$parentId];

//         while (!empty($queue)) {
//             $currentId = array_shift($queue);

//             // جلب الأبناء المباشرين
//             $children = Legislation::where('parent_id', $currentId)->pluck('id')->toArray();

//             foreach ($children as $childId) {
//                 $ids[] = $childId;
//                 $queue[] = $childId; // إضافة الابن للطابور لمعالجة أبنائه
//             }
//         }

//         return $ids;
//     }
// }
