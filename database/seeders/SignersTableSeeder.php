<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SignersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('signers')->delete();
        
        \DB::table('signers')->insert(array (
            0 => 
            array (
                'id' => '01a02b77-70fc-713d-9530-58e7b81a56f9',
                'signature_request_id' => '01a02b77-70a3-7291-94f3-ca129a25722b',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'role' => 'client',
                'is_seal' => 0,
                'x_position' => 330,
                'y_position' => 740,
                'width' => 5,
                'height' => 5,
                'sign_type' => 'signature',
                'anchor_text' => NULL,
                'page_number' => 1,
                'docusign_recipient_id' => '1',
                'status' => 'pending',
                'signed_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => '01a02b77-710b-7208-a7d3-a3039c99fc5b',
                'signature_request_id' => '01a02b77-70a3-7291-94f3-ca129a25722b',
                'user_id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'role' => 'client',
                'is_seal' => 1,
                'x_position' => 90,
                'y_position' => 740,
                'width' => 5,
                'height' => 5,
                'sign_type' => 'seal',
                'anchor_text' => NULL,
                'page_number' => 1,
                'docusign_recipient_id' => '2',
                'status' => 'pending',
                'signed_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}