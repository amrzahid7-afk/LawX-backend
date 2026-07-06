<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'slug', 'label_ar'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
