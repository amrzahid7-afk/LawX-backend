<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin seeder
        $admin =  User::create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'admin',
            'is_active' => true
        ]);
        // client seeder
        $admin =  User::create([
            'role_id' => Role::where('name', 'client')->first()->id,
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'client',
            'is_active' => true
        ]);
        // lawer seeder
        $admin =  User::create([
            'role_id' => Role::where('name', 'lawer')->first()->id,
            'email' => 'lawer@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'lawer',
            'is_active' => true
        ]);
    }
}
