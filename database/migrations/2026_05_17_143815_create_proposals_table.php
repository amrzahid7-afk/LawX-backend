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
        Schema::create('proposals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('price');
            $table->string('estimated_days');
            $table->text('message_for_client');
            $table->date('valid_until');
            $table->text('payment_terms');
            $table->enum('status', ['rejected ', 'accepted ', 'pending'])->default('pending');
            $table->enum('price_currency', ['EGP', 'USD', 'syp'])->default('syp');
            $table->ForeignUuid('request_id')->constrained('legal_requests')->onDelete('cascade');
            $table->ForeignUuid('lawer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
