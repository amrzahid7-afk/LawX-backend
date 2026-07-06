<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OfficeInvitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'office_id',
        'invited_lawyer_profile_id',
        'status',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function invitedLawyer(): BelongsTo
    {
        return $this->belongsTo(LawerProfile::class, 'invited_lawyer_profile_id');
    }

    public function accept(): void
    {
        $this->update(['status' => 'accepted']);

        $this->invitedLawyer->update([
            'employed_office_id' => $this->office_id,
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
