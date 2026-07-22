<?php
// app/Models/SignatureRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignatureRequest extends Model
{
    use HasUuids, HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'document_id',
        'lawyer_id',
        'envelope_id',
        'status'
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class, 'signature_request_id');
    }
}
