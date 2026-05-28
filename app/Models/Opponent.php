<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opponent extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'opponents';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'type',
        'national_id',
        'phone',
        'email',
        'address'
    ];
    public function cases()
    {
        return $this->belongsToMany(\App\Models\CaseModel::class, 'case_opponents', 'opponent_id', 'case_id');
    }
}
