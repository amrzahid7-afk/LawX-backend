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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('file_path');
            $table->string('type');
            $table->text('nots')->nullable();
            $table->ForeignUuid('case_id')->constrained('cases')->onDelete('cascade')->nullable();
            $table->ForeignUuid('legal_request_id')->constrained('legal_requests')->onDelete('cascade')->nullable();
            $table->ForeignUuid('proposal_id')->constrained('proposals')->onDelete('cascade')->nullable();
            $table->ForeignUuid('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
