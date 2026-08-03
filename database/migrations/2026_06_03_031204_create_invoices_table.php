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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('case_id')->nullable();            // القضية التابعة لها الفاتورة
            $table->uuid('lawyer_id');          // الذي أصدر الفاتورة
            $table->uuid('client_id')->nullable();         // الذي يجب عليه الدفع
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->date('due_date');           // تاريخ الاستحقاق النهائي
            $table->enum('type', ['For_case', 'salary'])->default('For_case');
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('payment_gateway')->default('stripe');
            $table->json('items')->nullable();   // تفاصيل الفاتورة (اسم البند، السعر) بصيغة JSON
            $table->string('pdf_url')->nullable(); // رابط لملف PDF المحفوظ
            $table->timestamps();
            $table->foreign('case_id')->references('id')->on('cases');
            $table->foreign('lawyer_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('users');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
