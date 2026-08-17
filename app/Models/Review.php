<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    // لأننا نستخدم UUID بدلاً من ID العادي
    public $incrementing = false;
    protected $keyType = 'string';

    // الحقول التي يُسمح بملئها عبر Mass Assignment
    protected $fillable = [
        'id',
        'lawyer_id',
        'client_id',
        'case_id',
        'professionalism_rating',
        'communication_rating',
        'result_rating',
        'rating',
        'comment',
        'is_hidden',
    ];

    // إخفاء الحقول عند تحويل الموديل لـ JSON (في الـ API)
    protected $hidden = [
        'updated_at',
        // يمكن إخفاء 'lawyer_id' و 'client_id' إذا كنت سترسل اسماء بدل الأرقام
    ];

    /**
     *Boot function من إنشاء UUID تلقائياً عند إنشاء سجل جديد
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($review) {
            if (empty($review->id)) {
                $review->id = Str::uuid()->toString();
            }
        });
    }

    // ================= العلاقات (Relationships) =================

    /**
     * التقييم يخص محامي واحد
     */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /**
     * التقييم يخص عميل واحد
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * التقييم يخص قضية واحدة
     * ملاحظة: غير LegalCase إلى اسم موديل القضايا لديك إذا كان مختلفاً (مثل CaseModel)
     */
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    // ================= Scopes (للتسهيل على الاستعلامات) =================

    /**
     * عرض التقييمات العلنية فقط (التي لم يقم المشرف بإخفائها)
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }
}
