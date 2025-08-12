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
        Schema::create('client_english_proficiency_test_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->foreignId('english_proficiency_test_id')
                ->constrained('english_proficiency_tests', 'id')
                ->onDelete('cascade')
                ->name('fk_test_booking_test_id');
            // $table->foreignId('client_coaching_id')
            //     ->nullable()
            //     ->constrained('client_coachings')
            //     ->onDelete('cascade');
            $table->unsignedBigInteger('client_coaching_id')->nullable();

            $table->foreign('client_coaching_id', 'fk_booking_coaching_id')
                ->references('id')
                ->on('client_coachings')
                ->onDelete('cascade');
            $table->text('exam_way')->nullable();
            $table->text('exam_mode_id')->nullable();
            $table->date('exam_date')->nullable();
            $table->text('exam_center')->nullable();
            $table->date('result_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_english_proficiency_test_bookings');
    }
};
