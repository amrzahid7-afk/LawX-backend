<?php

namespace App\Models;

use App\Models\OfficeInvitation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Office extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'owner_lawyer_profile_id',
        'status',
        // 'reviewed_by',
        // 'reviewed_at',
        // 'rejection_reason',
    ];

    protected $casts = [
        // 'reviewed_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(LawerProfile::class, 'owner_lawyer_profile_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }


    public function invitations(): HasMany
    {
        return $this->hasMany(OfficeInvitation::class);
    }

    public function employees()
    {
        return $this->belongsToMany(
            LawerProfile::class,
            'office_invitations', // اسم الجدول الوسيط
            'office_id',          // المفتاح الأجنبي في جدول الوسيط الخاص بـ Office
            'invited_lawyer_profile_id' // المفتاح الأجنبي في جدول الوسيط الخاص بـ Lawyer
        )->withPivot('status', 'invited_by', 'responded_at'); // لإحضار بيانات إضافية من الجدول الوسيط إذا لزم الأمر
    }

    // public function isApproved(): bool
    // {
    //     return $this->status === 'approved';
    // }

    // public function isPending(): bool
    // {
    //     return $this->status === 'pending';
    // }
}
