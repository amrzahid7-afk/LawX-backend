<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GeneratedTemplate extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'generated_templates';
    public $incrementing = false;

    protected $fillable = [
        'template_id',
        'file_id',
        'case_id',
        'generated_by',
        'used_values'
    ];
    protected $casts = [
        'used_values' => 'array',
    ];
    public function template()
    {
        return $this->belongsTo(SmartTemplate::class, 'template_id');
    }

    public function file()
    {
        return $this->belongsTo(Document::class, 'file_id');
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
    public function GeneratedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
