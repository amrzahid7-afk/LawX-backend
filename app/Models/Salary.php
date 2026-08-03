<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasUuid;

    protected $table = 'salaries';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "id",
        'lawyer_id',
        'month',                    // مضافة
        'year',                     // مضافة
        'base_salary',
        'total_approved_expenses',  // مضافة
        'net_salary',               // مضافة
        'status',
        'invoice_id',               // مضافة
        'payment_date'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_approved_expenses' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
