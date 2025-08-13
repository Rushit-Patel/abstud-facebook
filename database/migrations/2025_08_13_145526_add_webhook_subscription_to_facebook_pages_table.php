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
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->boolean('webhook_subscribed')->default(false)->after('is_active');
            $table->timestamp('webhook_subscribed_at')->nullable()->after('webhook_subscribed');
            $table->json('webhook_subscribed_fields')->nullable()->after('webhook_subscribed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->dropColumn(['webhook_subscribed', 'webhook_subscribed_at', 'webhook_subscribed_fields']);
        });
    }
};
