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
        Schema::create('opponents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['individual', 'company', 'government_entity'])->default('individual');
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
        Schema::create('case_opponents', function (Blueprint $table) {
            $table->uuid('case_id');
            $table->uuid('opponent_id');
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            $table->foreign('opponent_id')->references('id')->on('opponents')->onDelete('cascade');
            $table->primary(['case_id', 'opponent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opponents');
    }
};
