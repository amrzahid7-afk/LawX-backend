<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\ProposalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Legal_request;

class Proposal extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'proposals';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'price',
        'estimated_days',
        'message_for_client',
        'valid_until',
        'payment_terms',
        'status',
        'price_currency',
        'request_id',
        'lawer_id',
    ];
    // Define Factory Method
    protected static function newFactory()
    {
        return ProposalFactory::new();
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'assigned_lawyer_id');
    }
    public function request()
    {
        return $this->belongsTo(Legal_request::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
