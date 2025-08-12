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
        Schema::create('client_coaching_attendances', function (Blueprint $table) {
            $table->id();
            $table->text('client_coaching_id')->nullable();
            $table->text('batch_id')->nullable();
            $table->date('attendance_date')->nullable();
            $table->text('added_by')->nullable();
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
        Schema::dropIfExists('client_coaching_attendances');
    }
};
