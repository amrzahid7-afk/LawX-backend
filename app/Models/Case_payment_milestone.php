<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Case_payment_milestone extends Model
{
    //  case_payment_milestones
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'case_payment_milestones';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'case_id',
        'title',
        'amount',
        'due_date',
        'status'
    ];
    public function case()
    {
        return $this->belongsTo(\App\Models\CaseModel::class, 'case_id');
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
