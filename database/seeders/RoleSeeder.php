<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $roles = ['admin', 'lawer', 'client'];
    //     foreach ($roles as $role) {
    //         Role::firstOrCreate(['name' => $role]);
    //     }
    // }
    public function run(): void
    {
        $labels = [
            RoleSlug::Syndicate->value => 'النقابة',
            RoleSlug::Lawyer->value => 'المحامي',
            RoleSlug::Client->value => 'العميل',
        ];

        foreach (RoleSlug::cases() as $role) {
            Role::updateOrCreate(
                ['slug' => $role->value],
                ['label_ar' => $labels[$role->value]]
            );
        }
    }
}
