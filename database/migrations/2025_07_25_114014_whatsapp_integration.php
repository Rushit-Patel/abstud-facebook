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
        Schema::create('whatsapp_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('api_endpoint');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->integer('rate_limit_per_minute')->default(60);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('whatsapp_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('whatsapp_providers')->onDelete('cascade');
            $table->string('config_key', 100);
            $table->text('config_value');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['provider_id', 'config_key']);
        });
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('campaign_type', ['bulk', 'automation', 'follow_up', 'reminder']);
            $table->enum('trigger_type', ['status_change', 'time_based', 'manual', 'follow_up_due', 'birthday', 'anniversary']);
            $table->json('trigger_conditions')->nullable(); // Conditions for when to trigger
            $table->enum('message_type', ['text', 'template'])->default('text');
            $table->text('message_content')->nullable(); // For text messages
            $table->string('template_name')->nullable(); // For template messages
            $table->json('template_variables')->nullable(); // Template variable mappings
            $table->foreignId('provider_id')->nullable()->constrained('whatsapp_providers')->onDelete('set null');
            $table->integer('delay_minutes')->default(0); // Delay before sending
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // 1=high, 2=medium, 3=low
            $table->enum('execution_type', ['one_time', 'automation'])->default('automation');
            $table->datetime('scheduled_at')->nullable();
            $table->enum('schedule_frequency', ['once', 'daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->json('schedule_config')->nullable(); // Days of week, time, etc.
            $table->datetime('next_run_at')->nullable();
            $table->datetime('last_run_at')->nullable();
            $table->json('lead_filters')->nullable(); // Filters for which leads to target
            $table->boolean('apply_to_new_leads')->default(false);
            $table->integer('total_recipients')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_delivered')->default(0);
            $table->integer('messages_failed')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // WhatsApp Campaign Rules (for automation triggers)
        Schema::create('whatsapp_campaign_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('whatsapp_campaigns')->onDelete('set null');
            $table->string('field_name'); // e.g., 'status', 'sub_status', 'lead_type'
            $table->string('operator'); // e.g., 'equals', 'not_equals', 'in', 'not_in'
            $table->json('field_value'); // The value(s) to match
            $table->timestamps();
        });
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('whatsapp_campaigns')->onDelete('set null');
            $table->string('message_id')->nullable()->unique(); // Unique ID from WhatsApp provider
            $table->string('phone_number', 20);
            $table->enum('message_type', ['text', 'image', 'document', 'template'])->default('text');
            $table->string('template_name')->nullable(); // WhatsApp template name
            $table->json('template_variables')->nullable(); // Template variables
            $table->text('message_content')->nullable(); // Message content or template data
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->json('provider_response')->nullable(); // Full response from WhatsApp API
            $table->text('error_message')->nullable(); // Error details if message failed
            $table->integer('retry_count')->default(0); // Number of retry attempts
            $table->boolean('is_test')->default(false); // Flag for test messages
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('scheduled_at')->nullable(); // When message should be sent (for delayed messages)
            $table->timestamp('sent_at')->nullable(); // When message was actually sent
            $table->timestamp('delivered_at')->nullable(); // When message was delivered
            $table->timestamps();
            // Add indexes for better performance
            $table->index(['campaign_id', 'status']);
            $table->index(['status', 'scheduled_at']); // For processing pending messages
            $table->index(['is_test']);
            $table->index(['created_by']);
            // Indexes for better performance
            $table->index(['phone_number', 'status']);
            $table->index('created_at');
        });
        Schema::create('whatsapp_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')
                  ->constrained('whatsapp_messages')
                  ->onDelete('cascade');
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->string('file_type', 100);
            $table->bigInteger('file_size');
            $table->string('media_type', 50)
                  ->default('document');
            $table->string('media_url', 500)
                  ->nullable();
            $table->boolean('is_uploaded')
                  ->default(false);
            $table->json('upload_response')
                  ->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['message_id', 'media_type']);
            $table->index('file_type');
            $table->index('created_at');
        });

        Schema::create('whatsapp_template_variable_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('template_name'); // WhatsApp template name
            $table->string('whatsapp_variable'); // {{1}}, {{2}}, etc.
            $table->string('system_variable'); // client_name, email, etc.
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index for faster lookups
            $table->index(['template_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_providers');
        Schema::dropIfExists('whatsapp_provider_configs');
        Schema::dropIfExists('whatsapp_campaign_rules');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_message_attachments');
        Schema::dropIfExists('whatsapp_template_variable_mappings');
    }
};
