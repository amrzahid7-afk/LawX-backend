<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tasks')->delete();
        
        \DB::table('tasks')->insert(array (
            0 => 
            array (
                'id' => '15992d96-0c05-4b00-b815-2cc9095385dc',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'استخراج قرار محكمة',
                'description' => 'استخراج محكمة  الخاص بالعقار رقم 815',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:47:02',
                'updated_at' => '2026-08-22 20:47:02',
            ),
            1 => 
            array (
                'id' => '1e10b057-241b-4afc-91af-f75aaad60c08',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'قيد مالي',
                'description' => 'استخراج  قيد مالي   الخاص بالعقار رقم 815',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:48:07',
                'updated_at' => '2026-08-22 20:48:07',
            ),
            2 => 
            array (
                'id' => '8cf94ad7-69ac-4ef7-a898-c67cd13eb50e',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'كتابة مرافعة',
                'description' => 'كتابة مرافعة',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:50:06',
                'updated_at' => '2026-08-22 20:50:06',
            ),
            3 => 
            array (
                'id' => '9ab17804-0cc3-4068-951c-ef088f0f18f4',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'براءة ذمة',
                'description' => 'استخراج  براءة ذمة  الخاص بالعقار رقم 815',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:47:42',
                'updated_at' => '2026-08-22 20:47:42',
            ),
            4 => 
            array (
                'id' => 'd7846dbe-ff88-4165-96b3-19248575be03',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'قيد مالي',
                'description' => 'استخراج  بيان حركة للمدعو مصطفى الغالب',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:49:06',
                'updated_at' => '2026-08-22 20:49:06',
            ),
            5 => 
            array (
                'id' => 'fbb2ec66-d558-4130-954a-753986e56643',
                'case_id' => '4f506dd8-8c11-46db-b6c1-0f8ebe97c295',
                'title' => 'استخراج قيد عقاري',
                'description' => 'استخراج قيد عقاري مع بيان ملكية الحصة السهمية لكل مالك بالعقار',
                'due_date' => '2029-11-01 00:00:00',
                'assigned_to' => 'f3a3d6c6-f451-43ed-8d42-d1de2da8c585',
                'status' => 'pending',
                'created_at' => '2026-08-22 20:46:11',
                'updated_at' => '2026-08-22 20:46:11',
            ),
        ));
        
        
    }
}