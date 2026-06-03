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
        // Schema::create('cases', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->string('title');
        //     $table->text('description')->nullable();
        //     $table->enum('status', ['open', 'close', 'pending'])->default('Open');
        //     $table->string('court', 255)->nullable();
        //     $table->foreignUuid('client_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignUuid('lawer_id')->constrained('users')->onDelete('cascade');
        //     // $table->foreignUuid('office_id')->constrained()->onDelete('cascade');
        //     // $table->foreignUuid('assigned_employee_id')->nullable()->constrained('employees');
        //     // $table->foreignUuid('case_type_id')->constrained('case_types');
        //     // $table->foreignUuid('template_id')->nullable()->constrained('workflow_templates');
        //     // $table->integer('difficulty_stars')->default(1);
        //     // $table->decimal('total_value', 10, 2)->nullable();
        //     $table->timestamps();
        // });
        // New Migration
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('case_number')->unique();
            $table->string('title');
            $table->float('price');
            $table->string('case_category');
            $table->text('description')->nullable();
            $table->enum('result', ['pending', 'won', 'lost'])->default('pending');
            $table->tinyInteger('importance_stars')->default(3); // 1, 2, 3, 4, 5
            // نوع خطة الدفع لهذه القضية
            $table->enum('billing_type', [
                'fixed_proposal',       // دفع حسب العرض المتفق عليه (مبلغ مقطوع)
                'percentage_collection', // نسبة من التحصيل (مثلاً 10% من المبلغ المحصل)
                'installments',         // أقساط بفترات زمنية
                'pay_as_you_go'         // دفع حسب العمل (ساعة بساعة أو مهمة بمهمة)
            ])->default('fixed_proposal');

            // إذا كانت نسبة من التحصيل، نخزن النسبة هنا (مثلاً 15.00)
            $table->decimal('collection_percentage', 5, 2)->nullable();
            $table->enum('status', ['open', 'close', 'pending', 'archived'])->default('open');
            $table->enum('financial_status', ['unpaid', 'partially_paid', 'paid', 'overdue'])->default('unpaid');
            $table->integer('progress_percentage')->default(0);
            $table->string('court', 255)->nullable();
            $table->foreignUuid('lead_lawyer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_models');
    }
};
