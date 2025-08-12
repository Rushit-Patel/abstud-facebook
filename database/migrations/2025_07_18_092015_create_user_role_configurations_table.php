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
        Schema::create('user_role_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('permission_type'); // 'show-all', 'country', 'purpose', 'coaching', 'show-branch'
            $table->json('configuration_data')->nullable(); // Store branch_ids, country_ids, purpose_ids, coaching_ids
            $table->timestamps();
            
            $table->unique(['user_id', 'role_id', 'permission_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role_configurations');
    }
};
