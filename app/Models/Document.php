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
    protected $increment = false;
    protected $fillable = [
        'id',
        'case_id',
        'file_path',
        'type',
        'uploaded_by',
        'legal_request_id',
        'proposal_id',
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
}
