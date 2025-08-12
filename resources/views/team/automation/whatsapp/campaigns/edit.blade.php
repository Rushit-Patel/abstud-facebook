@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'WhatsApp Campaigns', 'url' => route('team.automation.whatsapp.campaigns.index')],
        ['title' => 'Edit Campaign']
    ];
@endphp

<x-team.layout.app title="Edit WhatsApp Campaign - {{ $campaign->name }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit WhatsApp Campaign
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update your automated WhatsApp campaign settings
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.whatsapp.campaigns.show', $campaign) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Campaign
                    </a>
                </div>
            </div>

            <form action="{{ route('team.automation.whatsapp.campaigns.update', $campaign) }}" method="POST" id="campaignForm" class="form">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
                    
                    {{-- Main Form --}}
                    <div class="xl:col-span-2">
                        <div class="flex flex-col gap-5">
                            
                            {{-- Campaign Details --}}
                            <x-team.card title="Campaign Details" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    
                                    {{-- Campaign Name --}}
                                    <div class="lg:col-span-2">
                                        <x-team.forms.input
                                            name="name"
                                            label="Campaign Name"
                                            placeholder="Enter campaign name"
                                            :value="old('name', $campaign->name)"
                                            required="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Description --}}
                                    <div class="lg:col-span-2">
                                        <x-team.forms.textarea
                                            name="description"
                                            label="Description"
                                            placeholder="Describe this campaign"
                                            :value="old('description', $campaign->description)"
                                            rows="3"
                                            class="w-full" />
                                    </div>

                                    {{-- Delay Minutes --}}
                                    <div>
                                        <x-team.forms.input
                                            name="delay_minutes"
                                            label="Delay (minutes)"
                                            type="number"
                                            placeholder="5"
                                            :value="old('delay_minutes', $campaign->delay_minutes ?? 5)"
                                            min="0"
                                            max="1440"
                                            helpText="Delay in minutes before sending messages"
                                            class="w-full" />
                                    </div>

                                    {{-- Priority --}}
                                    <div>
                                        @php
                                            $priorityOptions = collect([
                                                1 => 'High',
                                                2 => 'Medium', 
                                                3 => 'Low'
                                            ]);
                                        @endphp
                                        <x-team.forms.select
                                            name="priority"
                                            label="Priority"
                                            :options="$priorityOptions"
                                            :selected="old('priority', $campaign->priority ?? 2)"
                                            class="w-full" />
                                    </div>

                                    {{-- Retry Attempts --}}
                                    <div>
                                        <x-team.forms.input
                                            name="retry_attempts"
                                            label="Retry Attempts"
                                            type="number"
                                            placeholder="3"
                                            :value="old('retry_attempts', $campaign->retry_attempts ?? 3)"
                                            min="0"
                                            max="5"
                                            helpText="Number of retry attempts for failed messages"
                                            class="w-full" />
                                    </div>

                                    {{-- Active Status --}}
                                    <div>
                                        {{-- Hidden input to ensure unchecked sends false --}}
                                        <input type="hidden" name="is_active" value="0">
                                        <x-team.forms.checkbox
                                            name="is_active"
                                            label="Active Campaign"
                                            :checked="old('is_active', $campaign->is_active ?? true)"
                                            value="1"
                                            style="default"
                                            />
                                    </div>

                                </div>
                            </x-team.card>

                            {{-- WhatsApp Configuration --}}
                            <x-team.card title="WhatsApp Configuration" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    {{-- Message Type --}}
                                    <div>
                                        @php
                                            $messageTypeOptions = collect([
                                                'text' => 'Text Message',
                                                'template' => 'Template Message',
                                                'media' => 'Media Message'
                                            ]);
                                        @endphp
                                        <x-team.forms.select
                                            name="message_type"
                                            label="Message Type"
                                            :options="$messageTypeOptions"
                                            :selected="old('message_type', $campaign->message_type)"
                                            required="true"
                                            id="messageType"
                                            class="w-full" />
                                    </div>

                                    {{-- Text Message Content --}}
                                    <div id="textMessageContent" class="lg:col-span-2" style="display: {{ old('message_type', $campaign->message_type) === 'text' ? 'block' : 'none' }};">
                                        <x-team.forms.textarea
                                            name="message_content"
                                            label="Message Content"
                                            placeholder="Enter your WhatsApp message here..."
                                            :value="old('message_content', $campaign->message_content)"
                                            rows="4"
                                            helpText="You can use placeholders like {name}, {phone}, etc."
                                            class="w-full" />
                                    </div>

                                    {{-- Template Configuration --}}
                                    <div id="templateConfiguration" class="lg:col-span-2" style="display: {{ old('message_type', $campaign->message_type) === 'template' ? 'block' : 'none' }};">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                            <div>
                                                @php
                                                    $templateOptions = collect(['' => 'Select Template'])
                                                        ->merge($templates->mapWithKeys(fn($template) => [$template => $template]));
                                                @endphp
                                                <x-team.forms.select
                                                    name="template_name"
                                                    label="Template Name"
                                                    :options="$templateOptions"
                                                    :selected="old('template_name', $campaign->template_name)"
                                                    searchable="true"
                                                    id="templateName"
                                                    class="w-full" />
                                            </div>
                                            <div>
                                                <x-team.forms.input
                                                    name="template_language"
                                                    label="Template Language"
                                                    placeholder="en_US"
                                                    :value="old('template_language', $campaign->template_language ?? 'en_US')"
                                                    class="w-full" />
                                            </div>
                                        </div>
                                        
                                        <div class="mt-5">
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                <div class="flex items-start">
                                                    <i class="ki-filled ki-information-3 text-blue-600 text-lg mr-3 mt-0.5"></i>
                                                    <div>
                                                        <h6 class="text-blue-800 font-medium mb-1">Auto-Mapped Variables</h6>
                                                        <p class="text-blue-700 text-sm">
                                                            Template variables are automatically mapped from system variables. 
                                                            No manual configuration needed - the system will handle variable replacement automatically.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </x-team.card>

                            {{-- Campaign Execution --}}
                            <x-team.card title="Campaign Execution" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    
                                    {{-- Execution Type --}}
                                    <div class="lg:col-span-2">
                                        @php
                                            $executionTypes = collect([
                                                'one_time' => 'One Time Campaign',
                                                'automation' => 'Automation Campaign'
                                            ]);
                                        @endphp
                                        <x-team.forms.select
                                            name="execution_type"
                                            label="Campaign Type"
                                            :options="$executionTypes"
                                            :selected="old('execution_type', $campaign->execution_type ?? 'automation')"
                                            required="true"
                                            id="executionType"
                                            class="w-full" />
                                    </div>

                                    {{-- One Time Scheduling --}}
                                    <div id="oneTimeSettings" class="lg:col-span-2" style="display: {{ old('execution_type', $campaign->execution_type) === 'one_time' ? 'block' : 'none' }};">
                                        <x-team.forms.datepicker
                                            name="scheduled_at"
                                            label="Schedule Date & Time"
                                            placeholder="Select date and time"
                                            :value="old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d H:i') : '')"
                                            class="w-full"
                                            minDate="today"
                                            minTime="{{ now()->format('H:i') }}"
                                            enableTime="true"
                                            helpText="When should this campaign run once"
                                            />
                                    </div>

                                    {{-- Automation Scheduling --}}
                                    <div id="automationSettings" class="lg:col-span-2" style="display: {{ old('execution_type', $campaign->execution_type) === 'automation' ? 'block' : 'none' }};">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                            <div>
                                                @php
                                                    $scheduleFrequencies = collect([
                                                        'daily' => 'Daily',
                                                        'weekly' => 'Weekly',
                                                        'monthly' => 'Monthly'
                                                    ]);
                                                @endphp
                                                <x-team.forms.select
                                                    name="schedule_frequency"
                                                    label="Schedule Frequency"
                                                    :options="$scheduleFrequencies"
                                                    :selected="old('schedule_frequency', $campaign->schedule_frequency ?? 'daily')"
                                                    id="scheduleFrequency"
                                                    class="w-full" />
                                            </div>
                                            
                                            {{-- Schedule Configuration --}}
                                            <div id="scheduleConfig">
                                                @php
                                                    $scheduleConfig = old('schedule_config', $campaign->schedule_config ?? []);
                                                    $currentFreq = old('schedule_frequency', $campaign->schedule_frequency ?? 'daily');
                                                @endphp
                                                
                                                {{-- Daily Config --}}
                                                <div id="dailyConfig" class="schedule-config" style="display: {{ $currentFreq === 'daily' ? 'block' : 'none' }};">
                                                    <label class="form-label">Time of Day</label>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour (0-23)"
                                                            :value="old('schedule_config.hour', $scheduleConfig['hour'] ?? 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', $scheduleConfig['minute'] ?? 0)"
                                                            min="0"
                                                            max="59"
                                                            class="w-full" />
                                                    </div>
                                                </div>

                                                {{-- Weekly Config --}}
                                                <div id="weeklyConfig" class="schedule-config" style="display: {{ $currentFreq === 'weekly' ? 'block' : 'none' }};">
                                                    <label class="form-label">Day of Week & Time</label>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        @php
                                                            $daysOfWeek = collect([
                                                                1 => 'Monday',
                                                                2 => 'Tuesday',
                                                                3 => 'Wednesday',
                                                                4 => 'Thursday',
                                                                5 => 'Friday',
                                                                6 => 'Saturday',
                                                                0 => 'Sunday'
                                                            ]);
                                                        @endphp
                                                        <x-team.forms.select
                                                            name="schedule_config[day_of_week]"
                                                            label="Day of Week"
                                                            :options="$daysOfWeek"
                                                            :selected="old('schedule_config.day_of_week', $scheduleConfig['day_of_week'] ?? 1)"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour (0-23)"
                                                            :value="old('schedule_config.hour', $scheduleConfig['hour'] ?? 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', $scheduleConfig['minute'] ?? 0)"
                                                            min="0"
                                                            max="59"
                                                            class="w-full" />
                                                    </div>
                                                </div>

                                                {{-- Monthly Config --}}
                                                <div id="monthlyConfig" class="schedule-config" style="display: {{ $currentFreq === 'monthly' ? 'block' : 'none' }};">
                                                    <label class="form-label">Day of Month & Time</label>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <x-team.forms.input
                                                            name="schedule_config[day_of_month]"
                                                            label="Day of Month"
                                                            type="number"
                                                            placeholder="Day (1-31)"
                                                            :value="old('schedule_config.day_of_month', $scheduleConfig['day_of_month'] ?? 1)"
                                                            min="1"
                                                            max="31"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour (0-23)"
                                                            :value="old('schedule_config.hour', $scheduleConfig['hour'] ?? 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', $scheduleConfig['minute'] ?? 0)"
                                                            min="0"
                                                            max="59"
                                                            class="w-full" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </x-team.card>

                            {{-- Campaign Rules --}}
                            <x-team.card title="Campaign Rules" headerClass="">
                                <div class="py-5">
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">
                                            Define rules to filter which leads will receive this WhatsApp campaign.
                                        </p>
                                    </div>
                                    
                                    <div id="rules-repeater">
                                        <div data-repeater-list="rules">
                                            @php
                                                $existingRules = old('rules', $campaign->formatted_rules ?? []);
                                                $fieldOptions = collect([
                                                    'status' => 'Lead Status',
                                                    'sub_status' => 'Lead Sub Status',
                                                    'source' => 'Lead Source',
                                                    'created_at' => 'Created Date',
                                                    'updated_at' => 'Updated Date',
                                                    'phone' => 'Phone Number',
                                                    'email' => 'Email Address',
                                                    'no_of_days' => 'No. of Days',
                                                    'tag' => 'Tag',
                                                ]);
                                                $operatorOptions = collect([
                                                    'equals' => 'Equals',
                                                    'not_equals' => 'Not Equals',
                                                    'contains' => 'Contains',
                                                    'not_contains' => 'Does Not Contain',
                                                    'starts_with' => 'Starts With',
                                                    'ends_with' => 'Ends With',
                                                    'greater_than' => 'Greater Than',
                                                    'less_than' => 'Less Than',
                                                    'in' => 'In List',
                                                    'not_in' => 'Not In List',
                                                ]);
                                            @endphp
                                            
                                            @if(count($existingRules) > 0)
                                                @foreach($existingRules as $index => $rule)
                                                <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                                                    <button type="button" class="absolute top-0 right-0 m-2 text-red-500 remove-rule" data-repeater-delete>
                                                        <i class="ki-filled ki-trash text-lg text-destructive"></i>
                                                    </button>
                                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                                                        {{-- Field Name --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.select 
                                                                name="field_name" 
                                                                label="Field"
                                                                :options="$fieldOptions" 
                                                                :selected="$rule['field_name'] ?? ''"
                                                                class="field_name"  
                                                                placeholder="Select field" 
                                                                searchable="true"
                                                                required />
                                                        </div>
                                                        
                                                        {{-- Operator --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.select 
                                                                name="operator" 
                                                                label="Operator"
                                                                :options="$operatorOptions" 
                                                                :selected="$rule['operator'] ?? ''"
                                                                class="operator" 
                                                                placeholder="Select operator"
                                                                searchable="true"
                                                                required />
                                                        </div>
                                                        
                                                        {{-- Field Value --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.input 
                                                                name="field_value" 
                                                                label="Value" 
                                                                type="text"
                                                                placeholder="Enter value" 
                                                                :value="$rule['field_value'] ?? ''"
                                                                helpText="For multiple values, separate with commas"
                                                                required />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                                                    <button type="button" class="absolute top-0 right-0 m-2 text-red-500 remove-rule" data-repeater-delete>
                                                        <i class="ki-filled ki-trash text-lg text-destructive"></i>
                                                    </button>
                                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                                                        {{-- Field Name --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.select 
                                                                name="field_name" 
                                                                label="Field"
                                                                :options="$fieldOptions" 
                                                                class="field_name"  
                                                                placeholder="Select field" 
                                                                searchable="true"
                                                                required />
                                                        </div>
                                                        
                                                        {{-- Operator --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.select 
                                                                name="operator" 
                                                                label="Operator"
                                                                :options="$operatorOptions" 
                                                                class="operator" 
                                                                placeholder="Select operator"
                                                                searchable="true"
                                                                required />
                                                        </div>
                                                        
                                                        {{-- Field Value --}}
                                                        <div class="col-span-1">
                                                            <x-team.forms.input 
                                                                name="field_value" 
                                                                label="Value" 
                                                                type="text"
                                                                placeholder="Enter value" 
                                                                helpText="For multiple values, separate with commas"
                                                                required />
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" data-repeater-create>+ Add Rule</button>
                                        </div>
                                    </div>
                                </div>
                            </x-team.card>

                        </div>
                    </div>
                    
                    {{-- Sidebar --}}
                    <div class="flex flex-col gap-5">
                        
                        {{-- Campaign Status --}}
                        <x-team.card title="Campaign Status" headerClass="">
                            <div class="py-5">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm text-gray-600">Current Status</span>
                                    <span class="badge {{ $campaign->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm text-gray-600">Created</span>
                                    <span class="text-sm font-medium">{{ $campaign->created_at->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Last Updated</span>
                                    <span class="text-sm font-medium">{{ $campaign->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </x-team.card>

                        {{-- Form Actions --}}
                        <x-team.card title="Actions" headerClass="">
                            <div class="flex flex-col gap-3 py-5">
                                <button type="submit" class="kt-btn kt-btn-primary w-full">
                                    <i class="ki-filled ki-check"></i>
                                    Update Campaign
                                </button>
                                
                                <a href="{{ route('team.automation.whatsapp.campaigns.show', $campaign) }}" class="kt-btn kt-btn-secondary w-full">
                                    <i class="ki-filled ki-arrow-left"></i>
                                    Cancel
                                </a>
                            </div>
                        </x-team.card>
                        
                    </div>
                </div>
            </form>
        </div>

        @push('scripts')
        <script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
        <script>
        $(document).ready(function() {
            // Initialize repeater for rules
            $('#rules-repeater').repeater({
                show: function () {
                    $(this).slideDown();

                    // Update IDs based on name attributes
                    $(this).find('input, select, textarea').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                            $(this).attr('id', id);
                        }
                    });

                    // Initialize Select2 for new selects
                    $(this).find('select').each(function () {
                        var $select = $(this);
                        $select.parent().find('.select2-container--default').remove();

                        $select.select2({
                            width: '100%'
                        });
                    });
                },
                hide: function (deleteElement) {
                    // Destroy Select2 before removing
                    $(this).find('select').each(function () {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });
                    $(this).slideUp(deleteElement);
                }
            });

            // Initialize form state on page load first
            initializeFormState();

            // Handle form submission to only include visible schedule config fields
            $('form#campaignForm').on('submit', function(e) {
                // Disable hidden schedule config inputs before submission
                $('.schedule-config:hidden input, .schedule-config:hidden select').prop('disabled', true);
            });

            // Handle execution type changes
            $(document).on('change', '#executionType', function() {
                const type = $(this).val();
                handleExecutionTypeChange(type);
            });

            // Schedule frequency change handler
            $(document).on('change', '#scheduleFrequency', function() {
                const frequency = $(this).val();
                handleScheduleFrequencyChange(frequency);
            });

            // Message type change handler
            $(document).on('change', '#messageType', function() {
                const messageType = $(this).val();
                handleMessageTypeChange(messageType);
            });

            // Template name change handler - simplified since variables are auto-mapped
            $(document).on('change', '#templateName', function() {
                const templateName = $(this).val();
                // Variables are automatically mapped via WhatsappTemplateVariableMapping
                // No manual configuration needed
            });

            function initializeFormState() {
                // Initialize execution type
                const initialExecutionType = $('#executionType').val();
                handleExecutionTypeChange(initialExecutionType);
                
                // Initialize schedule frequency
                const initialFrequency = $('#scheduleFrequency').val();
                handleScheduleFrequencyChange(initialFrequency);
                
                // Initialize message type
                const initialMessageType = $('#messageType').val();
                handleMessageTypeChange(initialMessageType);
            }

            function handleExecutionTypeChange(type) {
                if (type === 'one_time') {
                    $('#oneTimeSettings').show();
                    $('#automationSettings').hide();
                } else {
                    $('#oneTimeSettings').hide();
                    $('#automationSettings').show();
                }
            }

            function handleScheduleFrequencyChange(frequency) {
                $('.schedule-config').hide();
                
                // Clear all schedule config inputs first
                $('.schedule-config input, .schedule-config select').val('');
                
                if (frequency === 'daily') {
                    $('#dailyConfig').show();
                } else if (frequency === 'weekly') {
                    $('#weeklyConfig').show();
                } else if (frequency === 'monthly') {
                    $('#monthlyConfig').show();
                }
            }

            function handleMessageTypeChange(messageType) {
                if (messageType === 'text') {
                    $('#textMessageContent').show();
                    $('#templateConfiguration').hide();
                    $('#textMessageContent textarea').attr('required', 'required');
                    $('#templateConfiguration select[name="template_name"]').removeAttr('required');
                } else if (messageType === 'template') {
                    $('#textMessageContent').hide();
                    $('#templateConfiguration').show();
                    $('#textMessageContent textarea').removeAttr('required');
                    $('#templateConfiguration select[name="template_name"]').attr('required', 'required');
                } else {
                    $('#textMessageContent').hide();
                    $('#templateConfiguration').hide();
                    $('#textMessageContent textarea').removeAttr('required');
                    $('#templateConfiguration select').removeAttr('required');
                }
            }
        });
        </script>
        @endpush
    </x-slot>
</x-team.layout.app>
