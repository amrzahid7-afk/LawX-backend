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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // FK s
            $table->uuid('lawyer_id');
            $table->uuid('client_id');
            $table->uuid('case_id');
            // review rating
            $table->tinyInteger('professionalism_rating'); // الاحترافية
            $table->tinyInteger('communication_rating');   // التواصل
            $table->tinyInteger('result_rating');          // جودة النتيجة
            // average of all
            $table->float('rating', 3, 1)->default(0.0);
            // comment 
            $table->text('comment')->nullable();
            $table->boolean('is_hidden')->default(false);
            // make contraint for client review just one time for each case 
            $table->unique(['case_id', 'client_id']);
            $table->timestamps();
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
