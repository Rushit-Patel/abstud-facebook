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
        Schema::create('client_visited_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->text('visited_country')->nullable();
            $table->text('visited_visa_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_visited_countries');
    }
};
