<?php

namespace App\Traits;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

trait SemanticSearch
{
    /**
     * توليد Embedding من نص
     * 
     * @param string $text النص المراد تحويله إلى Embedding
     * @return array|null مصفوفة من 1536 رقم، أو null في حالة الفشل
     */
    public static function generateEmbedding(string $text): ?array
    {
        try {
            $result = OpenAI::embeddings()->create([
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);

            return $result->embeddings[0]->embedding; // مصفوفة من 1536 رقم
        } catch (\Exception $e) {
            Log::error('Failed to generate embedding: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * حساب Cosine Similarity بين متجهين
     * 
     * @param array $vec1 المتجه الأول
     * @param array $vec2 المتجه الثاني
     * @return float درجة التشابه (من 0 إلى 1)
     */
    public static function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $magnitude1 += $vec1[$i] ** 2;
            $magnitude2 += $vec2[$i] ** 2;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * البحث الدلالي (Semantic Search)
     * 
     * @param string $query كلمة البحث
     * @param int $limit عدد النتائج المطلوبة
     * @return Collection مجموعة من النتائج مرتبة حسب التشابه
     */
    public static function semanticSearch(string $query, int $limit = 10): Collection
    {
        // 1. توليد Embedding لكلمة البحث
        $queryEmbedding = self::generateEmbedding($query);

        if (!$queryEmbedding) {
            return collect();
        }

        // 2. جلب جميع السجلات التي تمتلك Embeddings
        // ملاحظة: self:: هنا تشير إلى الـ Model الذي يستخدم هذا Trait
        $records = static::whereNotNull('embedding')->get();

        // 3. حساب Cosine Similarity لكل سجل
        $results = $records->map(function ($record) use ($queryEmbedding) {
            $similarity = self::cosineSimilarity($queryEmbedding, $record->embedding);
            return [
                'record' => $record,
                'similarity' => $similarity,
            ];
        });

        // 4. ترتيب النتائج حسب التشابه (الأعلى أولاً)
        return $results->sortByDesc('similarity')
            ->take($limit)
            ->values();
    }

    /**
     * توليد وحفظ Embedding للسجل الحالي
     * تُستخدم في Model Events (مثل saved)
     */
    public function generateAndSaveEmbedding(): void
    {
        $text = $this->title . ' ' . ($this->content ?? '');
        $embedding = self::generateEmbedding($text);

        if ($embedding) {
            $this->update(['embedding' => $embedding]);
        }
    }
}
