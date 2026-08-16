<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\LegalRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legal_request extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    protected $table = 'legal_requests';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title_request',
        'description_request',
        'location_request',
        'budget_min',
        'budget_max',
        'type',
        'status',
        'created_at',
        'client_id',
        'assigned_lawyer_id',
    ];
    // Define Factory Method 
    protected static function newFactory()
    {
        return LegalRequestFactory::new();
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'assigned_lawyer_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
