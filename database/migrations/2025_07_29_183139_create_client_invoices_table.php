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
        Schema::create('client_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                  ->constrained('client_leads')
                  ->onDelete('cascade');
            $table->text('service_id')->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('total_amount')->nullable();
            $table->text('discount')->nullable();
            $table->text('payable_amount')->nullable();
            $table->text('billing_company_id')->nullable();
            $table->text('added_by')->nullable();
            $table->date('due_date')->nullable();
            $table->date('due_amount')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_invoices');
    }
};
