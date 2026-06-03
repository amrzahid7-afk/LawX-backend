<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legal_category extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'legal_categories';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'icon',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_legal_categories', 'category_id', 'user_id');
    }
}
