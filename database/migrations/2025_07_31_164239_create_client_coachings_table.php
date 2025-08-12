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
        Schema::create('client_coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_reg_id')
                  ->constrained('client_lead_registrations')
                  ->onDelete('cascade');
            $table->text('branch_id')->nullable();
            $table->text('coaching_id')->nullable();
            $table->text('batch_id')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('faculty')->nullable();
            $table->text('coaching_length')->nullable();
            $table->text('added_by')->nullable();
            $table->string('is_complete_coaching')->default(0);
            $table->string('is_drop_coaching')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_coachings');
    }
};
