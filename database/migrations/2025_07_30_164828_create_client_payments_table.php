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
        Schema::create('client_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->foreignId('invoice_id')
                  ->constrained('client_invoices')
                  ->onDelete('cascade');
            $table->text('amount')->nullable();
            $table->text('payment_mode')->nullable();
            $table->text('remarks')->nullable();
            $table->text('added_by')->nullable();
            $table->text('created_by')->nullable();
            $table->text('payment_receipt')->nullable();
            $table->text('gst')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payments');
    }
};
