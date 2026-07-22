<?php
// app/Models/Signer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signer extends Model
{
    use HasUuids, HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'signature_request_id',
        'user_id',
        'role',
        'is_seal',
        'anchor_text',
        'page_number',
        'docusign_recipient_id',
        'status',
        'signed_at',
        'x_position',
        'y_position',
        'width',
        'height',
        'sign_type',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'is_seal' => 'boolean'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class, 'signature_request_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
