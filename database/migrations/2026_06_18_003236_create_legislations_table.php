<?php
// database/migrations/xxxx_xx_xx_create_legislations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legislations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content');
            $table->string('law_number')->unique();
            $table->foreignUuid('category_id')->nullable()->constrained('legal_categories')->nullOnDelete();
            $table->enum('status', ['active', 'amended', 'repealed'])->default('active');
            $table->enum('source', ['admin', 'external_api'])->default('admin');
            $table->string('country')->index();
            $table->date('effective_date')->nullable();
            $table->json('embedding')->nullable();
            $table->json('related_legislation_ids')->nullable();
            $table->json('articles')->nullable();
            $table->string('book')->nullable();
            $table->string('part')->nullable();
            $table->string('chapter')->nullable();
            $table->foreignUuid('parent_id')->nullable()->constrained('legislations')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('legislations');
    }
};
