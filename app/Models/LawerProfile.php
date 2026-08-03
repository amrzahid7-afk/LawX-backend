<?php

namespace App\Models;

use App\Models\OfficeInvitation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

// app/Models/LayerProfile.php
class LawerProfile extends Model
{
    use HasUuids; //, SoftDeletes;
    protected $keyType = 'string';
    protected $table = 'lawyer_profiles'; /// lawyer_profiles
    public $incrementing = false;
    protected $fillable = [
        'full_name',
        'national_id',
        'birth_date',
        'birth_place',
        'father_name',
        'mother_name',
        'mother_family_name',
        'civil_registry_number',
        'syndicate_card_number',
        'syndicate_branch',
        'lawyer_state',
        'is_suspended',
        'is_claimed',
        'claimed_at',
        'supervised_by_lawyer_id',
        'employed_in_office_id',
        'base_salary',
        'created_by',
        'user_id',
    ];

    protected $casts = [
        'is_claimed' => 'boolean',
        'is_suspended' => 'boolean',
        'trainee_approved_independent_at' => 'datetime',
        'claimed_at' => 'datetime',
        // 'national_Id_data' => 'array',
    ];
    public function employedOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'employed_in_office_id');
    }
    public function ownedOffice(): HasOne
    {
        return $this->hasOne(Office::class, 'owner_lawyer_profile_id');
    }
    public function officeInvitations(): HasMany
    {
        return $this->hasMany(OfficeInvitation::class, 'invited_lawyer_profile_id');
    }

    public function employedOffices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'office_invitations', 'invited_lawyer_profile_id', 'office_id')
            ->wherePivot('status', 'accepted')
            ->withPivot('status', 'created_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(LawerProfile::class, 'supervised_by_lawyer_id');
    }

    public function isTrainee(): bool
    {
        return $this->lawyer_state === 'trainee';
    }
}
