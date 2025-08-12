<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Unified Automation Campaign Processing
Schedule::command('automation:process-campaigns --type=all --batch-size=100')
    ->everyMinute()
    ->withoutOverlapping(5) // Prevent overlapping for 5 minutes
    ->runInBackground()
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/campaigns.log'));

// Process pending WhatsApp messages (from queue)
Schedule::command('messages:process-pending --type=all --limit=50')
    ->everyMinute()
    ->withoutOverlapping(2) // Prevent overlapping for 2 minutes
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/all-messages.log'));

// Health check - monitor automation system every hour
// Schedule::command('automation:process-campaigns --dry-run --batch-size=1')
//     ->hourly()
//     ->name('automation-health-check')
//     ->onOneServer() // Only run on one server in multi-server setup
//     ->appendOutputTo(storage_path('logs/automation-health.log'));
