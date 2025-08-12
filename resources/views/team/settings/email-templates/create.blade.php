@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Email Templates', 'url' => route('team.settings.email-templates.index')],
    ['title' => 'Add Email Template']
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
        .variable-button:hover {
            background-color: var(--kt-primary-light);
            border-color: var(--kt-primary);
            color: var(--kt-primary);
        }
    </style>
@endpush

<x-team.layout.app title="Add Email Template" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New Email Template
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new email template for system communications
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.email-templates.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Templates
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.email-templates.store') }}" method="POST" id="emailTemplateForm">
                @csrf
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
                                        :value="old('name')"
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
                                        :value="old('slug')"
                                        required
                                        autocomplete="off"
                                        help="Used to identify the template in code (lowercase, hyphens)" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-team.forms.input
                                        name="subject"
                                        label="Email Subject"
                                        type="text"
                                        placeholder="Enter email subject line"
                                        :value="old('subject')"
                                        required
                                        autocomplete="off"
                                        help="Subject line that recipients will see" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-team.forms.textarea
                                        id="description"
                                        name="description"
                                        label="Description"
                                        rows="3"
                                        placeholder="Describe the purpose of this email template"
                                        :value="old('description')"
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
                                    <textarea name="html_template" id="html_template" class="hidden" required>{{ old('html_template') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Rich HTML content for the email body</p>
                                </div>

                                <!-- Text Template -->
                                <div>
                                    <x-team.forms.textarea
                                        id="text_template"
                                        name="text_template"
                                        label="Plain Text Template (Optional)"
                                        rows="8"
                                        placeholder="Enter plain text version of the email content..."
                                        :value="old('text_template')"
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
                                        :value="old('category')"
                                        autocomplete="off"
                                        help="Categorize your template for better organization" />
                                </div>

                                <div>
                                    <label class="kt-form-label text-mono">Status</label>
                                    <label class="kt-label">
                                        <input class="kt-checkbox kt-checkbox-sm"
                                            name="is_active"
                                            type="checkbox"
                                            value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}
                                        />
                                        <span class="kt-checkbox-label">
                                            Active (Template is ready to use)
                                        </span>
                                    </label>
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

                        <!-- Variables Input -->
                        <x-team.card title="Custom Variables" class="mb-5">
                            <div class="space-y-3">
                                <p class="text-sm text-gray-600">Define custom variables for this template:</p>
                                <div id="variables-container">
                                    @php
                                        $oldVariables = old('variables', ['']);
                                        if (!is_array($oldVariables)) {
                                            $oldVariables = [''];
                                        }
                                        // Filter out empty values
                                        $oldVariables = array_filter($oldVariables, fn($v) => !empty(trim($v)));
                                        if (empty($oldVariables)) {
                                            $oldVariables = [''];
                                        }
                                    @endphp
                                    @if(is_array($oldVariables) && count($oldVariables) > 0)
                                        @foreach($oldVariables as $index => $variable)
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
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" onclick="addVariable()" class="kt-btn kt-btn-sm kt-btn-secondary">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Variable
                                </button>
                            </div>
                        </x-team.card>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="mt-7.5">
                    <x-team.card title="Live Preview" class="mb-5">
                        <div class="space-y-4">
                            <p class="text-sm text-muted-foreground">
                                Preview how your email will look with sample data. Variables will be replaced with actual values.
                            </p>
                            
                            <div class="border border-border rounded-lg p-4 bg-muted/30">
                                <div class="mb-3">
                                    <strong class="text-sm">Subject:</strong>
                                    <div id="preview-subject" class="mt-1 text-sm">{{ old('subject', 'Enter subject above to see preview') }}</div>
                                </div>
                                
                                <div>
                                    <strong class="text-sm">Content:</strong>
                                    <div id="preview-content" class="mt-1 text-sm leading-relaxed border-t border-border pt-3">
                                        Enter content above to see preview with variables replaced...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-team.card>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <a href="{{ route('team.settings.email-templates.index') }}" class="kt-btn kt-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Create Email Template
                    </button>
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

                // Sync Quill content with hidden textarea
                quill.on('text-change', function() {
                    $('#html_template').val(quill.root.innerHTML);
                    updatePreview();
                });

                // Sample values for preview
                const sampleValues = @json($sampleValues);

                // Update preview functionality
                function updatePreview() {
                    const subject = $('input[name="subject"]').val();
                    const htmlContent = quill.root.innerHTML;

                    // Replace variables with sample values in subject
                    let previewSubject = replaceVariables(subject, sampleValues);
                    $('#preview-subject').text(previewSubject || 'Enter subject above to see preview');

                    // Replace variables with sample values in content
                    let previewContent = replaceVariables(htmlContent, sampleValues);
                    $('#preview-content').html(previewContent || 'Enter content above to see preview with variables replaced...');
                }

                // Replace variables with sample values
                function replaceVariables(content, variables) {
                    let result = content;
                    $.each(variables, function(key, value) {
                        const regex = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g');
                        result = result.replace(regex, value);
                    });
                    return result;
                }

                // Listen for subject changes
                $('input[name="subject"]').on('input', updatePreview);

                // Set initial content if editing
                const initialContent = $('#html_template').val();
                if (initialContent) {
                    quill.root.innerHTML = initialContent;
                }

                // Auto-generate slug from name
                $('input[name="name"]').on('input', function() {
                    const name = $(this).val();
                    const slug = name.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim()
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    $('input[name="slug"]').val(slug);
                });

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
