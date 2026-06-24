<?php
// app/Models/Legislation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Legal_category;
use App\Traits\SemanticSearch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Legislation extends Model
{
    use HasFactory, SemanticSearch, HasUuids;

    protected $table = 'legislations';

    protected $fillable = [
        'id',
        'title',
        'content',
        'law_number',
        'category_id',
        'status',
        'source',
        'effective_date',
        'embedding',
        'related_legislation_ids',
        'articles',
        'book',
        'part',
        'chapter',
        'country'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'embedding' => 'array',
        'related_legislation_ids' => 'array',
        'articles' => 'array',
    ];

    public function relatedLegislations()
    {
        return $this->hasMany(Legislation::class, 'id', 'related_legislation_ids');
    }

    public function category()
    {
        return $this->belongsTo(Legal_category::class, 'category_id');
    }

    protected static function booted()
    {
        static::saved(function ($legislation) {
            if ($legislation->content) {
                dispatch(new \App\Jobs\GenerateEmbeddingJob($legislation));
            }
        });
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use App\Models\Legal_category;
// use App\Traits\SemanticSearch;
// use Illuminate\Database\Eloquent\Concerns\HasUuids;

// class Legislation extends Model
// {
//     use HasFactory, SemanticSearch, HasUuids;
//     protected $table = 'legislations';
//     protected $fillable = [
//         'id',
//         'title',
//         'content',
//         'law_number',
//         'category_id',
//         'status',
//         'source',
//         'effective_date',
//         'embedding',
//         'level',
//         'parent_id',
//         'country'
//     ];

//     protected $casts = [
//         'effective_date' => 'date',
//         'embedding' => 'array', // تحويل JSON إلى Array
//     ];
//     // this realtion represent the structure of law like chapter - section - article - paragraph
//     public function parent()
//     {
//         return $this->belongsTo(Legislation::class, 'parent_id');
//     }
//     // this realtion represent the structure of law like chapter - section - article - paragraph
//     public function children()
//     {
//         return $this->hasMany(Legislation::class, 'parent_id');
//     }
//     public function category()
//     {
//         return $this->belongsTo(Legal_category::class);
//     }

//     // عند إنشاء أو تحديث القانون، توليد الـ Embedding تلقائياً
//     protected static function booted()
//     {
//         static::saved(function ($legislation) {
//             if ($legislation->content) {
//                 dispatch(new \App\Jobs\GenerateEmbeddingJob($legislation));
//             }
//         });
//     }
// }
