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
        Schema::create('client_details', function (Blueprint $table) {
            $table->id();
            $table->text('client_code')->nullable();
            $table->text('first_name')->nullable();
            $table->text('middle_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('mobile_no')->nullable();
            $table->text('country_code')->nullable();
            $table->text('email_id')->nullable();
            $table->text('branch')->nullable();
            $table->text('gender')->nullable();
            $table->text('maratial_status')->nullable();
            $table->text('date_of_birth')->nullable();
            $table->text('country')->nullable();
            $table->text('state')->nullable();
            $table->text('city')->nullable();
            $table->text('whatsapp_no')->nullable();
            $table->text('whatsapp_country_code')->nullable();
            $table->text('source')->nullable();
            $table->text('address')->nullable();
            $table->text('lead_type')->nullable();
            $table->text('client_profile_photo')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_details');
    }
};
