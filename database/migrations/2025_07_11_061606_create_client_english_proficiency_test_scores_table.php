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
        Schema::create('client_english_proficiency_test_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_test_id')
                  ->constrained('client_english_proficiency_tests')
                  ->onDelete('cascade');
            $table->foreignId('exam_modual_id')
                  ->constrained('english_proficiency_test_moduals')
                  ->onDelete('cascade');
            $table->text('score');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clien_english_proficiency_test_scores');
    }
};
