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
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'close', 'pending'])->default('Open');
            $table->foreignUuid('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('lawer_id')->constrained('users')->onDelete('cascade');
            // $table->foreignUuid('office_id')->constrained()->onDelete('cascade');
            // $table->foreignUuid('assigned_employee_id')->nullable()->constrained('employees');
            // $table->foreignUuid('case_type_id')->constrained('case_types');
            // $table->foreignUuid('template_id')->nullable()->constrained('workflow_templates');
            // $table->integer('difficulty_stars')->default(1);
            // $table->decimal('total_value', 10, 2)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_models');
    }
};
