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
        Schema::create('client_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->constrained('client_details')
                  ->onDelete('cascade');

            $table->date('client_date')->nullable();
            $table->string('lead_type')->nullable();
            $table->string('purpose')->nullable();
            $table->string('country')->nullable();
            $table->text('second_country')->nullable();
            $table->string('coaching')->nullable();
            $table->string('branch')->nullable();
            $table->string('assign_owner')->nullable(); // Consider renaming to "assign_owner"
            $table->string('added_by')->nullable(); // Consider renaming to "Added By"
            $table->string('source')->nullable();
            $table->string('tag')->nullable();
            $table->string('status')->nullable();
            $table->string('sub_status')->nullable();
            $table->text('remark')->nullable();
            $table->text('genral_remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_leads');
    }
};
