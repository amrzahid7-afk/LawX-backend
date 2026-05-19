<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Legal_request;
use App\Models\User;
use Illuminate\Support\Str;

class LegalRequestSeeder extends Seeder
{
    public function run(): void
    {
        Legal_request::factory()->count(20)->create();
    }
}
