<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ExpensesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('expenses')->delete();
        
        \DB::table('expenses')->insert(array (
            0 => 
            array (
                'id' => '17411538-b820-4c67-bd83-9d85ba7a487b',
                'task_id' => '15992d96-0c05-4b00-b815-2cc9095385dc',
                'amount' => '200.00',
                'description' => 'اجور نقل',
                'status' => 'pending',
                'expense_date' => '2026-08-22',
                'created_at' => '2026-08-22 20:52:53',
                'updated_at' => '2026-08-22 20:52:53',
            ),
            1 => 
            array (
                'id' => '7e7610f6-8709-4721-a335-dfe93ff8a03b',
                'task_id' => '15992d96-0c05-4b00-b815-2cc9095385dc',
                'amount' => '900.00',
                'description' => 'ضرائب',
                'status' => 'pending',
                'expense_date' => '2026-08-22',
                'created_at' => '2026-08-22 20:53:10',
                'updated_at' => '2026-08-22 20:53:10',
            ),
            2 => 
            array (
                'id' => 'f3827dbc-5e7e-4396-91d6-4dfe178242c7',
                'task_id' => '15992d96-0c05-4b00-b815-2cc9095385dc',
                'amount' => '500.00',
                'description' => 'مصاريف طوابع',
                'status' => 'pending',
                'expense_date' => '2026-08-22',
                'created_at' => '2026-08-22 20:51:40',
                'updated_at' => '2026-08-22 20:51:40',
            ),
        ));
        
        
    }
}