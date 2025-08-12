<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Email Automation Campaigns
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['status_change', 'time_based', 'manual', 'follow_up_due']);
            $table->json('trigger_conditions'); // Conditions for when to trigger
            $table->foreignId('email_template_id')->constrained('email_templates');
            $table->integer('delay_minutes')->default(0); // Delay before sending
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // 1=high, 2=medium, 3=low
            $table->enum('execution_type', ['one_time', 'automation'])->default('automation');
            $table->datetime('scheduled_at')->nullable();
            $table->enum('schedule_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->json('schedule_config')->nullable();
            $table->datetime('next_run_at')->nullable();
            $table->datetime('last_run_at')->nullable();
            $table->json('lead_filters')->nullable();
            $table->boolean('apply_to_new_leads')->default(false);
            $table->timestamps();
        });

        // Email Automation Rules
        Schema::create('email_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->string('field_name'); // e.g., 'status', 'sub_status', 'lead_type'
            $table->string('operator'); // e.g., 'equals', 'not_equals', 'in', 'not_in'
            $table->json('field_value'); // The value(s) to match
            $table->timestamps();
        });

        // Email Queue/Log
        Schema::create('email_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_lead_id')->constrained('client_leads')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('email_campaigns');
            $table->foreignId('email_template_id')->constrained('email_templates');
            $table->string('recipient_email');
            $table->string('subject');
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled']);
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('email_data')->nullable(); // Variables used in email
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });

        // Email Sequences (for drip campaigns)
        Schema::create('email_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Email Sequence Steps
        Schema::create('email_sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('email_sequences')->onDelete('cascade');
            $table->integer('step_order'); // 1, 2, 3, etc.
            $table->foreignId('email_template_id')->constrained('email_templates');
            $table->integer('delay_days'); // Days after previous step or start
            $table->json('conditions')->nullable(); // Additional conditions for this step
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Lead Email Sequence Tracking
        Schema::create('lead_email_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_lead_id')->constrained('client_leads')->onDelete('cascade');
            $table->foreignId('sequence_id')->constrained('email_sequences');
            $table->integer('current_step')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled']);
            $table->timestamps();
        });

        // Email Performance Tracking
        Schema::create('email_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_log_id')->constrained('email_automation_logs');
            $table->boolean('opened')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->string('clicked_url')->nullable();
            $table->boolean('bounced')->default(false);
            $table->boolean('unsubscribed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_performance');
        Schema::dropIfExists('lead_email_sequences');
        Schema::dropIfExists('email_sequence_steps');
        Schema::dropIfExists('email_sequences');
        Schema::dropIfExists('email_automation_logs');
        Schema::dropIfExists('email_automation_rules');
        Schema::dropIfExists('email_campaigns');
    }
};
