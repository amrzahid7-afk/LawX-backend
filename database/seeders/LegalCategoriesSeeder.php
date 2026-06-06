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
            ['name' => 'قضايا الأحوال الشخصية والأسرة', 'icon' => '👨‍👩‍👧'],
            ['name' => 'قضايا الشركات والتجارة', 'icon' => '📊'],
            ['name' => 'قضايا العقارات والأملاك', 'icon' => '🏠'],
            ['name' => 'قضايا العمل والعمالة', 'icon' => '👷'],
            ['name' => 'قضايا الجرائم', 'icon' => '⚖️'],
            ['name' => 'قضايا الضرائب والجمارك', 'icon' => '🧾'],
            ['name' => 'قضايا التأمين', 'icon' => '🛡️'],
            ['name' => 'الملكية الفكرية', 'icon' => '💡'],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $data) {
            $category = Legal_category::create([
                'id' => Str::uuid(),
                'name' => $data['name'],
                'icon' => $data['icon'],
            ]);
            $createdCategories[] = $category;
        }

        // 2. جلب المستخدمين الحقيقيين الذين أنشأناهم سابقاً
        // هؤلاء المستخدمين يجب أن يكونوا موجودين في قاعدة البيانات (من Seeder السابق)
        $lawyer = User::where('email', 'lawyer@example.com')->first();
        $admin  = User::where('email', 'admin@example.com')->first();

        // 3. ربط التخصصات بالمحامي (مثلاً المحامي خبير في الأسرة والتجارة)
        if ($lawyer) {
            $lawyer->legalCategories()->attach([
                (string)  $createdCategories[0]->id => ['id' => Str::uuid()->toString()], // الأسرة
                (string)   $createdCategories[1]->id => ['id' => Str::uuid()->toString()], // التجارة
            ]);
        }

        // 4. ربط التخصصات بالأدمن (مثلاً المكتب يغطي معظم التخصصات)
        if ($admin) {
            $admin->legalCategories()->attach([
                (string) $createdCategories[2]->id => ['id' => Str::uuid()->toString()], // العقارات
                (string) $createdCategories[4]->id => ['id' => Str::uuid()->toString()], // الجرائم
                (string) $createdCategories[6]->id => ['id' => Str::uuid()->toString()], // التأمين
            ]);
        }
    }
}
