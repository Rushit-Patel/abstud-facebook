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
        Schema::create('client_document_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_document_check_list_id')
                ->constrained('client_document_check_lists')
                ->onDelete('cascade');
            $table->text('document_name')->nullable();
            $table->text('document_path')->nullable();
            $table->text('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_document_uploads');
    }
};
