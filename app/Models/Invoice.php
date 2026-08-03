<?php

namespace App\Models;

use App\Models\Payment;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'invoices';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'case_id',
        'lawyer_id',
        'client_id',
        'total_amount',
        'status',
        'type',
        'due_date',
        'items',
        'stripe_checkout_session_id',
        'payment_gateway'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'invoice_id');
    }
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function salary()
    {
        return $this->hasOne(Salary::class, 'invoice_id');
    }
}
