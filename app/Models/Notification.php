<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false; // فقط created_at موجود

    protected $fillable = ['id', 'user_id', 'title', 'message', 'type', 'read_at', 'created_at', 'related_entity_id'];
    public function markAsRead()
    {
        return $this->update([
            'read_at' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
