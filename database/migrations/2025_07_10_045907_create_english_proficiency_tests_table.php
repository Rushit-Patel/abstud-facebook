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
        Schema::create('english_proficiency_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('result_days')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('priority')->nullable();
            $table->integer('coaching_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('english_proficiency_tests');
    }
};
