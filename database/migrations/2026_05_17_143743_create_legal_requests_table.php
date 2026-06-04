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
        // more columns //
        /**
         *   add category Id Column that match with category cases table

         */
        Schema::create('legal_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_request');
            $table->text('description_request');
            $table->text('location_request'); //  المحكمة و المدينة و المحافظة
            $table->decimal('budget_min')->nullable();
            $table->decimal('budget_max')->nullable();
            $table->enum('type', ['private', 'public'])->default('public');
            $table->enum('status', ['open', 'close', 'in_progress'])->default('open');

            $table->ForeignUuid('client_id')->constrained('users')->onDelete('cascade');
            $table->ForeignUuid('assigned_lawyer_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_requests');
    }
};
