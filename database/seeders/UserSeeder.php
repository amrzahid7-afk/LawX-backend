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
        User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('slug', RoleSlug::Syndicate->value)->firstOrFail()->id,
            'email' => 'syndicate@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Syndicate Admin',
            'is_active' => true,
            'address' => null,
            'lat' => null,
            'lng' => null,
            'experience_years' => null,
            'bio' => null,
            'city' => null,
        ]);

        User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('slug', RoleSlug::Client->value)->firstOrFail()->id,
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Client User',
            'is_active' => true,
            'address' => 'الرياض، حي الملز',
            'lat' => '24.7136',
            'lng' => '46.6753',
            'experience_years' => null,
            'bio' => null,
            'city' => 'Riyadh',
        ]);

        User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('slug', RoleSlug::Lawyer->value)->firstOrFail()->id,
            'email' => 'lawyer@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Lawyer User',
            'is_active' => true,
            'address' => 'جدة، شارع التحلية',
            'lat' => '21.5433',
            'lng' => '39.1728',
            'experience_years' => 10,
            'bio' => 'محامي خبير في قضايا الشركات والأعمال مع خبرة تزيد عن 10 سنوات.',
            'city' => 'Jeddah',
        ]);
    }
}
