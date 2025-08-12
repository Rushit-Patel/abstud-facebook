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

        Schema::create('billing_companies', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('mobile_no')->nullable();
            $table->text('email_id')->nullable();
            $table->string('is_gst')->default(0);
            $table->text('gst_form_name')->nullable();
            $table->text('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->text('branch')->nullable();
            $table->text('company_logo')->nullable();
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
        Schema::dropIfExists('billing_companies');
    }
};
