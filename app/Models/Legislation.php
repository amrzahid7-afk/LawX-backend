<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Legal_category;

class Legislation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'legislations';

    protected $fillable = [
        'id',
        'title',
        'content',
        'law_number',
        'category_id',
        'status',
        'source',
        // 'country',
        'effective_date',
        'embedding',
        'related_legislation_ids',
        'articles',
        'book',
        'part',
        'chapter',
        'parent_id',
    ];

    protected $casts = [
        'effective_date'          => 'date',
        'embedding'               => 'array',
        'related_legislation_ids' => 'array',
        'articles'                => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Legal_category::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Legislation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Legislation::class, 'parent_id');
    }

    /**
     * Calculate Cosine Similarity between two vector arrays for MySQL.
     */
    public static function calculateCosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $count = min(count($vecA), count($vecB));
        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
