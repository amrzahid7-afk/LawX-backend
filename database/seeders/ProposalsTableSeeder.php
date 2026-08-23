<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProposalsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('proposals')->delete();
        
        \DB::table('proposals')->insert(array (
            0 => 
            array (
                'id' => '06144483-ffef-41d1-b9ee-f79865badb38',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => 'f9c5f085-61ac-4736-8ca7-f11874c7cb16',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:14:39',
                'updated_at' => '2026-08-22 10:14:39',
            ),
            1 => 
            array (
                'id' => '0aa81b67-f2d2-45d5-b5b4-6c8da9145fae',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => '06f5af97-8867-41b3-a524-63850bfe7bbc',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 09:52:20',
                'updated_at' => '2026-08-22 09:52:20',
            ),
            2 => 
            array (
                'id' => '1f364714-36e0-4729-bee7-742d9eaf6983',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => 'd1aa47d4-549c-45a9-9b50-36b310099b43',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:04:06',
                'updated_at' => '2026-08-22 10:04:06',
            ),
            3 => 
            array (
                'id' => '3e82deb1-e3db-43cc-aedd-207f4e6efd22',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => 'ab840341-50d0-4161-b0bd-b11de6b3f58a',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:03:50',
                'updated_at' => '2026-08-22 10:03:50',
            ),
            4 => 
            array (
                'id' => '4975e8f9-25ff-476e-b15d-539342a5f662',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => '06f5af97-8867-41b3-a524-63850bfe7bbc',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:09:45',
                'updated_at' => '2026-08-22 10:09:45',
            ),
            5 => 
            array (
                'id' => '7c5d31e5-8d75-4c53-b981-fba60bc1927c',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => '9ce6fabe-1d7a-4057-87b6-7ba18418b733',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:03:24',
                'updated_at' => '2026-08-22 10:03:24',
            ),
            6 => 
            array (
                'id' => 'b6a25f7d-7016-4834-830c-3b1e6ff2ea3c',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => 'f9c5f085-61ac-4736-8ca7-f11874c7cb16',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:13:38',
                'updated_at' => '2026-08-22 10:13:38',
            ),
            7 => 
            array (
                'id' => 'cf402f8b-c66b-4193-a598-4fd2cca64936',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => '82209c88-62b8-484f-b0fc-5b2ed52b76fb',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:03:04',
                'updated_at' => '2026-08-22 10:03:04',
            ),
            8 => 
            array (
                'id' => 'dbffe769-49a4-4da2-bfb6-9e37789917e5',
                'price' => '16.00',
                'estimated_days' => '15',
                'message_for_client' => 'احتمال نجاح الدعوى ضعيف',
                'valid_until' => '2026-12-09',
                'payment_terms' => 'دفعة مقدمة من التعاب شاملة أجور الوكالة',
                'status' => 'pending',
                'price_currency' => 'syp',
                'request_id' => '46c42f57-ec9d-4baa-b0fe-62831423eb7b',
                'lawer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 10:14:58',
                'updated_at' => '2026-08-22 10:14:58',
            ),
        ));
        
        
    }
}