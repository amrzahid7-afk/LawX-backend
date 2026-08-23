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
            [
                'id' => 'b13d2d25-f4e2-46cc-95f6-ebbd461059e1',
                'name' => 'مدني',
                'icon' => '👨‍👩‍👧'
            ],
            [
                'id' => '8d09f7eb-1227-4afc-8db8-2fe0099c3361',

                'name' => 'محامي مصرفي ومالي',
                'icon' => '👨‍👩‍👧'
            ],
            [
                'id' => 'c8b2e58b-3e4a-4a8f-a324-781b5230d079',
                'name' => 'محامي عمالي',
                'icon' => '👨‍👩‍👧'
            ],
            [
                'id' => '97204b45-1777-44da-890f-1d4e690d2323',
                'name' => 'شرعي (أحوال شخصية)',
                'icon' => '📊'
            ],

            [
                'id' => '',
                'name' => 'جزائي (جنائي)',
                'icon' => '🏠'
            ],
            [
                'id' => '',
                'name' => 'تجاري',
                'icon' => '👷'
            ],
            [
                'id' => '',
                'name' => 'إداري',
                'icon' => '⚖️'
            ],
            [
                'id' => '',
                'name' => 'عسكري',
                'icon' => '🧾'
            ],
            [
                'id' => 'c2d57182-c926-4f1e-bad5-ba430372d8f3',
                'name' => 'محامي دولي وتحكيم',
                'icon' => '🛡️'
            ],
            [
                'id' => 'f1b86ee5-5151-4333-96f8-3d00797c3f42',
                'name' => 'محامي ملكية فكرية',
                'icon' => '💡'
            ],
        ];

        foreach ($categoriesData as $data) {
            Legal_category::updateOrCreate(
                ['id' => $data['id']],
                ['name' => $data['name']],
                ['icon' => $data['icon']]
            );
        }
    }
}
