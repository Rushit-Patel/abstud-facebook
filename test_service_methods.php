<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing FacebookLeadIntegrationService methods...\n";
    
    $service = app('App\Services\FacebookLeadIntegrationService');
    
    echo "Testing getProcessingStats...\n";
    $stats = $service->getProcessingStats(1);
    echo "Stats: " . json_encode($stats) . "\n";
    
    echo "Testing getTodayLeadsCount (this was causing the facebook_created_time ambiguity)...\n";
    $todayCount = $service->getTodayLeadsCount(1);
    echo "Today leads count: " . $todayCount . "\n";
    
    echo "Testing getRecentLeads...\n";
    $recentLeads = $service->getRecentLeads(1, 5);
    echo "Recent leads count: " . count($recentLeads) . "\n";
    
    echo "All FacebookLeadIntegrationService tests passed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
