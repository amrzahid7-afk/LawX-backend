<?php
// app/Models/SmartTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTemplateVariables;

class SmartTemplate extends Model
{
    use HasUuids, HasTemplateVariables;

    protected $table = 'smart_templates';

    protected $fillable = [
        'title',
        'file_id',
        'variables_json',
        'owner_id',
        'scope',
        'category',
    ];

    protected $casts = [
        'variables_json' => 'array', // تحويل JSON إلى Array تلقائياً
    ];

    /**
     * الملف الأصلي للقالب
     */
    public function file()
    {
        return $this->belongsTo(Document::class, 'file_id');
    }

    /**
     * المحامي المالك للقالب
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * الملفات المولدة من هذا القالب
     */
    public function generatedFiles()
    {
        return $this->hasMany(GeneratedTemplate::class, 'template_id');
    }

    // ===== Scopes =====

    /**
     * القوالب العامة (للمكتب)
     */
    public function scopePublic($query)
    {
        return $query->where('scope', 'public');
    }

    /**
     * القوالب الخاصة بمحامي معين
     */
    public function scopePrivateOf($query, $ownerId)
    {
        return $query->where('scope', 'private')->where('owner_id', $ownerId);
    }

    /**
     * القوالب المتاحة لمحامي (العام + الخاص به)
     */
    public function scopeAccessibleBy($query, $ownerId)
    {
        return $query->where(function ($q) use ($ownerId) {
            $q->where('scope', 'public')
                ->orWhere(function ($q2) use ($ownerId) {
                    $q2->where('scope', 'private')->where('owner_id', $ownerId);
                });
        });
    }
}
