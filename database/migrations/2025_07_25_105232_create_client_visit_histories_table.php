<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_visit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('client_details')->onDelete('cascade');
            $table->foreignId('client_lead_id')->constrained('client_leads')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('assign_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('token_no');
            $table->date('date');
            $table->enum('status', ['pending', 'invite', 'receive', 'complete'])->default('pending');
            $table->dateTime('invited_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_visit_histories');
    }
};
