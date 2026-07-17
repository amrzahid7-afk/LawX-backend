<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// just For relation between lawyer_profiles and offices tables, to add foreign key to employed_in_office_id column in lawyer_profiles table
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 2024_01_01_000003_add_office_fk_to_lawyer_profiles_table.php
    public function up(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->foreign('employed_in_office_id')
                ->references('id')->on('offices')
                ->nullOnDelete();
        });
        Schema::table('cases', function (Blueprint $table) {
            // الآن نضيف العلاقة بأمان لأن جميع الجداول قد تم إنشاؤها مسبقاً
            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->dropForeign(['employed_in_office_id']);
        });
    }
};
