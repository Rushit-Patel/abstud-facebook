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
        // Add additional fields to facebook_pages table
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->string('page_category')->nullable()->after('page_name');
            $table->integer('fan_count')->default(0)->after('page_category');
            $table->text('profile_picture_url')->nullable()->after('fan_count');
            $table->boolean('is_published')->default(true)->after('profile_picture_url');
        });

        // Add additional fields to facebook_lead_forms table
        Schema::table('facebook_lead_forms', function (Blueprint $table) {
            $table->string('status')->default('ACTIVE')->after('form_description');
            $table->integer('leads_count')->default(0)->after('status');
            $table->json('questions')->nullable()->after('leads_count');
            $table->text('privacy_policy_url')->nullable()->after('questions');
            $table->text('follow_up_action_url')->nullable()->after('privacy_policy_url');
            $table->timestamp('facebook_created_time')->nullable()->after('follow_up_action_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->dropColumn([
                'page_category',
                'fan_count', 
                'profile_picture_url',
                'is_published'
            ]);
        });

        Schema::table('facebook_lead_forms', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'leads_count',
                'questions',
                'privacy_policy_url',
                'follow_up_action_url',
                'facebook_created_time'
            ]);
        });
    }
};
