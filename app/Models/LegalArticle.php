<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalArticle extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'office_id', 'title', 'article_number', 'content'];

    public function embeddings()
    {
        return $this->hasMany(LegalEmbedding::class);
    }
}
