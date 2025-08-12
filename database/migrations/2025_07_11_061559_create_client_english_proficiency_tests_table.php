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
        Schema::create('client_english_proficiency_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->foreignId('exam_id')
                  ->constrained('english_proficiency_tests')
                  ->onDelete('cascade');
            $table->date('exam_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clien_english_proficiency_tests');
    }
};
