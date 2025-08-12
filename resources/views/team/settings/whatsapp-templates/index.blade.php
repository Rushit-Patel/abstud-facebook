@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'WhatsApp Templates']
];
@endphp

<x-team.layout.app title="WhatsApp Templates" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        WhatsApp Templates
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage WhatsApp templates and view user messages
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.whatsapp-templates.index') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-arrows-circle"></i>
                        Refresh Page
                    </a>
                </div>
            </div>

            <x-team.card title="{{ count($templates) }} WhatsApp Templates" headerClass="">
                @if($error)
                    <!-- Error State -->
                    <div class="text-center py-8 text-destructive">
                        <i class="ki-filled ki-information-4 text-4xl mb-4"></i>
                        <p>{{ $error }}</p>
                    </div>
                @elseif(count($templates) > 0)
                    <!-- Template Statistics -->
                    @php
                        $totalTemplates = count($templates);
                        $templatesWithVariables = collect($templates)->where('variable_present', 'Yes')->count();
                        $templatesWithMappings = count($templateMappings);
                        $templatesNeedingMappings = $templatesWithVariables - $templatesWithMappings;
                    @endphp
                    
                    @if($templatesWithVariables > 0)
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-muted/30 rounded-lg">
                                <div class="text-sm text-muted-foreground">Total Templates</div>
                                <div class="text-lg font-medium">{{ $totalTemplates }}</div>
                            </div>
                            <div class="p-4 bg-info/10 rounded-lg">
                                <div class="text-sm text-info">With Variables</div>
                                <div class="text-lg font-medium text-info">{{ $templatesWithVariables }}</div>
                            </div>
                            <div class="p-4 bg-success/10 rounded-lg">
                                <div class="text-sm text-success">Mapped</div>
                                <div class="text-lg font-medium text-success">{{ $templatesWithMappings }}</div>
                            </div>
                            <div class="p-4 bg-warning/10 rounded-lg">
                                <div class="text-sm text-warning">Need Mapping</div>
                                <div class="text-lg font-medium text-warning">{{ $templatesNeedingMappings }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Provider Info -->
                    @if($provider)
                        <div class="mb-6 p-4 bg-muted/50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="ki-filled ki-whatsapp text-green-500 text-xl"></i>
                                <span class="font-medium">{{ $provider->name }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Templates Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($templates as $template)
                            @php
                                $templateName = $template['name'] ?? null;
                                $hasVariables = ($template['variable_present'] ?? '') === 'Yes';
                                $hasMappings = $templateName && isset($templateMappings[$templateName]) && !empty($templateMappings[$templateName]);
                                $mappings = $hasMappings ? $templateMappings[$templateName] : [];
                            @endphp
                            
                            <div class="kt-card {{ $hasVariables && !$hasMappings ? 'border-warning/20' : ($hasMappings ? 'border-success/20' : '') }}">
                                <div class="kt-card-content p-5">
                                    <!-- Template Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <h4 class="font-medium text-mono text-base leading-tight">
                                            {{ $template['display_name'] ?? $template['name'] ?? 'Unnamed Template' }}
                                        </h4>
                                        <div class="flex items-center gap-2 shrink-0 ml-2">
                                            @if($hasVariables && !$hasMappings)
                                                <a href="{{ route('team.settings.whatsapp-templates.view', ['templateName' => $template['name'] ?? '']) }}" 
                                                   class="kt-btn kt-btn-sm kt-btn-warning kt-btn-outline"
                                                   title="Configure variable mappings">
                                                    <i class="ki-filled ki-setting-2 text-xs"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('team.settings.whatsapp-templates.view', ['templateName' => $template['name'] ?? '']) }}" 
                                               class="text-muted-foreground hover:text-primary transition-colors p-1 kt-btn kt-btn-sm kt-btn-icon kt-btn-outline"
                                               title="View template details">
                                                <i class="ki-filled ki-eye text-sm"></i>
                                            </a>

                                        </div>
                                    </div>
                                    
                                    <!-- Template Details -->
                                    <div class="text-sm text-secondary-foreground space-y-1 mb-4">
                                        
                                        <div>
                                            <span>
                                                {{ $template['approval_status'] ?? 'Unknown' }} - 
                                            </span>
                                            {{ $template['category'] ?? 'N/A' }} • {{ $template['language'] ?? 'N/A' }}
                                        </div>
                                        @if(($template['variable_present'] ?? '') === 'Yes')
                                            <div class="flex items-center gap-1">
                                                <i class="ki-filled ki-code text-xs"></i>
                                                <span>Contains variables</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Variable Mappings Display -->
                                    @if(!empty($mappings))
                                        <div class="mb-4 p-3 bg-info/5 border border-info/10 rounded-lg">
                                            <div class="flex items-center gap-1 mb-2">
                                                <i class="ki-filled ki-link text-xs text-info"></i>
                                                <span class="text-xs font-medium text-info">Variable Mappings</span>
                                            </div>
                                            <div class="space-y-1">
                                                @foreach($mappings as $whatsappVar => $systemVar)
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="font-mono text-primary">&#123;&#123;{{ $whatsappVar }}&#125;&#125;</span>
                                                        <i class="ki-filled ki-arrow-right text-muted-foreground text-xs"></i>
                                                        <span class="text-muted-foreground">{{ $systemVar }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($hasVariables)
                                        <div class="mb-4 p-3 bg-warning/5 border border-warning/10 rounded-lg">
                                            <div class="flex items-center gap-1">
                                                <i class="ki-filled ki-information-2 text-xs text-warning"></i>
                                                <span class="text-xs text-warning">Variables not mapped</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Body Preview -->
                                    @if(isset($template['body']) && !empty($template['body']))
                                        <div class="text-xs text-muted-foreground leading-relaxed mb-3">
                                            {{ Str::limit(strip_tags($template['body']), 80) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Footer -->
                                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                                        <div class="flex items-center gap-2">
                                            <span class="text-muted-foreground hover:text-primary transition-colors copy-text cursor-pointer hover:underline transition">{{ $template['name'] ?? 'N/A' }}</span>
                                            @if(isset($template['name']) && !empty($template['name']))
                                                <i class="ki-filled ki-copy text-xs"></i>
                                            @endif
                                        </div>
                                        @if(isset($template['created_at_utc']))
                                            <div>
                                                {{ \Carbon\Carbon::parse($template['created_at_utc'])->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Templates State -->
                    <div class="text-center py-8 text-muted-foreground">
                        <i class="ki-filled ki-file-sheet text-4xl mb-4"></i>
                        <p>No templates found for the active provider</p>
                    </div>
                @endif
            </x-team.card>
        </div>
    </x-slot>
</x-team.layout.app>
