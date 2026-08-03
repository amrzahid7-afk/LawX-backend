<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'expenses';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'task_id',
        'amount',
        'description',
        'status',
        'expense_date',
    ];
    protected $casts = [
        'expense_date' => 'date',
    ];
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
