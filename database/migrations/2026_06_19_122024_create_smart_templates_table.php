<?php
// database/migrations/xxxx_xx_xx_create_smart_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title'); // اسم القالب (مثال: عقد إيجار موحد)

            $table->uuid('file_id');
            $table->foreign('file_id')->references('id')->on('documents')->cascadeOnDelete();

            $table->json('variables_json')->nullable();

            $table->uuid('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();

            $table->enum('scope', ['public', 'private'])->default('private');

            // التصنيف (عقود، مذكرات، لوائح دعوى)
            $table->string('category')->index();

            $table->timestamps();
            $table->softDeletes();

            // Indexes للسرعة
            $table->index(['owner_id', 'scope']);
            $table->index(['category', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_templates');
    }
};
