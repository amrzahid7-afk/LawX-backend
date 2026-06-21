<?php
// database/migrations/xxxx_xx_xx_create_generated_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('template_id');
            $table->foreign('template_id')->references('id')->on('smart_templates')->cascadeOnDelete();

            $table->uuid('file_id');
            $table->foreign('file_id')->references('id')->on('documents')->cascadeOnDelete();

            $table->uuid('case_id')->nullable();
            $table->foreign('case_id')->references('id')->on('cases')->nullOnDelete();

            $table->uuid('generated_by');
            $table->foreign('generated_by')->references('id')->on('users')->cascadeOnDelete();

            $table->json('used_values')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_templates');
    }
};
