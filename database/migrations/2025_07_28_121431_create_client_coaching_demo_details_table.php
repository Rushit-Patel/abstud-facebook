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
        Schema::create('client_coaching_demo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->foreignId('coaching_id')
                  ->constrained('coachings')
                  ->onDelete('cascade');
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->onDelete('cascade');
            $table->date('demo_date')->nullable();
            $table->text('assign_owner')->nullable();
            $table->text('added_by')->nullable();
            $table->string('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_coaching_demo_details');
    }
};
