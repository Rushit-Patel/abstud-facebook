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
        Schema::create('education_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->string('education_level')->nullable();
            $table->string('education_board')->nullable();
            $table->string('language')->nullable();
            $table->string('education_stream')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('result')->nullable();
            $table->string('no_of_backlog')->nullable();
            $table->string('institute')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_details');
    }
};
