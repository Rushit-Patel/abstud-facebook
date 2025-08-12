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
        Schema::create('client_mobile_verifies', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no')->nullable();
            $table->string('otp')->nullable();
            $table->string('is_verify')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_mobile_verifies');
    }
};
