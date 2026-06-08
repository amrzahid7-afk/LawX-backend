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
        'case_number',
        'case_category',
        'title',
        'result',
        'price',
        'description',
        'importance_stars',
        'billing_type',
        'collection_percentage',
        'status',
        'financial_status',
        'progress_percentage',
        'court',
        'lead_lawyer_id'
    ];
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }
    public function sessions()
    {
        return $this->hasMany(Session::class, 'case_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'case_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'case_id');
    }
    // المحامي الرئيسي
    public function leadLawyer()
    {
        return $this->belongsTo(User::class, 'lead_lawyer_id');
    }
    // جميع الموكلين في القضية
    public function clients()
    {
        return $this->belongsToMany(User::class, 'case_clients', 'case_id', 'user_id')
            ->withPivot('is_primary');
    }

    // جميع الخصوم في القضية
    public function opponents()
    {
        return $this->belongsToMany(Opponent::class, 'case_opponents', 'case_id', 'opponent_id');
    }

    // الفريق المكلف بالقضية
    public function team()
    {
        return $this->belongsToMany(User::class, 'case_teams', 'case_id', 'user_id')
            ->withPivot('role_in_case');
    }
    public function paymentMilestones()
    {
        return $this->hasMany(Case_payment_milestone::class, 'case_id');
    }
}
