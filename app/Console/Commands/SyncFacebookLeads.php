<?php

namespace App\Console\Commands;

use App\Http\Controllers\Facebook\FacebookIntegrationController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncFacebookLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-leads {--branch-id= : Sync leads for specific branch ID} {--all : Sync leads for all branches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Facebook leads from all active lead forms for all business accounts';

    protected FacebookIntegrationController $controller;

    public function __construct(FacebookIntegrationController $controller)
    {
        parent::__construct();
        $this->controller = $controller;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Facebook leads synchronization...');
        
        try {
            $branchId = $this->option('branch-id');
            $syncAll = $this->option('all');
            
            if ($branchId && !$syncAll) {
                $this->info("Syncing leads for branch ID: {$branchId}");
            } else {
                $this->info("Syncing leads for all branches");
            }
            
            // Create a mock request object for the controller
            $request = new \Illuminate\Http\Request();
            if ($branchId && !$syncAll) {
                $request->merge(['branch_id' => $branchId]);
            }
            
            // Call the controller method directly
            $response = $this->controller->apiSyncLeads($request);
            $data = $response->getData(true);
            
            if ($data['success']) {
                $this->info('✓ ' . $data['message']);
                
                // Display detailed results if available
                if (isset($data['results'])) {
                    $this->info('=== Branch Details ===');
                    foreach ($data['results'] as $result) {
                        $status = $result['success'] ? '✓' : '✗';
                        $this->info("{$status} Branch {$result['branch_id']} ({$result['business_name']}): {$result['synced_count']} new leads");
                    }
                }
                
                // Log success
                Log::info('Facebook leads sync command completed successfully', [
                    'total_synced' => $data['total_synced'] ?? $data['synced_count'] ?? 0,
                    'total_processed' => $data['total_processed'] ?? 0,
                    'branch_id' => $branchId
                ]);
                
                return Command::SUCCESS;
            } else {
                $this->error('✗ ' . $data['message']);
                
                Log::error('Facebook leads sync command failed', [
                    'error' => $data['message'],
                    'branch_id' => $branchId
                ]);
                
                return Command::FAILURE;
            }
            
        } catch (Exception $e) {
            $this->error('✗ Fatal error during Facebook leads synchronization: ' . $e->getMessage());
            
            Log::error('Fatal error in Facebook leads sync command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'branch_id' => $this->option('branch-id')
            ]);
            
            return Command::FAILURE;
        }
    }
}
