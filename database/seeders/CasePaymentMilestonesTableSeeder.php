<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CasePaymentMilestonesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('case_payment_milestones')->delete();
        
        \DB::table('case_payment_milestones')->insert(array (
            0 => 
            array (
                'id' => '2a9009ef-419e-4463-89aa-aa3428b18f9c',
                'title' => 'دفعة بدء العمل',
                'amount' => '5000.00',
                'due_date' => '2026-11-01',
                'status' => 'pending',
                'case_id' => 'd600b87e-11a3-4ea6-8a38-839934d508a8',
                'created_at' => '2026-08-22 10:28:59',
                'updated_at' => '2026-08-22 10:28:59',
            ),
            1 => 
            array (
                'id' => '50400886-f9e9-4c68-bfdc-aa3f2b4eff28',
                'title' => 'دفعة بدء العمل',
                'amount' => '5000.00',
                'due_date' => '2026-11-01',
                'status' => 'pending',
                'case_id' => '35ff2e3e-d50e-4da7-bd41-88bee8444478',
                'created_at' => '2026-08-22 10:30:29',
                'updated_at' => '2026-08-22 10:30:29',
            ),
            2 => 
            array (
                'id' => '5c13cb91-4f26-4210-a654-ee309e983c10',
                'title' => 'دفعة بدء العمل',
                'amount' => '5000.00',
                'due_date' => '2026-11-01',
                'status' => 'pending',
                'case_id' => 'a5f9e634-452f-4160-b7ce-4bb4c6ede5e6',
                'created_at' => '2026-08-22 10:27:44',
                'updated_at' => '2026-08-22 10:27:44',
            ),
            3 => 
            array (
                'id' => 'aa252720-02de-44a2-8c0c-d5471f95be36',
                'title' => 'دفعة بدء العمل',
                'amount' => '5000.00',
                'due_date' => '2026-11-01',
                'status' => 'pending',
                'case_id' => 'f7fb31d9-2ee6-4fe9-a5b8-5857e36ec620',
                'created_at' => '2026-08-22 10:29:47',
                'updated_at' => '2026-08-22 10:29:47',
            ),
            4 => 
            array (
                'id' => 'cf47bf51-f66e-4b52-a71a-fb0790454866',
                'title' => 'دفعة بدء العمل',
                'amount' => '5000.00',
                'due_date' => '2026-11-01',
                'status' => 'pending',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'created_at' => '2026-08-22 10:31:36',
                'updated_at' => '2026-08-22 10:31:36',
            ),
        ));
        
        
    }
}