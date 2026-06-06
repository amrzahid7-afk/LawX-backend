<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $table = 'tasks';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'case_id',
        'title',
        'description',
        'due_date',
        'assigned_to',
        'status'
    ];
    protected $casts = [
        'due_date' => 'datetime', // تأكد من وجود هذا السطر
        'created_at' => 'datetime', // تأكد من وجود هذا السطر
        'status' => 'string'

        // ... باقي الحقول الأخرى
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
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
