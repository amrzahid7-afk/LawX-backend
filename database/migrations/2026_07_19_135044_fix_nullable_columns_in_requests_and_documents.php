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
        // Schema::table('legal_requests', function (Blueprint $table) {
        //     $table->uuid('assigned_lawyer_id')->nullable()->change();
        // });

        // Schema::table('documents', function (Blueprint $table) {
        //     $table->uuid('case_id')->nullable()->change();
        //     $table->uuid('proposal_id')->nullable()->change();
        //     $table->uuid('legal_request_id')->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // في حال أردت التراجع عن التغييرات (اختياري)
        Schema::table('legal_requests', function (Blueprint $table) {
            $table->uuid('assigned_lawyer_id')->nullable(false)->change();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->uuid('case_id')->nullable(false)->change();
        });
    }
};
