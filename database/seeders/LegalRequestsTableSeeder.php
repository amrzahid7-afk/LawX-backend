<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LegalRequestsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('legal_requests')->delete();
        
        \DB::table('legal_requests')->insert(array (
            0 => 
            array (
                'id' => '06f5af97-8867-41b3-a524-63850bfe7bbc',
                'title_request' => 'دعوى نفقة',
                'description_request' => 'دعوى نفقة',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:46:50',
            ),
            1 => 
            array (
                'id' => '377735d0-aa00-48ad-84ee-a9d4b292918d',
                'title_request' => 'دعوى تثبيت بيع',
                'description_request' => 'دعوى تثبيت بيع',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:44:36',
            ),
            2 => 
            array (
                'id' => '3ed93eec-e922-4215-a209-63956adcb193',
                'title_request' => 'تثبيت بيع',
                'description_request' => 'تثبيت بيع',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 09:34:08',
                'updated_at' => '2026-08-22 09:34:08',
            ),
            3 => 
            array (
                'id' => '46c42f57-ec9d-4baa-b0fe-62831423eb7b',
                'title_request' => 'دعوى تثبيت بيع',
                'description_request' => 'دعوى تثبيت بيع',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:45:14',
            ),
            4 => 
            array (
                'id' => '82209c88-62b8-484f-b0fc-5b2ed52b76fb',
                'title_request' => 'دعوى حصر ارث',
                'description_request' => 'دعوى حصر ارث',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:45:49',
            ),
            5 => 
            array (
                'id' => '9ce6fabe-1d7a-4057-87b6-7ba18418b733',
                'title_request' => 'استخراج رخصة سجل تجاري',
                'description_request' => 'سجل تجاري لشركة حوالات مالية',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '584d073b-a517-45ac-b79d-82f047e9ab3b',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 09:32:47',
                'updated_at' => '2026-08-22 09:32:47',
            ),
            6 => 
            array (
                'id' => 'ab840341-50d0-4161-b0bd-b11de6b3f58a',
                'title_request' => 'دعوى تثبيت زواج',
                'description_request' => 'دعوى تثبيت زواج',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:46:17',
            ),
            7 => 
            array (
                'id' => 'd1aa47d4-549c-45a9-9b50-36b310099b43',
                'title_request' => 'استخراج رخصة سجل تجاري',
                'description_request' => 'تثبيت بيع',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => 'd0ea53dd-78eb-4a8a-8b9c-86fc675c730c',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 09:33:34',
                'updated_at' => '2026-08-22 09:33:34',
            ),
            8 => 
            array (
                'id' => 'f9c5f085-61ac-4736-8ca7-f11874c7cb16',
                'title_request' => 'دعوى تفريق',
                'description_request' => 'دعوى تفريق',
                'location_request' => 'شارع الثورة',
                'budget_min' => '50000.00',
                'budget_max' => '60000.00',
                'type' => 'private',
                'status' => 'open',
                'client_id' => '8d22fa52-554f-4490-8359-e3e68c595d8d',
                'assigned_lawyer_id' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'created_at' => '2026-08-22 00:00:00',
                'updated_at' => '2026-08-22 09:43:07',
            ),
        ));
        
        
    }
}