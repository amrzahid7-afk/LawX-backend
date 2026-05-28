<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\Office;
use App\Traits\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'office_id',
        'role_id',
        'email',
        'password',
        'full_name',
        'avatar',
        'is_active',
        'activation_2FA'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function cases()
    {
        return $this->hasMany(CaseModel::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // relations methods 
    // public function office()
    // {
    //     return $this->belongsTo(Office::class);
    // }
    // public function employee()
    // {
    //     return $this->hasOne(Employee::class);
    // }
    // public function client()
    // {
    //     return $this->hasOne(Client::class);
    // }
    // public function salaryStructure()
    // {
    //     return $this->hasOne(SalaryStructure::class);
    // }
}
