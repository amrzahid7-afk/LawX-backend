<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CasesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cases')->delete();
        
        \DB::table('cases')->insert(array (
            0 => 
            array (
                'id' => '35ff2e3e-d50e-4da7-bd41-88bee8444478',
                'case_number' => '803',
                'title' => 'قضية حضانة أطفال',
                'price' => 50000.0,
                'case_category' => 'مدني',
                'description' => 'حيازة حضانة الاطفال للأم',
                'result' => 'pending',
                'importance_stars' => 4,
                'billing_type' => 'installments',
                'collection_percentage' => NULL,
                'status' => 'open',
                'financial_status' => 'unpaid',
                'progress_percentage' => 0,
                'court' => 'المحكمة التجارية  دمشق',
                'lead_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'office_id' => NULL,
                'created_at' => '2026-08-22 10:30:29',
                'updated_at' => '2026-08-22 10:30:29',
            ),
            1 => 
            array (
                'id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'case_number' => '804',
                'title' => 'معاملة سجل تجاري',
                'price' => 50000.0,
                'case_category' => 'تجاري',
                'description' => 'استخراج سجل تجاري من غرفة تجارة دمشق',
                'result' => 'pending',
                'importance_stars' => 4,
                'billing_type' => 'installments',
                'collection_percentage' => NULL,
                'status' => 'close',
                'financial_status' => 'unpaid',
                'progress_percentage' => 0,
                'court' => 'المحكمة التجارية  دمشق',
                'lead_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'office_id' => NULL,
                'created_at' => '2026-08-22 10:31:36',
                'updated_at' => '2026-08-23 00:29:35',
            ),
            2 => 
            array (
                'id' => 'a5f9e634-452f-4160-b7ce-4bb4c6ede5e6',
                'case_number' => '800',
                'title' => 'قضية حصر ارث',
                'price' => 50000.0,
                'case_category' => 'مدني',
                'description' => 'حصر ارث رضائي قسمة رضائية',
                'result' => 'pending',
                'importance_stars' => 4,
                'billing_type' => 'installments',
                'collection_percentage' => NULL,
                'status' => 'open',
                'financial_status' => 'unpaid',
                'progress_percentage' => 0,
                'court' => 'المحكمة المدنية دمشق',
                'lead_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'office_id' => NULL,
                'created_at' => '2026-08-22 10:27:44',
                'updated_at' => '2026-08-22 10:27:44',
            ),
            3 => 
            array (
                'id' => 'd600b87e-11a3-4ea6-8a38-839934d508a8',
                'case_number' => '801',
                'title' => 'قضية تثبيت بيع',
                'price' => 50000.0,
                'case_category' => 'مدني',
                'description' => 'تثبيت بيع شقة سكنية',
                'result' => 'pending',
                'importance_stars' => 4,
                'billing_type' => 'installments',
                'collection_percentage' => NULL,
                'status' => 'open',
                'financial_status' => 'unpaid',
                'progress_percentage' => 0,
                'court' => 'المحكمة التجارية  دمشق',
                'lead_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'office_id' => NULL,
                'created_at' => '2026-08-22 10:28:59',
                'updated_at' => '2026-08-22 10:28:59',
            ),
            4 => 
            array (
                'id' => 'f7fb31d9-2ee6-4fe9-a5b8-5857e36ec620',
                'case_number' => '802',
                'title' => 'قضية تثبيت زواج',
                'price' => 50000.0,
                'case_category' => 'مدني',
                'description' => 'تثبيت زواج',
                'result' => 'pending',
                'importance_stars' => 4,
                'billing_type' => 'installments',
                'collection_percentage' => NULL,
                'status' => 'open',
                'financial_status' => 'unpaid',
                'progress_percentage' => 0,
                'court' => 'المحكمة التجارية  دمشق',
                'lead_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'office_id' => NULL,
                'created_at' => '2026-08-22 10:29:47',
                'updated_at' => '2026-08-22 10:29:47',
            ),
        ));
        
        
    }
}