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
        Schema::create('case_payment_milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title'); // مثال: "دفعة بعد الجلسة الأولى" أو "القسط الشهري"
            $table->decimal('amount', 15, 2); // المبلغ المطلوب في هذه المرحلة
            $table->date('due_date'); // تاريخ الاستحقاق
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->foreignUuid('case_id')->constrained('cases')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_payment_milestones');
    }
};
