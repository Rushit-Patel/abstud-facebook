<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Facebook Business Account (One per branch/company)
        Schema::create('facebook_business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); // Connect to branch
            $table->string('business_name');
            $table->string('facebook_business_id')->unique();
            $table->text('access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('app_id');
            $table->text('app_secret');
            $table->enum('status', ['connected', 'disconnected', 'expired'])->default('disconnected');
            $table->timestamps();
        });

        // 2. Facebook Pages (Multiple pages under one business account)
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_business_account_id')->constrained()->onDelete('cascade');
            $table->string('page_name');
            $table->string('facebook_page_id')->unique();
            $table->text('page_access_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Facebook Lead Forms (Forms from pages)
        Schema::create('facebook_lead_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_page_id')->constrained()->onDelete('cascade');
            $table->string('form_name');
            $table->string('facebook_form_id')->unique();
            $table->text('form_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Facebook Leads (Actual leads from Facebook)
        Schema::create('facebook_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_lead_form_id')->constrained()->onDelete('cascade');
            $table->string('facebook_lead_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('additional_data')->nullable(); // Other form fields
            $table->timestamp('facebook_created_time');
            $table->enum('status', ['new', 'processed', 'failed'])->default('new');
            $table->timestamps();
        });

        // 5. Parameter Mappings (Field mapping configuration)
        Schema::create('facebook_parameter_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_lead_form_id')->constrained()->onDelete('cascade');
            $table->string('facebook_field_name'); // e.g., 'full_name', 'email', 'phone_number'
            $table->string('facebook_field_type'); // e.g., 'text', 'email', 'phone', 'select'
            $table->string('system_field_name'); // Your system's field name
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Custom Field Mappings (for dynamic fields)
        Schema::create('facebook_custom_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_lead_form_id')->constrained()->onDelete('cascade');
            $table->string('facebook_custom_question'); // Custom question from Facebook
            $table->string('system_field_name'); // Where to map in your system
            $table->string('data_type')->default('text'); // text, number, date, boolean
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Lead Source Tracking (Campaign and ad tracking)
        Schema::create('facebook_lead_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_lead_id')->constrained()->onDelete('cascade');
            $table->string('campaign_id')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('adset_id')->nullable();
            $table->string('adset_name')->nullable();
            $table->string('ad_id')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();
        });

        // 8. Webhook Settings (Simple webhook configuration)
        Schema::create('facebook_webhook_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_business_account_id')->constrained()->onDelete('cascade');
            $table->string('webhook_url');
            $table->string('verify_token');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facebook_webhook_settings');
        Schema::dropIfExists('facebook_lead_sources');
        Schema::dropIfExists('facebook_custom_field_mappings');
        Schema::dropIfExists('facebook_parameter_mappings');
        Schema::dropIfExists('facebook_leads');
        Schema::dropIfExists('facebook_lead_forms');
        Schema::dropIfExists('facebook_pages');
        Schema::dropIfExists('facebook_business_accounts');
    }
};
