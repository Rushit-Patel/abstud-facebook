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
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->date('followup_date')->nullable();
            $table->text('remarks')->nullable();
            $table->text('status')->nullable();
            $table->text('communication')->nullable();
            $table->text('updated_by')->nullable();
            $table->text('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};
