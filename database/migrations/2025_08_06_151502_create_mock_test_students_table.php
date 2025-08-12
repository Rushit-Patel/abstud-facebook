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
        Schema::create('mock_test_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')
                ->constrained('mock_tests')
                ->onDelete('cascade');
            $table->foreignId('client_coaching_student_id')
                ->constrained('client_coachings')
                ->onDelete('cascade');
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
        Schema::dropIfExists('mock_test_students');
    }
};
