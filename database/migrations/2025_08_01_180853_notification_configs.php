<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_configs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g., 'new_task_notification'
            $table->string('class'); // e.g., App\Notifications\NewTaskNotification

            // Email channel
            $table->boolean('email_enabled')->default(false);
            $table->unsignedBigInteger('email_template_id')->nullable();

            // WhatsApp channel
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('whatsapp_template')->nullable();

            // System notification
            $table->boolean('system_enabled')->default(false);
            $table->unsignedBigInteger('team_notification_types')->nullable();
            $table->timestamps();

            // Foreign keys (optional, based on your existing tables)
            $table->foreign('email_template_id')->references('id')->on('email_templates')->nullOnDelete();
            $table->foreign('team_notification_types')->references('id')->on('team_notification_types')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_configs');
    }
};
