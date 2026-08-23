<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OpponentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('opponents')->delete();
        
        \DB::table('opponents')->insert(array (
            0 => 
            array (
                'id' => '4f60579f-c98e-4e01-be86-89b3b6da55d2',
                'name' => 'حازم',
                'type' => 'individual',
                'national_id' => '59',
                'phone' => '0551234567',
                'email' => NULL,
                'address' => NULL,
                'created_at' => '2026-08-22 10:27:44',
                'updated_at' => '2026-08-22 10:27:44',
            ),
            1 => 
            array (
                'id' => 'd6ba3b3c-945d-4494-a860-d4fd302fb5c9',
                'name' => 'أحمد',
                'type' => 'individual',
                'national_id' => '55',
                'phone' => '055123456756',
                'email' => NULL,
                'address' => NULL,
                'created_at' => '2026-08-22 10:27:44',
                'updated_at' => '2026-08-22 10:27:44',
            ),
        ));
        
        
    }
}