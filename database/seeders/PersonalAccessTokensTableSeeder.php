<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonalAccessTokensTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('personal_access_tokens')->delete();
        
        \DB::table('personal_access_tokens')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'd43c0bb0-2f26-4ad7-9144-2fa4f518d3ae',
                'name' => 'api-token',
                'token' => 'a116693d4292a02d8ed74ff9bf61660d78ddd8879b9737485aee2e71855ba3b4',
                'abilities' => '["*"]',
                'last_used_at' => '2026-08-22 08:18:23',
                'expires_at' => NULL,
                'created_at' => '2026-08-21 22:43:06',
                'updated_at' => '2026-08-22 08:18:23',
            ),
            1 => 
            array (
                'id' => 2,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'name' => 'LawyerToken',
                'token' => '4e258fd8ec73951d3ef684add8b9f585e8f35965311131c16915dcf2da8fcd34',
                'abilities' => '["*"]',
                'last_used_at' => '2026-08-23 00:29:34',
                'expires_at' => NULL,
                'created_at' => '2026-08-22 08:37:58',
                'updated_at' => '2026-08-23 00:29:34',
            ),
            2 => 
            array (
                'id' => 3,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'f7bf797d-8c4c-4e7b-871f-1e56ebc7c085',
                'name' => 'LawyerToken',
                'token' => '21a50c6dc9a0377ffc2af59c095e85e6986e7523731264c7c90abbd9e85be2cf',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
                'created_at' => '2026-08-22 08:44:14',
                'updated_at' => '2026-08-22 08:44:14',
            ),
            3 => 
            array (
                'id' => 4,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'bd69116a-1e9f-47fb-8cfe-ac93549c5202',
                'name' => 'LawyerToken',
                'token' => 'efbeb3123f0b4ff7833786bea813b8d91693e6816d7a771f3b9d100f4f84133a',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
                'created_at' => '2026-08-22 08:47:29',
                'updated_at' => '2026-08-22 08:47:29',
            ),
            4 => 
            array (
                'id' => 5,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'bf6d0732-a679-406a-b349-682ec8011d2b',
                'name' => 'LawyerToken',
                'token' => '8ece2d97b4d43e3220dca1019f1535f7feacd83b34bf95f18680582df4558117',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
                'created_at' => '2026-08-22 09:00:18',
                'updated_at' => '2026-08-22 09:00:18',
            ),
            5 => 
            array (
                'id' => 6,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => '4da6e0fb-b4b6-4024-920b-096f1eb5db60',
                'name' => 'LawyerToken',
                'token' => '9350588ec8ebeb42ae2527a5956e4ca689a9cd8c7276784f659c06722d656f56',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
                'created_at' => '2026-08-22 09:13:24',
                'updated_at' => '2026-08-22 09:13:24',
            ),
            6 => 
            array (
                'id' => 7,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'name' => 'ClientToken',
                'token' => 'b2d9a4dccfec5170f573de287e95c473f467fdc353a0bce81b9a51a1fc9a325e',
                'abilities' => '["*"]',
                'last_used_at' => '2026-08-23 00:31:18',
                'expires_at' => NULL,
                'created_at' => '2026-08-22 09:15:20',
                'updated_at' => '2026-08-23 00:31:18',
            ),
            7 => 
            array (
                'id' => 8,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'name' => 'ClientToken',
                'token' => '9717e4923641a73c01c2a8d90feff845adb33886687a618319e9f6a174631121',
                'abilities' => '["*"]',
                'last_used_at' => '2026-08-22 09:33:34',
                'expires_at' => NULL,
                'created_at' => '2026-08-22 09:17:39',
                'updated_at' => '2026-08-22 09:33:34',
            ),
            8 => 
            array (
                'id' => 9,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'name' => 'ClientToken',
                'token' => '75b0e9242f779cb311f24c0fec01f22d03ec3d9241a093a3b74759233763528d',
                'abilities' => '["*"]',
                'last_used_at' => '2026-08-22 09:46:59',
                'expires_at' => NULL,
                'created_at' => '2026-08-22 09:19:00',
                'updated_at' => '2026-08-22 09:46:59',
            ),
        ));
        
        
    }
}