<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 2024_01_01_000002_create_offices_table.php
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('owner_lawyer_profile_id');
            $table->enum('status', ['active', 'suspended'])->default('suspended');
            $table->timestamps();

            // آمن: lawyer_profiles تم إنشاؤه في المهاجرة السابقة
            $table->foreign('owner_lawyer_profile_id')
                ->references('id')->on('lawyer_profiles')
                ->cascadeOnDelete();
        });

        Schema::create('office_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('office_id');
            $table->uuid('invited_lawyer_profile_id');
            $table->uuid('invited_by')->nullable();
            $table->decimal('salary_expected', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->cascadeOnDelete();
            $table->foreign('invited_lawyer_profile_id')->references('id')->on('lawyer_profiles')->cascadeOnDelete();
            $table->foreign('invited_by')->references('id')->on('lawyer_profiles')->nullOnDelete();

            $table->index(['office_id', 'status']);
            $table->index(['invited_lawyer_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_invitations');
        Schema::dropIfExists('offices');
    }
};
