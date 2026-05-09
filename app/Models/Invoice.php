<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'case_id', 'total_amount', 'type', 'issue_date', 'is_paid'];

    protected $casts = ['is_paid' => 'boolean'];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }
}
