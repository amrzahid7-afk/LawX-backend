<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CaseModel;
use Illuminate\Support\Str;
use Override;

class Document extends Model
{
    protected $table = 'documents';
    protected $keyType = 'string';
    protected $increment = false;
    protected $fillable = [
        'id',
        'case_id',
        'file_path',
        'type',
        'uploaded_by',
    ];
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
}
