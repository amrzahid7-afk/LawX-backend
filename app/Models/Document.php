<?php

namespace App\Models;

use App\Models\CaseModel;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory, HasUuid;
    protected $table = 'documents';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'file_path',
        'signed_file_path',
        'type',
        'file_size',
        'case_id',
        'invoice_id',
        'legal_request_id',
        'proposal_id',
        'uploaded_by',
        'extracted_text',
        'ai_tags',
        'ai_summary',
        'has_summary',
        'has_versions',
        'version_number',
        'parent_id'
    ];
    protected $casts = [
        'ai_tags' => 'array',
        'has_summary' => 'boolean',
        'has_versions' => 'boolean',
    ];
    // Define Factory Method 
    protected static function newFactory()
    {
        return DocumentFactory::new();
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function legal_request()
    {
        return $this->belongsTo(Legal_request::class, 'legal_request_id');
    }
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }
    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }
}
