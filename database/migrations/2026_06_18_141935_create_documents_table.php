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
            // $table->text('nots')->nullable();
            $table->integer('file_size')->nullable();
            // Case ID
            $table->uuid('case_id')->nullable();
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            // Invoice ID
            $table->uuid('invoice_id')->nullable();
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
            $table->text('extracted_text')->nullable();

            $table->json('ai_tags')->nullable();

            $table->text('ai_summary')->nullable();

            $table->boolean('has_summary')->default(false);

            $table->boolean('has_versions')->default(false);

            $table->integer('version_number')->default(1);

            $table->uuid('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('documents')->nullOnDelete();

            $table->index('parent_id');
            $table->index('version_number');
            $table->index('has_summary');
            $table->index('has_versions');

            $table->fullText('extracted_text');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'version_number', 'has_summary', 'has_versions']);
            $table->dropFullText('extracted_text');
            $table->dropColumn([
                'extracted_text',
                'ai_tags',
                'ai_summary',
                'has_summary',
                'has_versions',
                'version_number',
                'parent_id'
            ]);
        });
    }
};
