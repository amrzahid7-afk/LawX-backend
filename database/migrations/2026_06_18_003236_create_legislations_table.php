<?php
// database/migrations/xxxx_xx_xx_create_legislations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('legislations', function (Blueprint $table) {
    //         $table->uuid('id')->primary();
    //         $table->string('title');
    //         $table->text('content');
    //         $table->string('law_number')->unique();
    //         $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
    //         $table->enum('status', ['active', 'amended', 'repealed'])->default('active');
    //         $table->enum('source', ['admin', 'external_api'])->default('admin');
    //         $table->date('effective_date')->nullable();
    //         $table->json('embedding')->nullable();
    //         $table->string('country')->index();
    //         $table->enum('level', ['chapter', 'section', 'article', 'paragraph']);
    //         $table->foreign('parent_id')->nullable()->references('id')->on('legislations');
    //         // $table->foreignId('parent_id')->nullable()->constrained('legislations')->nullOnDelete();
    //         $table->timestamps();
    //         $table->softDeletes();
    //     });
    // }
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
            $table->date('effective_date')->nullable();
            $table->json('embedding')->nullable();
            $table->string('country')->index();
            $table->enum('level', ['chapter', 'section', 'article', 'paragraph']);

            $table->uuid('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('legislations')->nullOnDelete();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('legislations');
    }
};
