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
        Schema::create('client_document_check_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('client_details')
                ->onDelete('cascade');
            $table->foreignId('client_lead_id')
                ->constrained('client_leads')
                ->onDelete('cascade');
            $table->foreignId('document_check_list_id')
                ->constrained('document_check_lists')
                ->onDelete('cascade');
            $table->text('document_type')->nullable();
            $table->text('status')->nullable();
            $table->text('notes')->nullable();
            $table->text('meta_data')->nullable();
            $table->text('added_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_document_check_lists');
    }
};
