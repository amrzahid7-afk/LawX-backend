<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Case_team extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'case_teams';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'case_id',
        'user_id',
        'role_in_case'
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
