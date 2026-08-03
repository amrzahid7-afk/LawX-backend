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
        Schema::table('office_invitations', function (Blueprint $table) {
            $table->decimal('salary_expected', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_invitations', function (Blueprint $table) {
            $table->decimal('salary_expected', 10, 2)->nullable();
        });
    }
};
