<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'invoices';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'case_id',
        'lawyer_id',
        'client_id',
        'total_amount',
        'status',
        'due_date',
        'items',
        'pdf_url',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
