<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // يجب أن يعتمدها مدير المكتب
            $table->date('expense_date'); // تاريخ صرف المصروف لحسابه في الشهر الصحيح
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
