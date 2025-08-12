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
        Schema::create('client_employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->text('company_name')->nullable();
            $table->text('designation')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('no_of_year')->nullable();
            $table->integer('is_working')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_employment_details');
    }
};
