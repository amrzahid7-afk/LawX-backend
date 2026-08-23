<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SessionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sessions')->delete();
        
        \DB::table('sessions')->insert(array (
            0 => 
            array (
                'id' => '505bcb42-2bd0-473c-af27-007458086b20',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'date' => '2026-11-16 01:30:00',
                'location' => 'المزة',
                'latitude' => '24.71355170',
                'longitude' => '46.67529570',
                'nots' => 'جلسة طرح الدعوى',
                'result' => NULL,
                'status' => 'pending',
                'created_at' => '2026-08-22 20:55:53',
                'updated_at' => '2026-08-22 20:55:53',
            ),
            1 => 
            array (
                'id' => 'd939e12f-04c4-467d-a5b7-98df6829341d',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'date' => '2026-12-16 01:30:00',
                'location' => 'المزة',
                'latitude' => '24.71355170',
                'longitude' => '46.67529570',
                'nots' => 'جلسة مداولة',
                'result' => NULL,
                'status' => 'pending',
                'created_at' => '2026-08-22 20:56:32',
                'updated_at' => '2026-08-22 20:56:32',
            ),
            2 => 
            array (
                'id' => 'eb76d49d-e8a8-43f6-a174-495b29d3c8f9',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'date' => '2026-12-16 01:30:00',
                'location' => 'المزة',
                'latitude' => '24.71355170',
                'longitude' => '46.67529570',
                'nots' => 'جلسة حكم',
                'result' => NULL,
                'status' => 'pending',
                'created_at' => '2026-08-22 20:57:36',
                'updated_at' => '2026-08-22 20:57:36',
            ),
        ));
        
        
    }
}