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

            // Case ID
            $table->uuid('case_id')->nullable(); // تعريف العمود أولاً
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade'); // ثم تعريف العلاقة

            // Invoice ID (هذا هو السطر المهم)
            $table->uuid('invoice_id')->nullable(); // تأكد من وضع nullable قبل foreign
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');

            // Legal Request ID
            $table->uuid('legal_request_id')->nullable();
            $table->foreign('legal_request_id')->references('id')->on('legal_requests')->onDelete('cascade');

            // Proposal ID
            $table->uuid('proposal_id')->nullable();
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');

            // Uploaded By
            $table->uuid('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
        // Schema::create('documents', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->text('file_path');
        //     $table->string('type');
        //     $table->text('nots')->nullable();
        //     $table->ForeignUuid('case_id')->constrained('cases')->onDelete('cascade')->nullable();
        //     $table->ForeignUuid('invoice_id')->constrained('invoices')->onDelete('cascade')->nullable();
        //     $table->ForeignUuid('legal_request_id')->constrained('legal_requests')->onDelete('cascade')->nullable();
        //     $table->ForeignUuid('proposal_id')->constrained('proposals')->onDelete('cascade')->nullable();
        //     $table->ForeignUuid('uploaded_by')->constrained('users')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
