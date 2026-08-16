<?php

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
            $table->longText('content');
            $table->string('law_number')->nullable()->index();
            $table->foreignUuid('category_id')->nullable()->constrained('legal_categories')->nullOnDelete();
            $table->enum('status', ['active', 'amended', 'repealed'])->default('active');
            $table->enum('source', ['admin', 'external_api'])->default('admin');
            $table->string('country')->default('Syria')->index();
            $table->date('effective_date')->nullable();

            // Storing Vector Embeddings for MySQL Semantic Search
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
