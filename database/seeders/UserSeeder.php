<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'syndicate@example.com'], // المفتاح الفريد — يمنع التكرار
            [
                'role_id'          => Role::where('slug', RoleSlug::Syndicate->value)->firstOrFail()->id,
                'password'         => Hash::make('password'),
                'full_name'        => 'Syndicate Admin',
                'is_active'        => true,
                'address'          => null,
                'lat'              => null,
                'lng'              => null,
                'experience_years' => null,
                'bio'              => null,
                'city'             => null,
            ]
        );
    }
}
