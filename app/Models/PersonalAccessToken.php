<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // أضف هذا السطر للتأكيد على أن الموديل يتعامل مع الـ UUID الخاص بالـ User
    protected $table = 'personal_access_tokens';

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'tokenable_id' => 'string', // حل مشكلة الـ UUID الخاص بالمستخدم
    ];
}
