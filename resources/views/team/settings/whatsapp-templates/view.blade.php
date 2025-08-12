@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'WhatsApp Templates', 'url' => route('team.settings.whatsapp-templates.index')],
    ['title' => $template['display_name'] ?? $template['name'] ?? 'Template View']
];
@endphp

<x-team.layout.app title="Template: {{ $template['display_name'] ?? $template['name'] ?? 'Unknown' }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        {{ $template['display_name'] ?? $template['name'] ?? 'Template Details' }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        View template details and configuration
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.whatsapp-templates.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Templates
                    </a>
                    <button type="button" onclick="copyToClipboard('{{ $template['name'] ?? '' }}')" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-copy"></i>
                        Copy Template Name
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-success/10 border border-success/20 rounded-lg">
                    <div class="flex items-center gap-2 text-success">
                        <i class="ki-filled ki-check-circle"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-destructive/10 border border-destructive/20 rounded-lg">
                    <div class="flex items-center gap-2 text-destructive">
                        <i class="ki-filled ki-cross-circle"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-destructive/10 border border-destructive/20 rounded-lg">
                    <div class="flex items-center gap-2 text-destructive mb-2">
                        <i class="ki-filled ki-cross-circle"></i>
                        <span class="font-medium">Validation Errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-destructive">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Template Overview -->
            <x-team.card title="Template Overview" headerClass="">
                <!-- Provider Info -->
                @if($provider)
                    <div class="mb-6 p-4 bg-muted/50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="ki-filled ki-whatsapp text-green-500 text-xl"></i>
                            <span class="font-medium">{{ $provider->name }}</span>
                        </div>
                    </div>
                @endif

                <!-- Template Header Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="kt-card">
                        <div class="kt-card-content p-5">
                            <h3 class="font-medium text-mono mb-4">Basic Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Template Name:</span>
                                    <span class="text-sm font-mono">{{ $template['name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Display Name:</span>
                                    <span class="text-sm">{{ $template['display_name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Category:</span>
                                    <span class="text-sm">{{ $template['category'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Language:</span>
                                    <span class="text-sm">{{ $template['language'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kt-card">
                        <div class="kt-card-content p-5">
                            <h3 class="font-medium text-mono mb-4">Status & Settings</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Approval Status:</span>
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium
                                        {{ ($template['approval_status'] ?? '') === 'APPROVED' ? 'kt-badge kt-badge-success' : 'kt-badge kt-badge-warning' }}">
                                        {{ $template['approval_status'] ?? 'Unknown' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-secondary-foreground">Variables Present:</span>
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium
                                        {{ ($template['variable_present'] ?? '') === 'Yes' ? 'kt-badge kt-badge-info' : 'kt-badge kt-badge-secondary' }}">
                                        {{ $template['variable_present'] ?? 'No' }}
                                    </span>
                                </div>
                                @if(isset($template['created_at_utc']))
                                    <div class="flex justify-between">
                                        <span class="text-sm text-secondary-foreground">Created:</span>
                                        <span class="text-sm">{{ \Carbon\Carbon::parse($template['created_at_utc'])->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                                @if(isset($template['modified_at_utc']))
                                    <div class="flex justify-between">
                                        <span class="text-sm text-secondary-foreground">Modified:</span>
                                        <span class="text-sm">{{ \Carbon\Carbon::parse($template['modified_at_utc'])->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Content -->
                @if(isset($template['body']) && !empty($template['body']))
                    <div class="kt-card">
                        <div class="kt-card-content p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-medium text-mono">Template Body</h3>
                                <button type="button" onclick="copyToClipboard(`{{ addslashes($template['body']) }}`)" 
                                        class="kt-btn kt-btn-sm kt-btn-secondary">
                                    <i class="ki-filled ki-copy"></i>
                                    Copy Body
                                </button>
                            </div>
                            <div class="p-4 bg-muted/30 rounded-lg">
                                <div class="text-sm leading-relaxed">
                                    {!! nl2br(preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', e($template['body']))) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Variable Mapping Section -->
                @php
                    // Extract WhatsApp template variables like {{1}}, {{2}}, etc.
                    $whatsappVariables = [];
                    if (isset($template['body']) && !empty($template['body'])) {
                        preg_match_all('/\{\{(\d+)\}\}/', $template['body'], $matches);
                        $whatsappVariables = array_unique($matches[1]);
                        sort($whatsappVariables, SORT_NUMERIC);
                    }
                @endphp

                @if(!empty($whatsappVariables))
                    <form method="POST" action="{{ route('team.settings.whatsapp-templates.save-variable-mappings', ['templateName' => $template['name'] ?? '']) }}">
                        @csrf
                        <div class="kt-card mt-6">
                            <div class="kt-card-content p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-medium text-mono">Variable Mapping</h3>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="resetVariableMapping()" class="kt-btn kt-btn-sm kt-btn-secondary">
                                            <i class="ki-filled ki-arrows-circle"></i>
                                            Reset
                                        </button>
                                        <button type="submit" class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-check"></i>
                                            Save Mapping
                                        </button>
                                    </div>
                                </div>
                            
                            <div class="mb-4 p-3 bg-info/10 rounded-lg">
                                <p class="text-sm text-info">
                                    <i class="ki-filled ki-information-2 mr-1"></i>
                                    Map WhatsApp template variables to system variables for dynamic content replacement.
                                </p>
                            </div>

                            <div class="space-y-4">
                                @foreach($whatsappVariables as $index => $variable)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center p-4 border border-border rounded-lg">
                                        <div>
                                            <label class="block text-sm font-medium text-secondary-foreground mb-2">
                                                WhatsApp Variable
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex px-3 py-2 bg-primary/10 text-primary text-sm font-mono rounded">
                                                    &#123;&#123;{{ $variable }}&#125;&#125;
                                                </span>
                                                <button type="button" class="text-muted-foreground hover:text-primary">
                                                    <i class="ki-filled ki-copy text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-secondary-foreground mb-2">
                                                System Variable
                                            </label>
                                            <select name="mappings[{{ $variable }}]" class="kt-input variable-mapping-select" data-whatsapp-var="{{ $variable }}">
                                                <option value="">Select a system variable...</option>
                                                @foreach($systemVariables as $category => $variables)
                                                    <optgroup label="{{ $category }}">
                                                        @foreach($variables as $sysVar => $description)
                                                            <option value="{{ $sysVar }}" title="{{ $description }}"
                                                                {{ ($existingMappings[$variable] ?? '') === $sysVar ? 'selected' : '' }}>
                                                                {{ $sysVar }} - {{ $description }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Preview Section -->
                            <div class="mt-6 p-4 bg-muted/30 rounded-lg">
                                <h4 class="font-medium text-sm mb-3">Preview with Mapped Variables:</h4>
                                <div id="template-preview" class="text-sm leading-relaxed">
                                    <div class="text-muted-foreground italic">Select variable mappings to see preview...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @endif

                <!-- Additional Fields -->
                @php
                    $excludeFields = ['name', 'display_name', 'category', 'language', 'approval_status', 'variable_present', 'body', 'created_at_utc', 'modified_at_utc'];
                    $additionalFields = array_diff_key($template, array_flip($excludeFields));
                @endphp

                @if(!empty($additionalFields))
                    <div class="kt-card mt-6">
                        <div class="kt-card-content p-5">
                            <h3 class="font-medium text-mono mb-4">Additional Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($additionalFields as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-secondary-foreground">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="text-sm">
                                            @if(is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </x-team.card>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            // Copy to clipboard function
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    // You can add a toast notification here if needed
                    console.log('Copied to clipboard: ' + text);
                }).catch(function(err) {
                    console.error('Failed to copy text: ', err);
                });
            }

            // Variable mapping functions
            let originalTemplateBody = `{{ addslashes($template['body'] ?? '') }}`;
            let currentMapping = @json($existingMappings);

            // Sample system variable values for preview (passed from controller)
            const sampleValues = @json($sampleValues);

            // Reset variable mapping
            function resetVariableMapping() {
                if (confirm('Are you sure you want to reset all variable mappings?')) {
                    currentMapping = {};
                    document.querySelectorAll('.variable-mapping-select').forEach(select => {
                        select.value = '';
                    });
                    updatePreview();
                }
            }

            // Update preview with current mapping
            function updatePreview() {
                let previewText = originalTemplateBody;
                
                // Replace WhatsApp variables with mapped system variables
                Object.keys(currentMapping).forEach(whatsappVar => {
                    const systemVar = currentMapping[whatsappVar];
                    const sampleValue = sampleValues[systemVar] || `[${systemVar}]`;
                    
                    // Replace  with sample value
                    const regex = new RegExp(`\\{\\{${whatsappVar}\\}\\}`, 'g');
                    previewText = previewText.replace(regex, sampleValue);
                });

                // Apply WhatsApp formatting and display
                const previewContainer = document.getElementById('template-preview');
                if (previewContainer) {
                    // Convert *text* to bold and preserve line breaks
                    const formattedText = previewText
                        .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>');
                    
                    previewContainer.innerHTML = formattedText || '<div class="text-muted-foreground italic">Select variable mappings to see preview...</div>';
                }
            }

            // Handle variable mapping changes
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize preview with existing mappings
                updatePreview();

                // Listen for mapping changes
                document.querySelectorAll('.variable-mapping-select').forEach(select => {
                    select.addEventListener('change', function() {
                        const whatsappVar = this.dataset.whatsappVar;
                        const systemVar = this.value;
                        
                        if (systemVar) {
                            currentMapping[whatsappVar] = systemVar;
                        } else {
                            delete currentMapping[whatsappVar];
                        }
                        
                        updatePreview();
                    });
                });
            });
        </script>
    @endpush
</x-team.layout.app>
