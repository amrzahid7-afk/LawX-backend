<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create Roles in system 
        // $roles = ['productowner', 'officeadmin', 'trainee', 'lawyer', 'client'];
        $roles = ['client', 'lawer', 'admin', 'office_manager', 'independent_lawyer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
