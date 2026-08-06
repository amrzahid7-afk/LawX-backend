<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalaryStructure;

class Employee extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'user_id', 'salary_structure_id', 'current_points', 'star_rating'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function salaryStructure()
    // {
    //     return $this->belongsTo(SalaryStructure::class);
    // }
}
