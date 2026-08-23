<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CaseClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('case_clients')->delete();
        
        \DB::table('case_clients')->insert(array (
            0 => 
            array (
                'id' => '05d68185-df30-4116-899a-2e6d69d9d44a',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'is_primary' => 0,
            ),
            1 => 
            array (
                'id' => '0e71755a-71ad-43f7-9a31-51051c9a9ec7',
                'case_id' => 'f7fb31d9-2ee6-4fe9-a5b8-5857e36ec620',
                'user_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'is_primary' => 1,
            ),
            2 => 
            array (
                'id' => '570ea70f-932d-4cd6-b143-437ef34db2ee',
                'case_id' => 'd600b87e-11a3-4ea6-8a38-839934d508a8',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'is_primary' => 0,
            ),
            3 => 
            array (
                'id' => '5b640ee0-4a5a-4855-82ca-4d787a9e5042',
                'case_id' => '35ff2e3e-d50e-4da7-bd41-88bee8444478',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'is_primary' => 0,
            ),
            4 => 
            array (
                'id' => '63ee70a1-62a7-4d06-92c0-0a570db97a39',
                'case_id' => 'a5f9e634-452f-4160-b7ce-4bb4c6ede5e6',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'is_primary' => 0,
            ),
            5 => 
            array (
                'id' => '6a1d035f-45c0-4286-8239-1944c0b87f26',
                'case_id' => 'f7fb31d9-2ee6-4fe9-a5b8-5857e36ec620',
                'user_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'is_primary' => 0,
            ),
            6 => 
            array (
                'id' => '8705ea1b-a696-42f2-9195-43635f8a8140',
                'case_id' => '35ff2e3e-d50e-4da7-bd41-88bee8444478',
                'user_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'is_primary' => 1,
            ),
            7 => 
            array (
                'id' => '9b201ce5-34c0-430d-b51f-2877f685e569',
                'case_id' => 'd600b87e-11a3-4ea6-8a38-839934d508a8',
                'user_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'is_primary' => 1,
            ),
            8 => 
            array (
                'id' => 'a1bf2914-ee01-42e4-8499-e64a519ed5fa',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'user_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'is_primary' => 1,
            ),
            9 => 
            array (
                'id' => 'a79a70da-f85a-4d8c-ac4b-16dc0ef418cc',
                'case_id' => 'a5f9e634-452f-4160-b7ce-4bb4c6ede5e6',
                'user_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'is_primary' => 1,
            ),
        ));
        
        
    }
}