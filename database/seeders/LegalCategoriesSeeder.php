<?php

namespace Database\Seeders;

use App\Models\Legal_category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            ['name' => 'مدني', 'icon' => '👨‍👩‍👧'],
            ['name' => 'محامي مصرفي ومالي', 'icon' => '👨‍👩‍👧'],
            ['name' => 'محامي عمالي', 'icon' => '👨‍👩‍👧'],
            ['name' => 'شرعي (أحوال شخصية)', 'icon' => '📊'],
            ['name' => 'جزائي (جنائي)', 'icon' => '🏠'],
            ['name' => 'تجاري', 'icon' => '👷'],
            ['name' => 'إداري', 'icon' => '⚖️'],
            ['name' => 'عسكري', 'icon' => '🧾'],
            ['name' => 'محامي دولي وتحكيم', 'icon' => '🛡️'],
            ['name' => 'محامي ملكية فكرية', 'icon' => '💡'],
        ];

        foreach ($categoriesData as $data) {
            Legal_category::updateOrCreate(
                ['name' => $data['name']], // المفتاح الفريد — يمنع التكرار
                ['icon' => $data['icon']]
            );
        }
    }
}
