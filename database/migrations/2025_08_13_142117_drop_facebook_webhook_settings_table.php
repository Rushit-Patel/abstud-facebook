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
        Schema::dropIfExists('facebook_webhook_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('facebook_webhook_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_business_account_id')->constrained()->cascadeOnDelete();
            $table->string('webhook_url');
            $table->string('verify_token');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('facebook_business_account_id');
        });
    }
};
