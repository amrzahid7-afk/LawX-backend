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
        Schema::create('signers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('signature_request_id');
            $table->uuid('user_id');
            $table->string('role'); // client, syndicate, lawyer
            $table->boolean('is_seal')->default(false); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            $table->integer('x_position'); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            $table->integer('y_position'); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            $table->integer('width'); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            $table->integer('height'); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            $table->enum('sign_type', ['seal', 'signature']); // هل يضع ختم أم توقيع خطي؟ (مفيد للنقابة)
            // x_position, y_position, width, height, sign_type
            // تحديد مكان التوقيع في الملف
            $table->string('anchor_text')->nullable(); // النص الذي يبحث عنه DocuSign لوضع التوقيع تحته
            $table->integer('page_number')->default(1); // رقم الصفحة
            $table->string('docusign_recipient_id')->nullable(); // معرف الموقع عند DocuSign
            $table->enum('status', ['pending', 'signed', 'declined'])->default('pending');
            $table->timestamp('signed_at')->nullable();


            $table->foreign('signature_request_id')->references('id')->on('signature_requests')->onDelete('cascade'); // المحامي الذي طلب التوقيع();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // المحامي الذي طلب التوقيع();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signers');
    }
};
