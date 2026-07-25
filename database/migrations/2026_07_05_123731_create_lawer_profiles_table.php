<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */ // database/migrations/2024_01_01_000001_create_lawyer_profiles_table.php
    // 2024_01_01_000001_create_lawyer_profiles_table.php
    public function up(): void
    {
        Schema::create('lawyer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->unique();

            // العمود موجود، لكن بلا مفتاح أجنبي الآن
            // ولاحظ: بدون unique() — انظر الملاحظة أدناه
            $table->uuid('employed_in_office_id')->nullable()->index();

            $table->string('national_id')->unique();
            $table->string('full_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_family_name')->nullable();
            $table->string('civil_registry_number')->nullable();
            $table->string('syndicate_card_number')->unique();
            $table->string('syndicate_branch');
            $table->enum('lawyer_state', ['trainee', 'licensed'])->default('trainee');
            $table->uuid('supervised_by_lawyer_id')->nullable();
            $table->timestamp('trainee_approved_independent_at')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->boolean('is_suspended')->default(false);
            $table->uuid('created_by')->nullable();
            //$table->softDeletes();
            $table->timestamps();

            // مفاتيح آمنة فقط (جداولها موجودة مسبقاً)
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('supervised_by_lawyer_id')->references('id')->on('lawyer_profiles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lawyer_profiles');
    }
};
