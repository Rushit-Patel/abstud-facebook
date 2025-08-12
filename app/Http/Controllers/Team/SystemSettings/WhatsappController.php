<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\WhatsappProvider;
use App\Models\WhatsappTemplateVariableMapping;
use App\Services\WhatsappService;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    private WhatsappService $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        try {
            // Load templates directly in the controller
            $result = $this->whatsappService->getAllTemplates();
            $provider = $this->whatsappService->getCurrentProvider();
            
            $templates = $result['success'] ? ($result['templates'] ?? []) : [];
            $error = $result['success'] ? null : ($result['error'] ?? 'Failed to load templates');
            
            // Get all template mappings for display
            $templateMappings = [];
            if (!empty($templates)) {
                foreach ($templates as $template) {
                    $templateName = $template['name'] ?? null;
                    if ($templateName) {
                        $mappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($templateName);
                        if (!empty($mappings)) {
                            $templateMappings[$templateName] = $mappings;
                        }
                    }
                }
            }
            
            return view('team.settings.whatsapp-templates.index', compact('templates', 'provider', 'error', 'templateMappings'));
        } catch (\Exception $e) {
            Log::error('Failed to load templates in controller', ['error' => $e->getMessage()]);
            return view('team.settings.whatsapp-templates.index', [
                'templates' => [],
                'provider' => null,
                'error' => 'Failed to load templates: ' . $e->getMessage(),
                'templateMappings' => []
            ]);
        }
    }

    /**
     * View a specific template
     */
    public function view($templateName)
    {
        try {
            $result = $this->whatsappService->getAllTemplates();
            $provider = $this->whatsappService->getCurrentProvider();
            
            if (!$result['success']) {
                return redirect()->route('team.settings.whatsapp-templates.index')
                    ->with('error', 'Failed to load templates: ' . ($result['error'] ?? 'Unknown error'));
            }

            $templates = $result['templates'] ?? [];
            $template = collect($templates)->firstWhere('name', $templateName);
            
            if (!$template) {
                return redirect()->route('team.settings.whatsapp-templates.index')
                    ->with('error', 'Template not found');
            }
            
            // Get system variables for template mapping
            $systemVariables = TemplateVariableService::getAllVariables();
            $sampleValues = TemplateVariableService::getSampleValues();
            
            // Get existing variable mappings for this template
            $existingMappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($templateName);
            
            return view('team.settings.whatsapp-templates.view', compact('template', 'provider', 'systemVariables', 'sampleValues', 'existingMappings'));
        } catch (\Exception $e) {
            Log::error('Failed to view template', [
                'template_name' => $templateName,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('team.settings.whatsapp-templates.index')
                ->with('error', 'Failed to load template: ' . $e->getMessage());
        }
    }

    /**
     * Get all templates from the active WhatsApp provider
     */
    public function getAllTemplates()
    {
        try {
            $result = $this->whatsappService->getAllTemplates();

            return response()->json([
                'success' => $result['success'] ?? false,
                'templates' => $result['templates'] ?? [],
                'provider' => $this->whatsappService->getCurrentProvider(),
                'error' => $result['error'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get all templates', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch templates'
            ], 500);
        }
    }

    /**
     * Get templates for a specific provider
     */
    public function getProviderTemplates(Request $request, $providerId)
    {
        try {
            $provider = WhatsappProvider::findOrFail($providerId);
            $result = $this->whatsappService->getAllTemplates($provider->slug);
            
            return response()->json([
                'success' => $result['success'],
                'templates' => $result['templates'] ?? [],
                'error' => $result['error'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get provider templates', [
                'provider_id' => $providerId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch provider templates'
            ], 500);
        }
    }

    /**
     * Get user messages for a specific provider
     */
    public function getUserMessages(Request $request, $providerId)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'country_code' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        try {
            $provider = WhatsappProvider::findOrFail($providerId);
            
            $options = array_filter([
                'countryCode' => $request->input('country_code', '+91'),
                'limit' => $request->input('limit', 50),
                'offset' => $request->input('offset', 0),
                'startDate' => $request->input('start_date'),
                'endDate' => $request->input('end_date')
            ]);

            $result = $this->whatsappService->getUserMessages(
                $request->input('phone_number'),
                $options,
                $provider->slug
            );
            
            return response()->json([
                'success' => $result['success'],
                'messages' => $result['messages'] ?? [],
                'error' => $result['error'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user messages', [
                'provider_id' => $providerId,
                'phone_number' => $request->input('phone_number'),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch user messages'
            ], 500);
        }
    }

    /**
     * Save variable mappings for a template
     */
    public function saveVariableMappings(Request $request, $templateName)
    {
        try {
            $request->validate([
                'mappings' => 'array',
                'mappings.*' => 'nullable|string|max:255'
            ]);

            $mappings = $request->input('mappings', []);
            $userId = Auth::user()->id;

            // Save mappings to database
            WhatsappTemplateVariableMapping::saveMappingsForTemplate($templateName, $mappings, $userId);

            return redirect()->back()->with('success', 'Variable mappings saved successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Invalid mapping data provided.');
        } catch (\Exception $e) {
            Log::error('Failed to save variable mappings', [
                'template_name' => $templateName,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to save variable mappings. Please try again.');
        }
    }

    /**
     * Get variable mappings for a template
     */
    public function getVariableMappings($templateName)
    {
        try {
            $mappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($templateName);
            
            return response()->json([
                'success' => true,
                'mappings' => $mappings
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get variable mappings', [
                'template_name' => $templateName,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch variable mappings'
            ], 500);
        }
    }
}
