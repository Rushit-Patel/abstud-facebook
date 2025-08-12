<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_key')->unique(); // 'new_lead_assign', 'new_task_assign', etc.
            $table->string('title');
            $table->text('description'); // Template with variables like {user_name}, {lead_name}
            $table->string('icon')->nullable(); // For different icons
            $table->string('color')->default('#3B82F6'); // Notification color
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('team_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_type_id')->constrained('team_notification_types');
            $table->string('title');
            $table->text('message'); // Processed message with actual values
            $table->string('link')->nullable(); // Where to redirect when clicked
            $table->json('data')->nullable(); // Additional data if needed
            $table->boolean('is_seen')->default(false);
            $table->timestamp('seen_at')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Who will receive notification
            $table->foreignId('created_by')->constrained('users')->nullable(); // Who triggered the notification
            $table->timestamps();
            
            $table->index(['user_id', 'is_seen']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_notification_types');
        Schema::dropIfExists('team_notifications');

    }
};