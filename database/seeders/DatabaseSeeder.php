<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // calling seeders 
        $this->call([
            RoleSeeder::class,
            UserSeeeder::class,
            LegalRequestSeeder::class,
            ProposalSeeder::class
        ]);
    }
}
