<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SignatureRequestsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('signature_requests')->delete();
        
        \DB::table('signature_requests')->insert(array (
            0 => 
            array (
                'id' => '01a02b77-70a3-7291-94f3-ca129a25722b',
                'lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'document_id' => 'e68c390f-b2ce-4f4a-bd84-4316636e3337',
                'envelope_id' => 'bc80298e-bf48-8280-80d1-5c1d65881636',
                'status' => 'in_progress',
                'created_at' => '2026-08-22 21:54:03',
                'updated_at' => '2026-08-22 21:54:14',
            ),
        ));
        
        
    }
}