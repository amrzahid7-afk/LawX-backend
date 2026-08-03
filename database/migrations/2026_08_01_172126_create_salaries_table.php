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
        Schema::create('salaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lawyer_id'); // user_id
            $table->integer('month'); // 1-12
            $table->integer('year'); // 2024
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('total_approved_expenses', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0); // الأساسي + المصروفات
            $table->enum('status', ['generated', 'paid'])->default('generated');
            $table->uuid('invoice_id')->nullable(); // ربطه بفاتورة الدفع
            $table->timestamps();
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
