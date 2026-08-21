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
        $this->call([
            RoleSeeder::class,              // أدوار (يدوي - مرجعي)
            LegalCategoriesSeeder::class,   // تخصصات (يدوي - مرجعي)
            UserSeeder::class,              // syndicate بس
        ]);
    }
}
