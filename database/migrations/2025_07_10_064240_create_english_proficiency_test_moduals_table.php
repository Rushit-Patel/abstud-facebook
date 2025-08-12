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
        Schema::create('english_proficiency_test_moduals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('english_proficiency_tests_id');

            // Define foreign key with shorter name
            $table->foreign('english_proficiency_tests_id', 'eptm_ept_id_fk')
                  ->references('id')
                  ->on('english_proficiency_tests')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('minimum_score');
            $table->string('maximum_score');
            $table->string('range_score');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('english_proficiency_test_moduals');
    }
};
