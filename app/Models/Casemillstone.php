<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casemillstone extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'case_id', 'name', 'percentage', 'order_index', 'due_date', 'status'];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
