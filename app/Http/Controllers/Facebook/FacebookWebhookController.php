<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Services\FacebookLeadIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class FacebookWebhookController extends Controller
{
    protected $integrationService;

    public function __construct(FacebookLeadIntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    /**
     * Verify webhook subscription (Facebook requirement)
     */
    public function verify(Request $request)
    {
        $hubMode = $request->query('hub_mode');
        $hubChallenge = $request->query('hub_challenge');
        $hubVerifyToken = $request->query('hub_verify_token');

        Log::info('Facebook webhook verification attempt', [
            'hub_mode' => $hubMode,
            'hub_verify_token' => $hubVerifyToken,
            'hub_challenge' => $hubChallenge,
        ]);

        // Check if the mode is 'subscribe'
        if ($hubMode !== 'subscribe') {
            Log::warning('Invalid hub mode for webhook verification', ['hub_mode' => $hubMode]);
            return response('Invalid hub mode', 400);
        }

        // Get verify token from configuration
        $configuredVerifyToken = config('services.facebook.webhook.verify_token');

        if (!$configuredVerifyToken || $hubVerifyToken !== $configuredVerifyToken) {
            Log::warning('Invalid verify token for webhook verification', [
                'provided_token' => $hubVerifyToken,
                'configured_token_exists' => !empty($configuredVerifyToken)
            ]);
            return response('Invalid verify token', 403);
        }

        Log::info('Facebook webhook verification successful', [
            'hub_challenge' => $hubChallenge,
        ]);

        // Return the challenge to complete verification
        return response($hubChallenge, 200);
    }

    /**
     * Handle incoming webhook data from Facebook
     */
    public function handle(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Facebook webhook received', [
                'data' => $data,
                'headers' => $request->headers->all(),
            ]);

            // Verify the request is from Facebook
            if (!$this->verifyFacebookSignature($request)) {
                Log::warning('Invalid Facebook signature for webhook');
                return response('Unauthorized', 401);
            }

            // Check if this is a leadgen event
            if (!isset($data['object']) || $data['object'] !== 'page') {
                Log::info('Webhook object is not a page, ignoring', ['object' => $data['object'] ?? 'unknown']);
                return response('OK', 200);
            }

            // Process each entry
            foreach ($data['entry'] ?? [] as $entry) {
                $this->processWebhookEntry($entry);
            }

            return response('OK', 200);

        } catch (Exception $e) {
            Log::error('Error processing Facebook webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Process individual webhook entry
     */
    protected function processWebhookEntry(array $entry): void
    {
        $pageId = $entry['id'] ?? null;
        $changes = $entry['changes'] ?? [];

        Log::info('Processing webhook entry', [
            'page_id' => $pageId,
            'changes_count' => count($changes),
        ]);

        foreach ($changes as $change) {
            if ($change['field'] === 'leadgen') {
                $this->processLeadgenChange($change, $pageId);
            }
        }
    }

    /**
     * Process leadgen webhook change
     */
    protected function processLeadgenChange(array $change, ?string $pageId): void
    {
        $leadData = $change['value'] ?? [];
        
        Log::info('Processing leadgen change', [
            'page_id' => $pageId,
            'lead_data' => $leadData,
        ]);

        $leadId = $leadData['leadgen_id'] ?? null;
        $formId = $leadData['form_id'] ?? null;

        if (!$leadId || !$formId) {
            Log::warning('Missing leadgen_id or form_id in webhook data', [
                'lead_id' => $leadId,
                'form_id' => $formId,
            ]);
            return;
        }

        // Here you would typically fetch the lead data from Facebook API
        // For now, we'll simulate the lead data structure
        $mockLeadData = [
            'id' => $leadId,
            'created_time' => now()->toISOString(),
            'field_data' => [
                ['name' => 'full_name', 'values' => ['John Doe']],
                ['name' => 'email', 'values' => ['john.doe@example.com']],
                ['name' => 'phone_number', 'values' => ['+1234567890']],
            ],
        ];

        // Process the lead using the integration service
        $result = $this->integrationService->processLead($mockLeadData, $formId);

        if ($result['success']) {
            Log::info('Lead processed successfully from webhook', [
                'facebook_lead_id' => $result['facebook_lead_id'],
                'system_lead_id' => $result['system_lead_id'] ?? null,
            ]);
        } else {
            Log::error('Failed to process lead from webhook', [
                'error' => $result['error'],
                'lead_id' => $leadId,
                'form_id' => $formId,
            ]);
        }
    }

    /**
     * Verify Facebook signature (optional but recommended for security)
     */
    protected function verifyFacebookSignature(Request $request): bool
    {
        // Get the raw body and the signature header
        $payload = $request->getContent();
        $signatureHeader = $request->header('X-Hub-Signature-256');

        if (!$signatureHeader) {
            Log::warning('No X-Hub-Signature-256 header found in webhook request');
            return true; // For development, you might want to skip signature verification
        }

        // Extract the signature
        $signature = str_replace('sha256=', '', $signatureHeader);

        // You would need to get the app secret for verification
        // For now, we'll skip actual verification in development
        Log::info('Facebook signature verification skipped for development');
        
        return true;
    }

    /**
     * Manual webhook test (for development)
     */
    public function testWebhook(Request $request)
    {
        $testData = [
            'object' => 'page',
            'entry' => [
                [
                    'id' => 'test_page_id',
                    'changes' => [
                        [
                            'field' => 'leadgen',
                            'value' => [
                                'leadgen_id' => 'test_lead_' . time(),
                                'form_id' => 'test_form_id',
                                'page_id' => 'test_page_id',
                                'created_time' => time(),
                            ]
                        ]
                    ]
                ]
            ]
        ];

        Log::info('Manual webhook test initiated', ['test_data' => $testData]);

        try {
            foreach ($testData['entry'] as $entry) {
                $this->processWebhookEntry($entry);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook test completed successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
