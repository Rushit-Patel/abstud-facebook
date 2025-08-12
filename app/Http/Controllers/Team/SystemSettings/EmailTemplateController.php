<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\DataTables\Team\Setting\EmailTemplateDataTable;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    public function index(EmailTemplateDataTable $dataTable)
    {
        return $dataTable->render('team.settings.email-templates.index');
    }

    public function create()
    {
        // Get system variables for template creation
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        return view('team.settings.email-templates.create', compact('systemVariables', 'sampleValues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'html_template' => 'required|string',
            'text_template' => 'nullable|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_system'] = false; // Custom templates are never system templates
        
        // Clean up variables array - remove empty values
        if (isset($validated['variables'])) {
            $validated['variables'] = array_filter($validated['variables'], function($var) {
                return !empty(trim($var));
            });
            // Re-index array to remove gaps
            $validated['variables'] = array_values($validated['variables']);
        } else {
            $validated['variables'] = [];
        }

        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active') ? true : false;

        EmailTemplate::create($validated);

        return redirect()->route('team.settings.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        // Get system variables for template preview
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        // Generate preview content with sample values
        $previewContent = $emailTemplate->getCompiledContent();
        
        return view('team.settings.email-templates.show', compact('emailTemplate', 'systemVariables', 'sampleValues', 'previewContent'));
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        // Get system variables for template preview
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        // Generate preview content with sample values
        $previewContent = $emailTemplate->getCompiledContent();

        return view('team.settings.email-templates.preview', compact('emailTemplate', 'systemVariables', 'sampleValues', 'previewContent'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        // Get system variables for template editing
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        return view('team.settings.email-templates.edit', compact('emailTemplate', 'systemVariables', 'sampleValues'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('email_templates')->ignore($emailTemplate->id),
            ],
            'subject' => 'required|string|max:255',
            'html_template' => 'required|string',
            'text_template' => 'nullable|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($emailTemplate->is_system) {
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }
        
        // Clean up variables array - remove empty values
        if (isset($validated['variables'])) {
            $validated['variables'] = array_filter($validated['variables'], function($var) {
                return !empty(trim($var));
            });
            // Re-index array to remove gaps
            $validated['variables'] = array_values($validated['variables']);
        } else {
            $validated['variables'] = [];
        }

        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $emailTemplate->update($validated);

        return redirect()->route('team.settings.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        if (!$emailTemplate->canBeDeleted()) {
            return redirect()->route('team.settings.email-templates.index')
                ->with('error', 'System templates cannot be deleted.');
        }

        $emailTemplate->delete();

        return redirect()->route('team.settings.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name . ' (Copy)';
        $newTemplate->slug = $emailTemplate->slug . '-copy-' . time();
        $newTemplate->is_system = false;
        $newTemplate->save();

        return redirect()->route('team.settings.email-templates.edit', $newTemplate)
            ->with('success', 'Email template duplicated successfully.');
    }

    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

        $status = $emailTemplate->is_active ? 'activated' : 'deactivated';

        return redirect()->route('team.settings.email-templates.index')
            ->with('success', "Email template {$status} successfully.");
    }

    /**
     * Test email template with real client data
     */
    public function test(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'test_email' => 'required|email',
            'client_lead_id' => 'nullable|exists:client_leads,id'
        ]);

        $testEmail = $request->input('test_email');
        $clientLeadId = $request->input('client_lead_id');
        
        // Get system variables and sample values
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();

        // If client lead provided, get actual values
        $clientLead = null;
        if ($clientLeadId) {
            $clientLead = \App\Models\ClientLead::with('client')->find($clientLeadId);
        }

        // Generate compiled content
        $compiledContent = $emailTemplate->getCompiledContent([], $clientLead);

        return view('team.settings.email-templates.test', compact(
            'emailTemplate', 
            'systemVariables', 
            'sampleValues', 
            'compiledContent',
            'testEmail',
            'clientLead'
        ));
    }
}