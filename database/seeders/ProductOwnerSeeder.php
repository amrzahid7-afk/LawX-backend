<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductOwnerSeeder extends Seeder
{

    public function run(): void
    {
        // // create User record
        // $user =  User::create([
        //     'office_id' => null,
        //     'role_id' => Role::where('name', 'productowner')->first()->id,
        //     'email' => 'productowner@example.com',
        //     'password' => bcrypt('password'),
        //     'full_name' => 'Product Owner',
        //     'is_active' => true
        // ]);
        // // create Super admin record 
        // SuperAdmin::create([
        //     'user_id' => $user->id,
        // ]);
    }
}
