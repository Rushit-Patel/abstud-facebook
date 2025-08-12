<?php

namespace App\Http\Controllers\Team\Automation;

use App\DataTables\Team\Automation\WhatsappMessageLogDataTable;
use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\WhatsappProvider;
use App\Models\WhatsappTemplateVariableMapping;
use App\Models\WhatsappCampaign;
use App\Services\WhatsappService;
use App\Jobs\ProcessWhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsappAutomationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display WhatsApp automation dashboard
     */
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('team.dashboard'), 'title' => 'Dashboard'],
            ['label' => 'Automation', 'url' => route('team.automation.index'), 'title' => 'Automation'],
            ['label' => 'WhatsApp', 'url' => null, 'title' => 'WhatsApp Automation'],
        ];

        // Get WhatsApp statistics
        $whatsappStats = [
            'totalProviders' => WhatsappProvider::count(),
            'activeProviders' => WhatsappProvider::where('is_active', true)->count(),
            'totalMessagesSent' => WhatsappMessage::whereIn('status', ['sent', 'delivered', 'read'])->count(),
            'pendingMessages' => WhatsappMessage::where('status', 'pending')->count(),
            'failedMessages' => WhatsappMessage::where('status', 'failed')->count(),
            'totalTemplates' => WhatsappTemplateVariableMapping::distinct('template_name')->count(),
            'totalCampaigns' => WhatsappCampaign::count(),
            'activeCampaigns' => WhatsappCampaign::where('is_active', true)->count(),
        ];

        // Get active providers
        $activeProviders = WhatsappProvider::where('is_active', true)
            ->orderBy('priority')
            ->get();

        // Recent messages
        $recentMessages = WhatsappMessage::with('provider')
            ->latest()
            ->take(10)
            ->get();

        // Available templates
        $availableTemplates = WhatsappTemplateVariableMapping::select('template_name')
            ->distinct()
            ->get()
            ->pluck('template_name');

        return view('team.automation.whatsapp.index', compact(
            'breadcrumbs',
            'whatsappStats',
            'activeProviders',
            'recentMessages',
            'availableTemplates'
        ));
    }

    /**
     * Send a single WhatsApp message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
            'message' => 'required|string|max:1000',
            'provider_slug' => 'nullable|string|exists:whatsapp_providers,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Dispatch job to send WhatsApp message
            ProcessWhatsappMessage::dispatch(
                $request->phone_number,
                $request->message,
                'text',
                $request->provider_slug
            );

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp message queued successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue WhatsApp message', [
                'phone' => $request->phone_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send WhatsApp template message
     */
    public function sendTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
            'template_name' => 'required|string',
            'variables' => 'nullable|array',
            'provider_slug' => 'nullable|string|exists:whatsapp_providers,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get template mappings
            $templateMappings = WhatsappTemplateVariableMapping::where('template_name', $request->template_name)
                ->pluck('system_variable', 'whatsapp_variable')
                ->toArray();

            // Prepare template variables
            $templateVariables = [];
            if ($request->variables) {
                foreach ($request->variables as $key => $value) {
                    $templateVariables[$key] = $value;
                }
            }

            // Dispatch job to send template message
            ProcessWhatsappMessage::dispatch(
                $request->phone_number,
                json_encode([
                    'template_name' => $request->template_name,
                    'variables' => $templateVariables
                ]),
                'template',
                $request->provider_slug,
                ['template_mappings' => $templateMappings]
            );

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp template message queued successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue WhatsApp template message', [
                'phone' => $request->phone_number,
                'template' => $request->template_name,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue template message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get WhatsApp message logs
     */
    public function logs(WhatsappMessageLogDataTable $dataTable)
    {
        return $dataTable->render('team.automation.whatsapp.logs');
    }

    /**
     * Retry failed message
     */
    public function retryMessage($messageId)
    {
        try {
            $message = WhatsappMessage::findOrFail($messageId);

            if ($message->status !== 'failed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only failed messages can be retried'
                ], 400);
            }

            // Reset message status and increment retry count
            $message->update([
                'status' => 'pending',
                'retry_count' => $message->retry_count + 1,
                'error_message' => null
            ]);

            // Dispatch job to retry the message
            ProcessWhatsappMessage::dispatch(
                $message->phone_number,
                $message->message_content,
                $message->message_type,
                $message->provider->slug ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Message queued for retry'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retry WhatsApp message', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retry message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get template variables for a specific template
     */
    public function getTemplateVariables($templateName)
    {
        try {
            $variables = WhatsappTemplateVariableMapping::where('template_name', $templateName)
                ->get()
                ->map(function ($mapping) {
                    return [
                        'whatsapp_variable' => $mapping->whatsapp_variable,
                        'system_variable' => $mapping->system_variable,
                        'description' => $this->getVariableDescription($mapping->system_variable)
                    ];
                });

            return response()->json([
                'success' => true,
                'variables' => $variables
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get template variables: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get description for system variables
     */
    private function getVariableDescription($systemVariable)
    {
        $descriptions = [
            'client_name' => 'Client\'s full name',
            'email' => 'Client\'s email address',
            'phone' => 'Client\'s phone number',
            'company_name' => 'Company name',
            'lead_status' => 'Current lead status',
            'amount' => 'Payment or invoice amount',
            'date' => 'Current date',
            'time' => 'Current time'
        ];

        return $descriptions[$systemVariable] ?? 'System variable';
    }
}
