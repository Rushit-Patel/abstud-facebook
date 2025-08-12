<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;

class TemplateVariableController extends Controller
{
    /**
     * Get all available template variables
     */
    public function getAllVariables()
    {
        return response()->json([
            'success' => true,
            'variables' => TemplateVariableService::getAllVariables(),
            'sample_values' => TemplateVariableService::getSampleValues()
        ]);
    }

    /**
     * Extract variables from template content
     */
    public function extractVariables(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $content = $request->input('content');
        $extractedVars = TemplateVariableService::extractVariables($content);
        $availableVars = TemplateVariableService::getAllVariables();
        
        // Flatten available variables for easier lookup
        $flatAvailableVars = [];
        foreach ($availableVars as $category => $vars) {
            $flatAvailableVars = array_merge($flatAvailableVars, array_keys($vars));
        }

        return response()->json([
            'success' => true,
            'extracted_variables' => $extractedVars,
            'validation' => TemplateVariableService::validateVariables($content, $flatAvailableVars)
        ]);
    }

    /**
     * Validate template content
     */
    public function validateTemplate(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $content = $request->input('content');
        $availableVars = TemplateVariableService::getAllVariables();
        
        // Flatten available variables
        $flatAvailableVars = [];
        foreach ($availableVars as $category => $vars) {
            $flatAvailableVars = array_merge($flatAvailableVars, $vars);
        }

        $validation = TemplateVariableService::validateVariables($content, $flatAvailableVars);

        return response()->json([
            'success' => true,
            'validation' => $validation
        ]);
    }

    /**
     * Preview template with sample data
     */
    public function previewTemplate(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'variable_mapping' => 'nullable|array'
        ]);

        $content = $request->input('content');
        $variableMapping = $request->input('variable_mapping', []);
        $sampleValues = TemplateVariableService::getSampleValues();

        // Apply variable mapping if provided (for WhatsApp templates)
        if (!empty($variableMapping)) {
            foreach ($variableMapping as $whatsappVar => $systemVar) {
                if (isset($sampleValues[$systemVar])) {
                    $content = str_replace('{{' . $whatsappVar . '}}', $sampleValues[$systemVar], $content);
                }
            }
        } else {
            // For regular templates, replace all variables with sample values
            $content = TemplateVariableService::replaceVariables($content, $sampleValues);
        }

        return response()->json([
            'success' => true,
            'preview_content' => $content
        ]);
    }
}
