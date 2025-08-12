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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->string('company_favicon')->nullable();
            $table->string('website_url')->nullable();
            $table->text('company_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_setup_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
