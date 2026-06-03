<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء حساب Admin
        $admin = User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('name', 'admin')->first()->id,
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'Admin User',
            'is_active' => true,
            // البيانات الإضافية (الـ Admin لا يحتاجها ولكن نضع null)
            'address' => null,
            'lat' => null,
            'lng' => null,
            'experience_years' => null,
            'bio' => null,
            'city' => null
        ]);

        // 2. إنشاء حساب Client
        $client = User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('name', 'client')->first()->id,
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'Client User',
            'is_active' => true,
            'address' => 'الرياض، حي الملز',
            'lat' => '24.7136',
            'lng' => '46.6753',
            'experience_years' => null, // العميل ليس لديه خبرة سنوية
            'bio' => null,
            'city' => 'Riyadh' // <--- اسم المدينة كنص
        ]);

        // 3. إنشاء حساب Lawyer (المحامي)
        $lawyer = User::create([
            'id' => Str::uuid(),
            'role_id' => Role::where('name', 'lawer')->first()->id,
            'email' => 'lawyer@example.com',
            'password' => bcrypt('password'),
            'full_name' => 'Lawyer User',
            'is_active' => true,
            'address' => 'جدة، شارع التحلية',
            'lat' => '21.5433',
            'lng' => '39.1728',
            'experience_years' => 10,
            'bio' => 'محامي خبير في قضايا الشركات والأعمال مع خبرة تزيد عن 10 سنوات في الدفاع عن العملاء في قضايا عالية الأهمية.',
            'city' => 'Jeddah' // <--- اسم المدينة كنص
        ]);
    }
}
