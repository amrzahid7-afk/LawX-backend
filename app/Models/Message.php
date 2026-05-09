<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false; // فقط sent_at موجود

    protected $fillable = [
        'id',
        'case_id',
        'sender_id',
        'content',
        'attachment_url',
        'is_read',
        'sent_at'
    ];
}
