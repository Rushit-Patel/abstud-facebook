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
        Schema::create('client_previous_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->text('rejection_country')->nullable();
            $table->text('rejection_month_year')->nullable();
            $table->text('rejection_visa_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_previous_rejections');
    }
};
