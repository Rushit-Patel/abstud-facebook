@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Email Templates', 'url' => route('team.settings.email-templates.index')],
    ['title' => 'Edit Template']
];
@endphp

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-editor {
            min-height: 200px;
            max-height: 400px;
        }
        .variable-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            cursor: pointer;
            margin: 2px;
            display: inline-block;
        }
        .variable-tag:hover {
            background: #bbdefb;
        }
        .system-template-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
        }
        .variable-button:hover {
            background-color: var(--kt-primary-light);
            border-color: var(--kt-primary);
            color: var(--kt-primary);
        }
    </style>
@endpush

<x-team.layout.app title="Edit Email Template" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Email Template
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        {{ $emailTemplate->name }}
                        @if($emailTemplate->is_system)
                            <span class="kt-badge kt-badge-outline kt-badge-warning">System Template</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.email-templates.show', $emailTemplate) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-eye"></i>
                        Preview
                    </a>
                    <a href="{{ route('team.settings.email-templates.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Templates
                    </a>
                </div>
            </div>

            @if($emailTemplate->is_system)
                <div class="system-template-warning p-4 rounded-lg mb-5">
                    <div class="flex items-center gap-3">
                        <i class="ki-filled ki-information-2 text-amber-600 text-xl"></i>
                        <div>
                            <h4 class="font-semibold text-amber-800">System Template Warning</h4>
                            <p class="text-sm text-amber-700">This is a system template used by the application. Modifications should be made carefully to avoid breaking functionality.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('team.settings.email-templates.update', $emailTemplate) }}" method="POST" id="emailTemplateForm">
                @csrf
                @method('PUT')
                
                <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5">
                    <!-- Main Content (Left Side) -->
                    <div class="lg:col-span-2">
                        <!-- Basic Information -->
                        <x-team.card title="Template Information" class="mb-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <x-team.forms.input
                                        name="name"
                                        label="Template Name"
                                        type="text"
                                        placeholder="Enter template name"
                                        :value="old('name', $emailTemplate->name)" 
                                        required 
                                        autocomplete="off"
                                        help="A descriptive name for your email template" />
                                </div>
                                <div>
                                    <x-team.forms.input
                                        name="slug"
                                        label="Template Slug"
                                        type="text"
                                        placeholder="Enter template slug"
                                        :value="old('slug', $emailTemplate->slug)"
                                        required 
                                        autocomplete="off"
                                        :readonly="$emailTemplate->is_system"
                                        help="Used to identify the template in code (lowercase, hyphens)" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-team.forms.input
                                        name="subject"
                                        label="Email Subject"
                                        type="text"
                                        placeholder="Enter email subject line"
                                        :value="old('subject', $emailTemplate->subject)"
                                        required 
                                        autocomplete="off"
                                        help="Subject line that recipients will see" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-team.forms.textarea
                                        name="description"
                                        label="Description"
                                        id="description"
                                        rows="3"
                                        placeholder="Describe the purpose of this email template"
                                        :value="old('description', $emailTemplate->description)"
                                        help="Optional description explaining when this template is used" />
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Email Content -->
                        <x-team.card title="Email Content" class="mb-5">
                            <div class="space-y-5">
                                <!-- HTML Template -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        HTML Template <span class="text-red-500">*</span>
                                    </label>
                                    <div id="html-editor" style="height: 300px;"></div>
                                    <textarea name="html_template" id="html_template" class="hidden" required>{{ old('html_template', $emailTemplate->html_template) }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Rich HTML content for the email body</p>
                                </div>

                                <!-- Text Template -->
                                <div>
                                    <x-team.forms.textarea
                                        name="text_template"
                                        label="Plain Text Template (Optional)"
                                        rows="8"
                                        id="text_template"
                                        placeholder="Enter plain text version of the email content..."
                                        :value="old('text_template', $emailTemplate->text_template)"
                                        help="Fallback text version for email clients that don't support HTML" />
                                </div>
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Sidebar (Right Side) -->
                    <div class="lg:col-span-1">
                        <!-- Template Settings -->
                        <x-team.card title="Template Settings" class="mb-5">
                            <div class="space-y-4">
                                <div>
                                    <x-team.forms.input
                                        name="category"
                                        label="Category"
                                        type="text"
                                        placeholder="e.g., User, System, Marketing"
                                        :value="old('category', $emailTemplate->category)"
                                        autocomplete="off"
                                        help="Categorize your template for better organization" />
                                </div>
                                
                                <x-team.forms.checkbox 
                                    label="Status"
                                    name="is_active"
                                    checkboxLabel="Active (Template is ready to use)"
                                    :checked="$emailTemplate->is_active"
                                    style="default"
                                    size="sm"
                                />
                            </div>
                        </x-team.card>

                        <!-- Template Statistics -->
                        <x-team.card title="Template Statistics" class="mb-5">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <span class="text-sm font-medium">{{ $emailTemplate->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Last Modified:</span>
                                    <span class="text-sm font-medium">{{ $emailTemplate->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Type:</span>
                                    <span class="kt-badge kt-badge-outline {{ $emailTemplate->is_system ? 'kt-badge-info' : 'kt-badge-primary' }}">
                                        {{ $emailTemplate->is_system ? 'System' : 'Custom' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="kt-badge {{ $emailTemplate->is_active ? 'kt-badge-success' : 'kt-badge-secondary' }}">
                                        {{ $emailTemplate->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Variables Helper -->
                        <x-team.card title="Available Variables" class="mb-5">
                            <div class="mb-4">
                                <h4 class="font-medium text-sm mb-2">Available System Variables</h4>
                                <p class="text-xs text-muted-foreground mb-3">
                                    Click on a variable to insert it into your template. These variables will be automatically replaced with actual data when emails are sent.
                                </p>
                            </div>

                            <div class="space-y-4">
                                @foreach($systemVariables as $category => $variables)
                                    <div>
                                        <h5 class="font-medium text-sm mb-2 text-primary">{{ $category }}</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($variables as $variable => $description)
                                                <button type="button" 
                                                        class="variable-button kt-btn kt-btn-sm kt-btn-outline kt-btn-secondary"
                                                        onclick="insertVariable('{{ $variable }}')"
                                                        title="{{ $description }}">
                                                    <span class="font-mono">{{ $variable }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-team.card>

                        <!-- Current Variables -->
                        @php
                            // Check if we have variables to display
                            $hasVariables = false;
                            if (old('variables')) {
                                $hasVariables = is_array(old('variables')) && count(array_filter(old('variables'), fn($v) => !empty(trim($v)))) > 0;
                            } elseif ($emailTemplate->variables) {
                                $hasVariables = is_array($emailTemplate->variables) && count($emailTemplate->variables) > 0;
                            }
                        @endphp
                        @if($hasVariables)
                            <x-team.card title="Current Variables" class="mb-5">
                                <div class="space-y-3">
                                    <p class="text-sm text-gray-600">Variables defined for this template:</p>
                                    <div id="variables-container">
                                        @php
                                            // Handle old form data vs database model data
                                            if (old('variables')) {
                                                // Form submission data - already an array
                                                $currentVariables = old('variables');
                                            } else {
                                                // Fresh load from database
                                                $currentVariables = $emailTemplate->variables ?? [];
                                            }
                                            
                                            // Ensure it's always an array
                                            if (!is_array($currentVariables)) {
                                                $currentVariables = [];
                                            }
                                        @endphp
                                        @if(is_array($currentVariables) && count($currentVariables) > 0)
                                            @foreach($currentVariables as $index => $variable)
                                                @if(!empty($variable))
                                                    <div class="variable-input flex gap-2 mb-2">
                                                        <input type="text" 
                                                               name="variables[]" 
                                                               placeholder="variable.name"
                                                               class="kt-input flex-1"
                                                               value="{{ is_string($variable) ? $variable : '' }}">
                                                        <button type="button" onclick="removeVariable(this)" class="kt-btn kt-btn-sm kt-btn-danger kt-btn-icon">
                                                            <i class="ki-filled ki-cross"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addVariable()" class="kt-btn kt-btn-sm kt-btn-secondary">
                                        <i class="ki-filled ki-plus"></i>
                                        Add Variable
                                    </button>
                                </div>
                            </x-team.card>
                        @else
                            <!-- Variables Input -->
                            <x-team.card title="Custom Variables" class="mb-5">
                                <div class="space-y-3">
                                    <p class="text-sm text-gray-600">Define custom variables for this template:</p>
                                    <div id="variables-container">
                                        {{-- <div class="variable-input flex gap-2 mb-2">
                                            <input type="text" 
                                                   name="variables[]" 
                                                   placeholder="variable.name"
                                                   class="kt-input flex-1"
                                                   value="">
                                            <button type="button" onclick="removeVariable(this)" class="kt-btn kt-btn-sm kt-btn-danger kt-btn-icon">
                                                <i class="ki-filled ki-cross"></i>
                                            </button>
                                        </div> --}}
                                    </div>
                                    <button type="button" onclick="addVariable()" class="kt-btn kt-btn-sm kt-btn-secondary">
                                        <i class="ki-filled ki-plus"></i>
                                        Add Variable
                                    </button>
                                </div>
                            </x-team.card>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <div class="flex align-items-end gap-3">
                        <a href="{{ route('team.settings.email-templates.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-sm kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Email Template
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            // Global quill instance
            let quill;
            
            $(document).ready(function() {
                // Initialize Quill editor
                quill = new Quill('#html-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Enter your email template content here...'
                });

                // Set initial content
                const initialContent = $('#html_template').val();
                if (initialContent) {
                    quill.root.innerHTML = initialContent;
                }

                // Sync Quill content with hidden textarea
                quill.on('text-change', function() {
                    $('#html_template').val(quill.root.innerHTML);
                });

                // Auto-generate slug from name (only if not system template)
                const isSystemTemplate = {{ $emailTemplate->is_system ? 'true' : 'false' }};
                if (!isSystemTemplate) {
                    $('input[name="name"]').on('input', function() {
                        const name = $(this).val();
                        const slug = name.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .trim()
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-');
                        $('input[name="slug"]').val(slug);
                    });
                }

                // Form validation before submit
                $('#emailTemplateForm').on('submit', function(e) {
                    const htmlContent = quill.root.innerHTML.trim();
                    if (htmlContent === '<p><br></p>' || htmlContent === '') {
                        e.preventDefault();
                        alert('Please enter the HTML template content.');
                        return false;
                    }
                    
                    // Update hidden field
                    $('#html_template').val(htmlContent);
                });
            });

            // Variable management functions (called from Blade component)
            function addVariable() {
                const container = $('#variables-container');
                const variableInput = $(`
                    <div class="variable-input flex gap-2 mb-2">
                        <input type="text" 
                               name="variables[]" 
                               placeholder="variable.name"
                               class="kt-input flex-1">
                        <button type="button" onclick="removeVariable(this)" class="kt-btn kt-btn-sm kt-btn-danger kt-btn-icon">
                            <i class="ki-filled ki-cross"></i>
                        </button>
                    </div>
                `);
                container.append(variableInput);
            }

            function removeVariable(button) {
                $(button).closest('.variable-input').remove();
            }

            function insertVariable(variable) {
                // Insert variable into the current cursor position in Quill editor
                if (quill) {
                    const range = quill.getSelection();
                    if (range) {
                        quill.insertText(range.index, variable);
                    } else {
                        // If no selection, append to the end
                        const length = quill.getLength();
                        quill.insertText(length - 1, ' ' + variable + ' ');
                    }
                }
                
                // Also add to subject if it's currently focused
                const activeElement = $(document.activeElement);
                if (activeElement.attr('name') === 'subject') {
                    const input = activeElement[0];
                    const startPos = input.selectionStart;
                    const endPos = input.selectionEnd;
                    const currentValue = activeElement.val();
                    activeElement.val(currentValue.substring(0, startPos) + variable + currentValue.substring(endPos));
                    input.setSelectionRange(startPos + variable.length, startPos + variable.length);
                }
            }
        </script>
    @endpush
</x-team.layout.app>
