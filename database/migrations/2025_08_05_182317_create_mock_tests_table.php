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
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->date('mock_test_date')->nullable();
            $table->time('mock_test_time')->nullable();
            $table->foreignId('coaching_id')
                ->constrained('coachings')
                ->onDelete('cascade');
            $table->text('batch_id')->nullable();
            $table->text('added_by')->nullable();
            $table->text('branch_id')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_tests');
    }
};
