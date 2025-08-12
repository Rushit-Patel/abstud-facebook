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
        Schema::create('mock_test_student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_student_id')
                ->constrained('mock_test_students')
                ->onDelete('cascade');
            $table->text('modual_id')->nullable();
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
        Schema::dropIfExists('mock_test_student_results');
    }
};
