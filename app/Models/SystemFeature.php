<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SystemFeature extends Model
{
    use HasFactory, HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'name', 'description', 'is_active'];
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_features', 'feature_id', 'plan_id');
    }
}
