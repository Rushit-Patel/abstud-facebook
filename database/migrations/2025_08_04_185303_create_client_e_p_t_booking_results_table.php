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
        Schema::create('client_e_p_t_booking_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_e_p_t_booking_result_id')
                  ->constrained('client_english_proficiency_test_bookings')
                  ->onDelete('cascade')
                  ->name('fk_client_e_p_t_booking_result_id');
            $table->text('exam_modual_id')->nullable();
            $table->text('score')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_e_p_t_booking_results');
    }
};
