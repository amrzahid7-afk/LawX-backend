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
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lawyer_id');
            $table->uuid('document_id');
            $table->string('envelope_id')->nullable(); // معرف المظروف من DocuSign
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('cascade'); // المحامي الذي طلب التوقيع();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade'); // المحامي الذي طلب التوقيع();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_requests');
    }
};
