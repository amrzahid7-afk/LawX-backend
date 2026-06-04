<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Office;
use App\Models\Invoice;
use App\Models\Casemillstone;
use App\Models\User;
use App\Models\Session;
use App\Models\Document;
use App\Models\Task;

class CaseModel extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'cases';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'client_id',
        'lawer_id'
    ];


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function lawer()
    {
        return $this->belongsTo(User::class, 'lawer_id');
    }
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // public function office()
    // {
    //     return $this->belongsTo(Office::class);
    // }
    // public function client()
    // {
    //     return $this->belongsTo(Client::class);
    // }
    // public function assignedEmployee()
    // {
    //     return $this->belongsTo(Employee::class, 'assigned_employee_id');
    // }
    // public function caseType()
    // {
    //     return $this->belongsTo(CaseType::class);
    // }
    // // public function template()
    // // {
    // //     return $this->belongsTo(WorkflowTemplate::class, 'template_id');
    // // }
    public function milestones()
    {
        return $this->hasMany(Casemillstone::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
