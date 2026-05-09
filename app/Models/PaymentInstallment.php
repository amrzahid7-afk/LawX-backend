<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'invoice_id',
        'milestone_id',
        'amount',
        'due_date',
        'status'
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
