<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SubscriptionPayment extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'invoice_id', 'amount', 'payment_method'];

    public function invoice()
    {
        return $this->belongsTo(OwnerInvoice::class, 'invoice_id');
    }
}
