<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => '4da6e0fb-b4b6-4024-920b-096f1eb5db60',
                'full_name' => 'أبي',
                'email' => 'Aubi@gmail.co',
                'password' => '$2y$12$MNVpNtthQQbpuQiuUbSDHeUDDpigDJ5ZTobWG/A7qpLyNPMy/pgau',
                'role_id' => 2,
                'is_active' => 1,
                'address' => 'ساحة الأمويين',
                'city' => 'دمشق',
                'lat' => '12.32320',
                'lng' => '12.656566',
                'experience_years' => 26,
                'bio' => 'خبير في القضايا الجزائية',
                'phone' => NULL,
                'national_id' => '01010151526364',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787390003/lawx/avatars/uifnyzja9cbepxxumgdr.jpg',
                'created_at' => '2026-08-22 09:13:24',
                'updated_at' => '2026-08-22 09:13:24',
            ),
            1 => 
            array (
                'id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'full_name' => 'حازم',
                'email' => 'Hazem@gmail.com',
                'password' => '$2y$12$HoR7zR6WTlVAajmLVCxVa.F9Ag0KRJvUQCRfa3UmdNbSMiyWBX4Ye',
                'role_id' => 3,
                'is_active' => 1,
                'address' => 'دمشق , برزة , الشارع العام',
                'city' => NULL,
                'lat' => '12.32320',
                'lng' => '15.0321',
                'experience_years' => 0,
                'bio' => 'موظف دولة',
                'phone' => '6666666666',
                'national_id' => '84569712365478',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787390120/lawx/avatars/y398lpdwfyfrboplhvut.jpg',
                'created_at' => '2026-08-22 09:15:20',
                'updated_at' => '2026-08-22 09:15:20',
            ),
            2 => 
            array (
                'id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'full_name' => 'رامي',
                'email' => 'Rami@gmail.com',
                'password' => '$2y$12$xqCFT66reQbcPSuUyhGPveXFJLw8vxy8fIIwSi2APOac5cvbE7diK',
                'role_id' => 3,
                'is_active' => 1,
                'address' => 'دمشق , برزة , الشارع العام',
                'city' => NULL,
                'lat' => '12.32320',
                'lng' => '15.0321',
                'experience_years' => 0,
                'bio' => 'شركة صرافة',
                'phone' => '0966619581',
                'national_id' => '45697123654495',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787390339/lawx/avatars/p3ujaotqev4fsncsgolq.jpg',
                'created_at' => '2026-08-22 09:19:00',
                'updated_at' => '2026-08-22 09:19:00',
            ),
            3 => 
            array (
                'id' => 'bd69116a-1e9f-47fb-8cfe-ac93549c5202',
                'full_name' => 'علي',
                'email' => 'Ali@gmail.co',
                'password' => '$2y$12$0h6r3nDUGzgYQ60NFgj8FupAyXPsUQW0sSVTM1dFni/C5aJYgqDYG',
                'role_id' => 2,
                'is_active' => 1,
                'address' => 'المرجة , يرج الفيحاء',
                'city' => 'دمشق',
                'lat' => '12.32320',
                'lng' => '12.656566',
                'experience_years' => 26,
                'bio' => 'خبير في دعاوى الأحوال الشخصية',
                'phone' => NULL,
                'national_id' => '01010151526361',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787388449/lawx/avatars/hjjbu2zrexpmcdvqhivw.jpg',
                'created_at' => '2026-08-22 08:47:29',
                'updated_at' => '2026-08-22 08:47:29',
            ),
            4 => 
            array (
                'id' => 'bf6d0732-a679-406a-b349-682ec8011d2b',
                'full_name' => 'رفعت',
                'email' => 'Refaat@gmail.co',
                'password' => '$2y$12$btvJtHGqb/qq9/bs55nnS.xfjDpanKx9W1UGCZY.5aMiQtFPz/miK',
                'role_id' => 2,
                'is_active' => 1,
                'address' => 'ساحة العباسيين',
                'city' => 'دمشق',
                'lat' => '12.32320',
                'lng' => '12.656566',
                'experience_years' => 26,
                'bio' => 'خبير في دعاوى الأحوال الشخصية',
                'phone' => NULL,
                'national_id' => '01010151526362',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787389217/lawx/avatars/w9nbcqtby1ulvrtlwb3x.jpg',
                'created_at' => '2026-08-22 09:00:18',
                'updated_at' => '2026-08-22 09:00:18',
            ),
            5 => 
            array (
                'id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'full_name' => 'احمد',
                'email' => 'Ahmad@gmail.com',
                'password' => '$2y$12$ptopGc4Z3YrZc9HnqQYhw.igXf12wI2Qu7PRO.usRAvQzi0uuQUA6',
                'role_id' => 3,
                'is_active' => 1,
                'address' => 'دمشق , برزة , الشارع العام',
                'city' => NULL,
                'lat' => '12.32320',
                'lng' => '15.0321',
                'experience_years' => 0,
                'bio' => 'شركة نقل',
                'phone' => '0966619580',
                'national_id' => '45697123654494',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787390259/lawx/avatars/lnb5voex7abasa2hv5in.jpg',
                'created_at' => '2026-08-22 09:17:39',
                'updated_at' => '2026-08-22 09:17:39',
            ),
            6 => 
            array (
                'id' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                'full_name' => 'Syndicate Admin',
                'email' => 'syndicate@example.com',
                'password' => '$2y$12$HN3Zt5/q/tR8L5w7qh5CeuB2Xv/eW3pbWk4HoxP6Zjq1T1B/wmbAG',
                'role_id' => 1,
                'is_active' => 1,
                'address' => 'ساحة المحافظة , فندق الشام',
                'city' => 'دمشق',
                'lat' => '2.0303',
                'lng' => '6.5151',
                'experience_years' => NULL,
                'bio' => NULL,
                'phone' => '2224251',
                'national_id' => NULL,
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787386707/lawx/avatars/yysczr6w023zploqcvud.jpg',
                'created_at' => '2026-08-21 15:28:31',
                'updated_at' => '2026-08-22 08:18:29',
            ),
            7 => 
            array (
                'id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'full_name' => 'ابراهيم',
                'email' => 'ibrahim@gmail.co',
                'password' => '$2y$12$zGJt5AZSC/lgSIpF.MnMounTg6HdCzPVhjcYFt/vJEGCNLrzIIBKO',
                'role_id' => 2,
                'is_active' => 1,
                'address' => 'البرامكة , المنطقة الحرة',
                'city' => 'دمشق',
                'lat' => '12.32320',
                'lng' => '12.656566',
                'experience_years' => 26,
                'bio' => 'خبير في دعاوى الأحوال الشخصية',
                'phone' => NULL,
                'national_id' => '88888888888887',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787387877/lawx/avatars/ezohdkixccymvc2wrvq0.jpg',
                'created_at' => '2026-08-22 08:37:58',
                'updated_at' => '2026-08-22 08:37:58',
            ),
            8 => 
            array (
                'id' => 'f7bf797d-8c4c-4e7b-871f-1e56ebc7c085',
                'full_name' => 'تميم',
                'email' => 'Tamim@gmail.co',
                'password' => '$2y$12$6TsHJp4ThJWDua1Qyocc3uWg166EQmh7KhCCCSZFgmz39//j/bRxa',
                'role_id' => 2,
                'is_active' => 1,
                'address' => 'جرمانا',
                'city' => 'دمشق',
                'lat' => '12.32320',
                'lng' => '12.656566',
                'experience_years' => 26,
                'bio' => 'خبير في دعاوى الأحوال الشخصية',
                'phone' => NULL,
                'national_id' => '01010151526360',
                'avatar_path' => 'https://res.cloudinary.com/mqrzbunw/image/upload/v1787388253/lawx/avatars/noxykh61ftxiigaovneb.jpg',
                'created_at' => '2026-08-22 08:44:14',
                'updated_at' => '2026-08-22 08:44:14',
            ),
        ));
        
        
    }
}