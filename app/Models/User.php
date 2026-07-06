<?php

namespace App\Models;

use App\Enums\RoleSlug;
use App\Models\LawyerProfile;
use App\Models\Task;
use App\Traits\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'role_id',
        'email',
        'address',
        'password',
        'full_name',
        'avatar',
        'is_active',
        'activation_2FA',
        'city',
        'lat',
        'lng',
        'experience_years',
        'bio',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(RoleSlug $role): bool
    {
        return $this->role?->slug === $role->value;
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'lead_lawyer_id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    public function legalCategories(): BelongsToMany
    {
        return $this->belongsToMany(Legal_category::class, 'user_legal_categories', 'user_id', 'category_id');
    }

    // علاقة 2: المستخدم (المحامي) يستقبل تقييمات من العملاء
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'lawyer_id');
    }

    // علاقة 3 (اختيارية): المستخدم (العميل) قام بكتابة تقييمات
    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id');
    }
    public function client_invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }
    public function lawyer_invoices()
    {
        return $this->hasMany(Invoice::class, 'lawyer_id');
    }

    public function lawyerProfile(): HasOne
    {
        return $this->hasOne(LawerProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isLawyer(): bool
    {
        return $this->role->name === 'lawer';
    }
}
