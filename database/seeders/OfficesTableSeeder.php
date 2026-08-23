<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OfficesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('offices')->delete();
        
        \DB::table('offices')->insert(array (
            0 => 
            array (
                'id' => '01a0271a-6370-7283-9ed9-b529ae20a765',
                'name' => 'علي\'s Office',
                'owner_lawyer_profile_id' => '01a0271a-636d-7336-9e8c-fd22e46f78c1',
                'status' => 'suspended',
                'created_at' => '2026-08-22 01:33:55',
                'updated_at' => '2026-08-22 01:33:55',
            ),
            1 => 
            array (
                'id' => '01a0271a-aec3-7246-998d-c828076a995a',
                'name' => 'رفعت\'s Office',
                'owner_lawyer_profile_id' => '01a0271a-aec0-70de-b6b9-b63ae6b9a6fe',
                'status' => 'suspended',
                'created_at' => '2026-08-22 01:34:15',
                'updated_at' => '2026-08-22 01:34:15',
            ),
            2 => 
            array (
                'id' => '01a0271a-f8cf-706b-84fe-9d8a8a702e36',
                'name' => 'ابراهيم\'s Office',
                'owner_lawyer_profile_id' => '01a0271a-f8cc-7268-9c53-84c0b5ef1796',
                'status' => 'suspended',
                'created_at' => '2026-08-22 01:34:34',
                'updated_at' => '2026-08-22 01:34:34',
            ),
            3 => 
            array (
                'id' => '01a0271b-42c5-7323-a387-af56618882a1',
                'name' => 'تميم\'s Office',
                'owner_lawyer_profile_id' => '01a0271b-42c3-72b2-99a5-e38ff1b0d7fa',
                'status' => 'suspended',
                'created_at' => '2026-08-22 01:34:53',
                'updated_at' => '2026-08-22 01:34:53',
            ),
        ));
        
        
    }
}