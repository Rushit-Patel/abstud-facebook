@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Task Management', 'url' => route('team.task.index')],
    ['title' => $task->title, 'url' => route('team.task.show', $task)],
    ['title' => 'Edit']
];
@endphp

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet">
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    <style>
        .assignee-item, .monitor-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f9fafb;
        }
        .assignee-item:last-child, .monitor-item:last-child {
            margin-bottom: 0;
        }
        .recurring-fields {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #f8fafc;
        }
        .due-date-field {
            
        }
        .tagify {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }
        .dropzone {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            transition: border-color 0.3s ease;
        }
        .dropzone.dz-drag-hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .dropzone .dz-message {
            font-size: 1rem;
            color: #6b7280;
        }
        .dropzone .dz-preview {
            margin: 0.5rem;
        }
    </style>
@endpush

<x-team.layout.app title="Edit Task: {{ $task->title }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Task
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update task details and assignments
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.task.show', $task) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Task
                    </a>
                </div>
            </div>

            <form action="{{ route('team.task.update', $task) }}" method="POST" id="taskForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Hidden fields for file attachments -->
                <div id="hiddenAttachmentInputs"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-1 gap-7.5">
                    <!-- Main Task Details -->
                    <div class="lg:col-span-1 space-y-7.5">
                        <!-- Basic Task Information -->
                        <x-team.card title="Task Information" headerClass="">
                            <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                                <div class="col-span-1">
                                    <div class="grid gap-5">
                                        <x-team.forms.input 
                                            label="Task Title" 
                                            name="title" 
                                            value="{{ old('title', $task->title) }}"
                                            placeholder="Enter task title..."
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="grid gap-5">  
                                        <x-team.forms.textarea 
                                            name="description" 
                                            label="Task Description" 
                                            rows="4" 
                                            placeholder="Describe the task in detail..."
                                            value="{{ old('description', $task->description) }}"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-6">
                            
                                <!-- Category, Priority, Status -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-team.forms.select
                                            label="Category"
                                            name="category_id"
                                            :options="$categories"
                                            :selected="old('category_id', $task->category_id)"
                                            placeholder="Select Category"
                                        />
                                    </div>
                                    <div>
                                        <x-team.forms.select
                                            label="Priority"
                                            name="priority_id"
                                            :options="$priorities"
                                            :selected="old('priority_id', $task->priority_id)"
                                            placeholder="Select Priority"
                                        />
                                    </div>
                                    <div>
                                        <x-team.forms.select
                                            label="Status"
                                            name="status_id"
                                            :options="$statuses"
                                            :selected="old('status_id', $task->status_id)"
                                            placeholder="Select Status"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-1">
                                        <x-team.forms.radio
                                            name="is_recurring"
                                            label="Is Task Recurring?"
                                            :options="['0' => 'No', '1' => 'Yes']"
                                            :value="old('is_recurring', $task->is_recurring ? '1' : '0')"
                                            orientation="horizontal"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <x-team.forms.datepicker
                                            label="Start Date"
                                            name="start_date"
                                            value="{{ old('start_date', $task->start_date ? $task->start_date->format('d/m/Y H:i') : '') }}"
                                            enableTime="true"
                                            placeholder="Select start date and time"
                                        />
                                    </div>
                                    <div id="dueDateField" class="due-date-field col-span-1">
                                        <x-team.forms.datepicker
                                            label="Due Date"
                                            name="due_date"
                                            value="{{ old('due_date', $task->due_date ? $task->due_date->format('d/m/Y H:i') : '') }}"
                                            enableTime="true"
                                            placeholder="Select due date and time"
                                        />
                                    </div>
                                </div>

                                <div id="recurringFields" class="recurring-fields grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-4">
                                        <div>
                                            <x-team.forms.select
                                                label="Repeat Mode"
                                                name="repeat_mode"
                                                :options="[
                                                    'daily' => 'Daily',
                                                    'weekly' => 'Weekly', 
                                                    'monthly' => 'Monthly',
                                                    'yearly' => 'Yearly'
                                                ]"
                                                :selected="old('repeat_mode', $task->repeat_mode)"
                                                placeholder="Select repeat mode"
                                            />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-team.forms.input
                                                    label="Repeat Every"
                                                    name="repeat_interval"
                                                    type="number"
                                                    value="{{ old('repeat_interval', $task->repeat_interval ?? '1') }}"
                                                    min="1"
                                                    placeholder="1"
                                                />
                                            </div>
                                            <div>
                                                <x-team.forms.datepicker
                                                    label="Repeat Until"
                                                    name="repeat_until"
                                                    value="{{ old('repeat_until', $task->repeat_until ? $task->repeat_until->format('d/m/Y') : '') }}"
                                                    placeholder="Select end date"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @php
                                    $assignees = $task->assignments->where('role', 'assignee')->where('is_active', true)->pluck('user_id')->toArray();
                                    $watchers = $task->assignments->where('role', 'observer')->where('is_active', true)->pluck('user_id')->toArray();
                                @endphp
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Assignees -->
                                    <div class="col-span-2">
                                        <x-team.forms.select
                                            label="Assignees"
                                            name="assignees[]"
                                            :options="$users"
                                            :selected="$assignees"
                                            multiple
                                            required="true"
                                        />
                                    </div>
                                    <div class="col-span-2">
                                        <x-team.forms.select
                                            label="Watchers"
                                            name="watchers[]"
                                            :options="$users"
                                            :selected="$watchers"
                                            multiple
                                        />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                    <div class="col-span-1">
                                        <x-team.forms.input
                                            name="tags"
                                            label="Tag"
                                            id="tags"
                                            :value="old('tags', is_array($task->tags) ? json_encode(array_map(fn($tag) => ['value' => $tag], $task->tags)) : '')"
                                            placeholder="Add tags"
                                            searchable="true"
                                        />
                                    </div>
                                </div>
                            </div>
                        </x-team.card>

                        <!-- File Attachments -->
                        <x-team.card title="Attachments" headerClass="">
                            <div>
                                <div id="dropzone-upload" class="dropzone" action="{{ route('team.task.api.temp.store-file') }}">
                                    <div class="dz-message needsclick">
                                        <i class="ki-filled ki-file-up text-3xl text-gray-400 mb-2"></i>
                                        <h3 class="text-lg font-medium text-gray-700 mb-1">Drop files here or click to upload</h3>
                                        <p class="text-sm text-gray-500">You can upload multiple files. Maximum file size: 10MB per file.</p>
                                    </div>
                                </div>
                                
                                @if($task->attachments->count() > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium mb-2">Existing Attachments</h4>
                                        <div class="space-y-2">
                                            @foreach($task->attachments as $attachment)
                                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-file text-gray-400"></i>
                                                        <span class="text-sm">{{ $attachment->original_name }}</span>
                                                        <span class="text-xs text-gray-500">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                                    </div>
                                                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeExistingAttachment({{ $attachment->id }})">
                                                        <i class="ki-filled ki-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Assignment Section -->
                    <div class="lg:col-span-1 space-y-7.5 mb-5">
                        <!-- Form Actions -->
                        <x-team.card headerClass="hidden" bodyClass="pt-6">
                            <div class="flex justify-end gap-3">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-check"></i>
                                    Update Task
                                </button>
                                <a href="{{ route('team.task.show', $task) }}" class="kt-btn kt-btn-secondary">
                                    <i class="ki-filled ki-cross"></i>
                                    Cancel
                                </a>
                            </div>
                        </x-team.card>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
        <script>
            // Global variables for file upload handling
            let uploadedFiles = [];

            // Function to update hidden attachment fields (moved to global scope)
            function updateHiddenAttachmentFields() {
                const container = document.getElementById('hiddenAttachmentInputs');
                if (!container) {
                    console.error('Hidden attachment inputs container not found!');
                    return;
                }
                
                container.innerHTML = ''; // Clear existing inputs
                console.log('Updating hidden fields for', uploadedFiles.length, 'files:', uploadedFiles);
                
                uploadedFiles.forEach((fileData, index) => {
                    console.log(`Adding hidden fields for file ${index + 1}:`, fileData);
                    
                    // Add attachment path input
                    const pathInput = document.createElement('input');
                    pathInput.type = 'hidden';
                    pathInput.name = 'attachment_paths[]';
                    pathInput.value = fileData.path;
                    container.appendChild(pathInput);
                    
                    // Add attachment name input
                    const nameInput = document.createElement('input');
                    nameInput.type = 'hidden';
                    nameInput.name = 'attachment_names[]';
                    nameInput.value = fileData.name;
                    container.appendChild(nameInput);
                });
                
                // Log the final state of hidden inputs
                const allHiddenInputs = container.querySelectorAll('input[type="hidden"]');
                console.log('Total hidden inputs created:', allHiddenInputs.length);
                allHiddenInputs.forEach((input, index) => {
                    console.log(`Hidden input ${index + 1}: ${input.name} = ${input.value}`);
                });
            }

            $(document).ready(function() {
                // Disable Dropzone auto initialization
                Dropzone.autoDiscover = false;

                // Initialize Tagify for tags input
                const tagifyInput = document.querySelector('#tags');
                if (tagifyInput) {
                    // Parse existing tags
                    let existingTags = [];
                    try {
                        const tagValue = tagifyInput.value;
                        if (tagValue) {
                            existingTags = JSON.parse(tagValue);
                        }
                    } catch (e) {
                        // If not JSON, treat as comma-separated
                        existingTags = tagifyInput.value.split(',').map(tag => ({ value: tag.trim() })).filter(tag => tag.value);
                    }
                    
                    const tagify = new Tagify(tagifyInput, {
                        placeholder: 'Add tags...',
                        delimiters: ',| ',
                        maxTags: 10,
                        dropdown: {
                            enabled: 1,
                            maxItems: 20
                        }
                    });
                    
                    // Set existing tags
                    if (existingTags.length > 0) {
                        tagify.addTags(existingTags);
                    }
                }

                // Initialize Dropzone manually
                const dropzoneElement = document.querySelector("#dropzone-upload");
                let myDropzone;
                if (dropzoneElement) {
                    // Check if dropzone is already initialized
                    if (dropzoneElement.dropzone) {
                        console.log('Using existing Dropzone instance');
                        myDropzone = dropzoneElement.dropzone;                        
                    } else {
                        console.log('Creating new Dropzone instance');
                        myDropzone = new Dropzone(dropzoneElement, {
                            url: "/team/task/api/temp/store-file",
                            method: "POST",
                            paramName: "file",
                            autoProcessQueue: true,
                            uploadMultiple: false,
                            parallelUploads: 1,
                            maxFiles: 10,
                            maxFilesize: 10,
                            addRemoveLinks: true,
                            acceptedFiles: null,
                            timeout: 120000,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dictRemoveFile: "Remove"
                        });
                    }
                    // Add our custom event handlers
                    myDropzone.on("addedfile", function(file) {
                        
                    });

                    myDropzone.on("sending", function(file, xhr, formData) {
                       
                    });
                    
                    myDropzone.on("uploadprogress", function(file, progress) {
                        // Update progress bar or UI element
                    });
                    
                    myDropzone.on("success", function(file, response) {
                        // Validate response structure
                        if (response && response.success && response.path && response.name) {
                            // Store the server file path in the file object
                            file.serverPath = response.path;
                            uploadedFiles.push({
                                file: file,
                                path: response.path,
                                name: response.name
                            });
                            
                            // Add hidden fields for form submission
                            updateHiddenAttachmentFields();
                        } else {
                            myDropzone.removeFile(file);
                        }
                    });
                    
                    myDropzone.on("error", function(file, errorMessage, xhr) {
                        console.error('Upload error for file:', file.name);
                    });
                    
                    myDropzone.on("removedfile", function(file) {
                        console.log('File removed:', file.name);
                        
                        // Remove from uploaded files array
                        uploadedFiles = uploadedFiles.filter(f => f.file !== file);

                        // Update hidden fields
                        updateHiddenAttachmentFields();
                        
                        // Optionally call server to delete temporary file
                        if (file.serverPath) {
                            $.ajax({
                                url: '/team/task/api/temp/delete-file',
                                type: 'DELETE',
                                data: { path: file.serverPath },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log('Temporary file deleted:', file.serverPath);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error deleting temporary file:', error);
                                }
                            });
                        }
                    });
                    // Store reference globally
                    window.myDropzone = myDropzone;
                }
                
                // Handle recurring task toggle
                $(document).on('change', 'input[name="is_recurring"]', function() {
                    if ($(this).val() === '1') {
                        $('#recurringFields').slideDown();
                        $('#dueDateField').slideUp();
                    } else {
                        $('#recurringFields').slideUp();
                        $('#dueDateField').slideDown();
                    }
                });

                // Initialize recurring field visibility
                if ($('input[name="is_recurring"]:checked').val() === '1') {
                    $('#recurringFields').show();
                    $('#dueDateField').hide();
                } else {
                    $('#recurringFields').hide();
                    $('#dueDateField').show();
                }
                
                // Remove existing attachment function
                window.removeExistingAttachment = function(attachmentId) {
                    if (confirm('Are you sure you want to remove this attachment?')) {
                        $.ajax({
                            url: `/team/task/attachments/${attachmentId}`,
                            method: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Error removing attachment');
                                }
                            },
                            error: function() {
                                alert('Error removing attachment');
                            }
                        });
                    }
                };
                
                // Debug function to check current state
                window.debugDropzone = function() {
                    console.log('=== Dropzone Debug Info ===');
                    console.log('Dropzone element:', document.querySelector("#dropzone-upload"));
                    console.log('Dropzone instance:', window.myDropzone);
                    console.log('Uploaded files array:', uploadedFiles);
                    console.log('Hidden inputs container:', document.getElementById('hiddenAttachmentInputs'));
                    console.log('Current hidden inputs:', document.getElementById('hiddenAttachmentInputs').children);
                    console.log('=== End Debug Info ===');
                };
            });
        </script>
    @endpush
</x-team.layout.app>
