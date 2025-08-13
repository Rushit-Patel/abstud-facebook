@extends('team.layouts.app')

@section('title', 'System Variables Reference')

@section('content')
<div class="container-fluid">
    <div class="grid">
        <!-- Header -->
        <div class="card mb-5 lg:mb-7.5">
            <div class="card-header">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col">
                        <h1 class="text-xl font-semibold text-gray-900">System Variables Reference</h1>
                        <p class="text-sm text-gray-600">Complete guide to all available template variables in your system</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="btn btn-info" id="testVariablesBtn">
                        <i class="ki-filled ki-flask"></i>
                        Test Variables
                    </button>
                    <button type="button" class="btn btn-secondary" id="exportVariablesBtn">
                        <i class="ki-filled ki-file-down"></i>
                        Export Reference
                    </button>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-5 lg:mb-7.5">
            <div class="card-body">
                <div class="grid lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search Variables</label>
                        <input type="text" id="variableSearch" class="input" 
                               placeholder="Search by variable name, category, or description...">
                    </div>
                    <div>
                        <label class="form-label">Filter by Category</label>
                        <select id="categoryFilter" class="select">
                            <option value="">All Categories</option>
                            @foreach($systemVariables as $category => $variables)
                                <option value="{{ $category }}">{{ $category }} ({{ count($variables) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variables Grid -->
        <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
            @foreach($systemVariables as $category => $variables)
                <div class="card variable-category" data-category="{{ $category }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $category }}
                            <span class="badge badge-sm badge-light ml-2">{{ count($variables) }}</span>
                        </h3>
                        <button type="button" class="btn btn-sm btn-light copy-category-btn" 
                                data-category="{{ $category }}">
                            <i class="ki-filled ki-copy"></i>
                            Copy All
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3">
                            @foreach($variables as $varKey => $varDescription)
                                <div class="variable-item border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors"
                                     data-variable="{{ $varKey }}" data-description="{{ $varDescription }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <code class="text-purple-600 font-mono text-sm">{{ $varKey }}</code>
                                                <button type="button" class="btn btn-xs btn-light copy-var-btn" 
                                                        data-variable="{{ $varKey }}" title="Copy variable">
                                                    <i class="ki-filled ki-copy text-xs"></i>
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $varDescription }}</p>
                                            
                                            <!-- Sample Value -->
                                            @if(isset($sampleValues[$varKey]))
                                                <div class="mt-2 text-xs">
                                                    <span class="text-gray-500">Sample:</span>
                                                    <span class="font-mono text-green-600 ml-1">{{ Str::limit($sampleValues[$varKey], 50) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button type="button" class="btn btn-xs btn-light preview-var-btn" 
                                                    data-variable="{{ $varKey }}" 
                                                    data-sample="{{ $sampleValues[$varKey] ?? 'N/A' }}"
                                                    title="Preview">
                                                <i class="ki-filled ki-eye text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Usage Examples -->
        <div class="card mt-5 lg:mt-7.5">
            <div class="card-header">
                <h3 class="card-title">Usage Examples</h3>
            </div>
            <div class="card-body">
                <div class="grid lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-sm text-gray-900 mb-3">Email Templates</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 overflow-x-auto">Hello {{client_name}},

Thank you for your interest in {{purpose}}.
Your lead ID is: {{lead_id}}

Best regards,
{{assigned_agent}}
{{company_name}}</pre>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-gray-900 mb-3">WhatsApp Templates</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 overflow-x-auto">Hi {{first_name}}! 👋

Your application for {{country}} is being processed.
Status: {{lead_status}}

Contact us: {{company_email}}
Visit: {{app_url}}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card mt-5 lg:mt-7.5">
            <div class="card-header">
                <h3 class="card-title">Variable Statistics</h3>
            </div>
            <div class="card-body">
                <div class="grid lg:grid-cols-4 gap-4">
                    @php 
                        $totalVars = collect($systemVariables)->sum(fn($vars) => count($vars));
                        $clientVars = count($systemVariables['Client Information'] ?? []);
                        $leadVars = count($systemVariables['Lead Information'] ?? []);
                        $systemVars = count($systemVariables['System Information'] ?? []);
                    @endphp
                    
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalVars }}</div>
                        <div class="text-sm text-blue-800">Total Variables</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $clientVars }}</div>
                        <div class="text-sm text-green-800">Client Variables</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $leadVars }}</div>
                        <div class="text-sm text-purple-800">Lead Variables</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $systemVars }}</div>
                        <div class="text-sm text-orange-800">System Variables</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variable Preview Modal -->
<div class="modal" data-modal="true" id="variable_preview_modal">
    <div class="modal-content max-w-lg">
        <div class="modal-header">
            <h3 class="modal-title">Variable Preview</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="previewVariableContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-dismiss="true">Close</button>
        </div>
    </div>
</div>

<!-- Test Variables Modal -->
<div class="modal" data-modal="true" id="test_variables_modal">
    <div class="modal-content max-w-2xl">
        <div class="modal-header">
            <h3 class="modal-title">Test Variables</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="testVariablesForm">
                <div class="mb-4">
                    <label class="form-label">Enter Template Content</label>
                    <textarea id="testContent" class="input" rows="6" 
                              placeholder="Enter your template with variables like {{client_name}}, {{lead_status}}, etc.">Hello {{client_name}},

Thank you for showing interest in {{purpose}}.
Your lead has been assigned to {{assigned_agent}}.

Best regards,
{{company_name}}</textarea>
                </div>
                
                <div class="mb-4">
                    <button type="button" class="btn btn-primary" id="previewTestBtn">
                        <i class="ki-filled ki-eye"></i>
                        Preview with Sample Values
                    </button>
                </div>
                
                <div id="testPreviewResult" class="bg-gray-50 rounded-lg p-4 hidden">
                    <h4 class="font-semibold text-sm mb-2">Preview Result:</h4>
                    <div id="testPreviewContent" class="text-sm text-gray-700"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-dismiss="true">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sampleValues = @json($sampleValues);
    
    // Search functionality
    document.getElementById('variableSearch').addEventListener('input', function() {
        filterVariables();
    });
    
    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        filterVariables();
    });
    
    // Copy variable
    document.addEventListener('click', function(e) {
        if (e.target.closest('.copy-var-btn')) {
            const variable = e.target.closest('.copy-var-btn').dataset.variable;
            copyToClipboard('{{' + variable + '}}');
            showNotification('Variable copied to clipboard: {{' + variable + '}}');
        }
    });
    
    // Copy category
    document.addEventListener('click', function(e) {
        if (e.target.closest('.copy-category-btn')) {
            const category = e.target.closest('.copy-category-btn').dataset.category;
            const categoryElement = document.querySelector(`.variable-category[data-category="${category}"]`);
            const variables = categoryElement.querySelectorAll('[data-variable]');
            const varList = Array.from(variables).map(el => '{{' + el.dataset.variable + '}}').join('\n');
            copyToClipboard(varList);
            showNotification(`All variables from ${category} copied to clipboard`);
        }
    });
    
    // Preview variable
    document.addEventListener('click', function(e) {
        if (e.target.closest('.preview-var-btn')) {
            const btn = e.target.closest('.preview-var-btn');
            const variable = btn.dataset.variable;
            const sample = btn.dataset.sample;
            const description = btn.closest('.variable-item').dataset.description;
            
            showVariablePreview(variable, description, sample);
        }
    });
    
    // Test variables
    document.getElementById('testVariablesBtn').addEventListener('click', function() {
        document.querySelector('#test_variables_modal').classList.add('open');
    });
    
    // Preview test
    document.getElementById('previewTestBtn').addEventListener('click', function() {
        const content = document.getElementById('testContent').value;
        if (content) {
            previewTestContent(content);
        }
    });
    
    // Export variables
    document.getElementById('exportVariablesBtn').addEventListener('click', function() {
        exportVariablesReference();
    });
    
    function filterVariables() {
        const searchTerm = document.getElementById('variableSearch').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        document.querySelectorAll('.variable-category').forEach(category => {
            const categoryName = category.dataset.category;
            let categoryVisible = false;
            
            // Check category filter
            if (categoryFilter && categoryFilter !== categoryName) {
                category.style.display = 'none';
                return;
            }
            
            // Check search in variables
            category.querySelectorAll('.variable-item').forEach(item => {
                const variable = item.dataset.variable.toLowerCase();
                const description = item.dataset.description.toLowerCase();
                const matches = !searchTerm || 
                               variable.includes(searchTerm) || 
                               description.includes(searchTerm) ||
                               categoryName.toLowerCase().includes(searchTerm);
                
                item.style.display = matches ? 'block' : 'none';
                if (matches) categoryVisible = true;
            });
            
            category.style.display = categoryVisible ? 'block' : 'none';
        });
    }
    
    function showVariablePreview(variable, description, sample) {
        const content = `
            <div class="space-y-4">
                <div>
                    <label class="form-label">Variable</label>
                    <div class="bg-gray-50 rounded p-2">
                        <code class="text-purple-600">{{${variable}}}</code>
                    </div>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <p class="text-sm text-gray-600">${description}</p>
                </div>
                <div>
                    <label class="form-label">Sample Value</label>
                    <div class="bg-green-50 rounded p-2">
                        <span class="text-green-700">${sample}</span>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('previewVariableContent').innerHTML = content;
        document.querySelector('#variable_preview_modal').classList.add('open');
    }
    
    function previewTestContent(content) {
        // Replace variables with sample values
        let previewContent = content;
        Object.keys(sampleValues).forEach(variable => {
            const regex = new RegExp('\\{\\{\\s*' + variable + '\\s*\\}\\}', 'g');
            previewContent = previewContent.replace(regex, sampleValues[variable]);
        });
        
        document.getElementById('testPreviewContent').innerHTML = previewContent.replace(/\n/g, '<br>');
        document.getElementById('testPreviewResult').classList.remove('hidden');
    }
    
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        });
    }
    
    function showNotification(message) {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    function exportVariablesReference() {
        const variables = @json($systemVariables);
        let csvContent = 'Category,Variable,Description,Sample Value\n';
        
        Object.keys(variables).forEach(category => {
            Object.keys(variables[category]).forEach(variable => {
                const description = variables[category][variable];
                const sample = sampleValues[variable] || 'N/A';
                csvContent += `"${category}","${variable}","${description}","${sample}"\n`;
            });
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'system_variables_reference.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showNotification('Variables reference exported successfully!');
    }
});
</script>
@endpush
