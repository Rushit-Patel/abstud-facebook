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
        Schema::create('coaching_material_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coaching_material_id')
                ->constrained('coaching_materials')
                ->onDelete('cascade');
            $table->text('branch_id')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->date('stock_date')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaching_material_stocks');
    }
};
