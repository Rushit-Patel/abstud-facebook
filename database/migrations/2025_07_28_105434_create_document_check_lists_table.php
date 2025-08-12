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
        Schema::create('document_check_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('document_categories')
                  ->onDelete('cascade');
            $table->text('name')->nullable();
            $table->text('applicable_for')->nullable();
            $table->text('country')->nullable();
            $table->text('coaching')->nullable();
            $table->text('tags')->nullable();
            $table->text('type')->nullable();
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
        Schema::dropIfExists('document_check_lists');
    }
};
