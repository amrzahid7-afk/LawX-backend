<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Session extends Model
{
    protected $table = 'sessions';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        "id",
        'case_id',
        'date',
        'location',
        'nots',
        'result',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
}
