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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone_code')->nullable();
            $table->string('currency', 3)->nullable(); // ISO 4217 currency code
            $table->string('currency_symbol', 10)->nullable();
            $table->json('timezones')->nullable(); // Store array of timezones
            $table->string('icon')->nullable(); // Country flag or icon
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
