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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('name');
            $table->string('mobile_no');
            $table->string('email_id');
            $table->string('call_status');
            $table->string('purpose');
            $table->string('country');
            $table->string('coaching');
            $table->string('branch');
            $table->string('lead_type');
            $table->string('assign_to');
            $table->string('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
