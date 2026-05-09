<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\SystemFeature;
use App\Traits\HasUuid;

class Plan extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'name', 'price'];

    public function features()
    {
        return $this->belongsToMany(SystemFeature::class, 'plan_features', 'plan_id', 'feature_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
