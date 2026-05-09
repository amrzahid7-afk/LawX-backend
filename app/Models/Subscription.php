<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Subscription extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'office_id', 'plan_id', 'start_date', 'end_date', 'status'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function invoices()
    {
        return $this->hasMany(OwnerInvoice::class, 'subscription_id');
    }
}
