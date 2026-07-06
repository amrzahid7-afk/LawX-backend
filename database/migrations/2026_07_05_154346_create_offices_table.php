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
        Schema::create('offices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('owner_lawyer_profile_id');
            $table->enum('status', ['active', 'suspended'])->default('active');
            // $table->uuid('reviewed_by')->nullable(); // مستخدم النقابة الذي راجع الطلب
            // $table->timestamp('reviewed_at')->nullable();
            // $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->foreign('owner_lawyer_profile_id')->references('id')->on('lawyer_profiles')->cascadeOnDelete();
            // $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('office_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('office_id');
            $table->uuid('invited_lawyer_profile_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
            $table->foreign('office_id')->references('id')->on('offices')->cascadeOnDelete();
            $table->foreign('invited_lawyer_profile_id')->references('id')->on('lawyer_profiles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
