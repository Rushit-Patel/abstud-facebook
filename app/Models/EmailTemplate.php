<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MailTemplates\Models\MailTemplate;
use App\Services\TemplateVariableService;
use App\Models\ClientLead;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'html_template',
        'text_template',
        'variables',
        'is_system',
        'is_active',
        'description',
        'category',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'subject' => '',
        'html_template' => '',
        'text_template' => null,
    ];

    protected static function booted(): void
    {
        static::creating(function (EmailTemplate $template) {
            // Auto-generate slug if not provided
            if (empty($template->slug)) {
                $template->slug = \Str::slug($template->name);
            }
        });

        static::created(function (EmailTemplate $template) {
            // Sync with spatie mail template
            $template->syncToMailTemplate();
        });

        static::updated(function (EmailTemplate $template) {
            // Sync with spatie mail template
            $template->syncToMailTemplate();
        });

        static::deleted(function (EmailTemplate $template) {
            // Remove from spatie mail template
            MailTemplate::where('mailable', $template->slug)->delete();
        });
    }

    public function syncToMailTemplate(): void
    {
        // Map template slugs to their corresponding mailable class names
        $mailableClassMap = [
            'team-account-created' => 'App\\Mail\\TeamAccountCreatedMail',
            'campaign-test-email' => 'App\\Mail\\CampaignTestMail',
            // Add more mappings here as you create more mail templates
        ];
        
        // Use the full class name if mapped, otherwise use the slug
        $mailableKey = $mailableClassMap[$this->slug] ?? $this->slug;
        
        MailTemplate::updateOrCreate(
            ['mailable' => $mailableKey],
            [
                'subject' => $this->subject,
                'html_template' => $this->html_template,
                'text_template' => $this->text_template,
            ]
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    protected function variablesList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->variables ? implode(', ', $this->variables) : 'None'
        );
    }

    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Get template by slug
     */
    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->active()->first();
    }

    /**
     * Get template content with variables replaced using TemplateVariableService
     */
    public function getCompiledContent(array $variables = [], ClientLead $clientLead = null): array
    {
        // Use TemplateVariableService for consistent variable replacement
        $systemVariables = $clientLead ? 
            TemplateVariableService::getValuesFromClientLead($clientLead) : 
            TemplateVariableService::getSampleValues();
        
        // Merge provided variables with system variables (provided variables take precedence)
        $allVariables = array_merge($systemVariables, $variables);
        
        return [
            'subject' => TemplateVariableService::replaceVariables($this->subject ?? '', $allVariables),
            'html_content' => TemplateVariableService::replaceVariables($this->html_template ?? '', $allVariables),
            'text_content' => TemplateVariableService::replaceVariables($this->text_template ?? '', $allVariables),
        ];
    }

    /**
     * Replace template variables with actual values using TemplateVariableService
     */
    public function replaceVariables(string $content, array $variables): string
    {
        return TemplateVariableService::replaceVariables($content, $variables);
    }

    /**
     * Get all variable keys from the template content using TemplateVariableService
     */
    public function extractVariablesFromContent(): array
    {
        $content = ($this->html_template ?? '') . ' ' . ($this->text_template ?? '') . ' ' . ($this->subject ?? '');
        return TemplateVariableService::extractVariables($content);
    }

    /**
     * Validate if all required variables are provided using TemplateVariableService
     */
    public function validateVariables(array $providedVariables): array
    {
        $content = ($this->html_template ?? '') . ' ' . ($this->text_template ?? '') . ' ' . ($this->subject ?? '');
        return TemplateVariableService::validateVariables($content, $providedVariables);
    }

    /**
     * Get available system variables for this template
     */
    public function getSystemVariables(): array
    {
        return TemplateVariableService::getAllVariables();
    }

    /**
     * Get sample values for template preview
     */
    public function getSampleVariableValues(): array
    {
        return TemplateVariableService::getSampleValues();
    }
}