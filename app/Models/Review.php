<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'reviews';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'lawyer_id',
        'client_id',
        'case_id',
        'rating',
        'comment'
    ];
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
}
