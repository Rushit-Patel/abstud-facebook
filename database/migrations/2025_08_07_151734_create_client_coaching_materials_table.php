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
        Schema::create('client_coaching_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_coaching_id')
                ->constrained('client_coachings')
                ->onDelete('cascade');
            $table->text('material_id');
            $table->text('added_by');
            $table->integer('is_provided')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_coaching_materials');
    }
};
