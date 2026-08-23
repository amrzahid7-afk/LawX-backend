<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('invoices')->delete();
        
        \DB::table('invoices')->insert(array (
            0 => 
            array (
                'id' => '0bfb9b4d-3f98-4f82-887d-51a6ee3efc82',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'client_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'total_amount' => '6000.00',
                'status' => 'unpaid',
                'due_date' => '2026-09-25',
                'type' => 'For_case',
                'stripe_checkout_session_id' => NULL,
                'payment_gateway' => 'stripe',
                'items' => '{"1": {"title": "دفعة أتعاب", "amount": "6000", "description": "دفعة أتعاب مقدمة"}}',
                'pdf_url' => NULL,
                'created_at' => '2026-08-22 21:04:10',
                'updated_at' => '2026-08-22 21:04:10',
            ),
            1 => 
            array (
                'id' => '841f5ed3-19e5-43c3-8fbb-8f0322ed826e',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'client_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'total_amount' => '8000.00',
                'status' => 'unpaid',
                'due_date' => '2026-08-25',
                'type' => 'For_case',
                'stripe_checkout_session_id' => NULL,
                'payment_gateway' => 'stripe',
                'items' => '[{"title": "رسوم توكيل", "amount": "6000", "description": "رسوم الوكالة للمحامي"}, {"title": "دفعة أتعاب", "amount": "6000", "description": "دفعة أتعاب مقدمة"}]',
                'pdf_url' => NULL,
                'created_at' => '2026-08-22 21:01:32',
                'updated_at' => '2026-08-22 21:01:32',
            ),
            2 => 
            array (
                'id' => 'fe2d623d-824e-4932-8048-618663644e28',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'client_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'total_amount' => '6000.00',
                'status' => 'unpaid',
                'due_date' => '2026-09-30',
                'type' => 'For_case',
                'stripe_checkout_session_id' => NULL,
                'payment_gateway' => 'stripe',
                'items' => '{"1": {"title": "دفعة أتعاب", "amount": "6000", "description": "دفعة أتعاب مقدمة"}}',
                'pdf_url' => NULL,
                'created_at' => '2026-08-22 21:04:20',
                'updated_at' => '2026-08-22 21:04:20',
            ),
        ));
        
        
    }
}