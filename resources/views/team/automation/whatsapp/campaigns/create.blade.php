@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'WhatsApp Campaigns', 'url' => route('team.automation.whatsapp.campaigns.index')],
        ['title' => 'Create Campaign']
    ];
@endphp

<x-team.layout.app title="Create WhatsApp Campaign" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create WhatsApp Campaign
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Set up automated WhatsApp campaigns for lead management
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.whatsapp.campaigns.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Campaigns
                    </a>
                </div>
            </div>

            <form action="{{ route('team.automation.whatsapp.campaigns.store') }}" method="POST" id="campaignForm" class="form">
                @csrf
                
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
                                            :value="old('name')"
                                            required="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Description --}}
                                    <div class="lg:col-span-2">
                                        <x-team.forms.textarea
                                            name="description"
                                            label="Description"
                                            placeholder="Describe this campaign"
                                            :value="old('description')"
                                            rows="3"
                                            class="w-full" />
                                    </div>

                                    {{-- Delay Between Messages --}}
                                    <div>
                                        <x-team.forms.input
                                            name="delay_between_messages"
                                            label="Delay Between Messages (seconds)"
                                            type="number"
                                            placeholder="5"
                                            :value="old('delay_between_messages', 5)"
                                            min="1"
                                            max="60"
                                            helpText="Recommended: 5-10 seconds to avoid rate limits"
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
                                            :selected="old('priority', 2)"
                                            class="w-full" />
                                    </div>

                                    {{-- Active Status --}}
                                    <div>
                                        {{-- Hidden input to ensure unchecked sends false --}}
                                        <input type="hidden" name="is_active" value="0">
                                        <x-team.forms.checkbox
                                            name="is_active"
                                            label="Active Campaign"
                                            :checked="old('is_active', true)"
                                            value="1"
                                            style="default"/>
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
                                            :selected="old('execution_type', 'automation')"
                                            required="true"
                                            id="executionType"
                                            class="w-full" />
                                    </div>

                                    {{-- One Time Scheduling --}}
                                    <div id="oneTimeSettings" class="lg:col-span-2" style="display: none;">
                                        <x-team.forms.datepicker
                                            name="scheduled_at"
                                            label="Schedule Date & Time"
                                            placeholder="Select date and time"
                                            :value="old('scheduled_at')"
                                            class="w-full"
                                            minDate="today"
                                            minTime="{{ now()->format('H:i') }}"
                                            enableTime="true"
                                            helpText="When should this campaign run once"
                                            />
                                    </div>

                                    {{-- Automation Scheduling --}}
                                    <div id="automationSettings" class="lg:col-span-2">
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
                                                    :selected="old('schedule_frequency', 'daily')"
                                                    id="scheduleFrequency"
                                                    class="w-full" />
                                            </div>
                                            
                                            {{-- Schedule Configuration --}}
                                            <div id="scheduleConfig">
                                                {{-- Daily Config --}}
                                                <div id="dailyConfig" class="schedule-config">
                                                    <label class="form-label">Time of Day</label>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour (0-23)"
                                                            :value="old('schedule_config.hour', 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', 0)"
                                                            min="0"
                                                            max="59"
                                                            class="w-full" />
                                                    </div>
                                                </div>

                                                {{-- Weekly Config --}}
                                                <div id="weeklyConfig" class="schedule-config" style="display: none;">
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
                                                            :selected="old('schedule_config.day_of_week', 1)"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour"
                                                            :value="old('schedule_config.hour', 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', 0)"
                                                            min="0"
                                                            max="59"
                                                            class="w-full" />
                                                    </div>
                                                </div>

                                                {{-- Monthly Config --}}
                                                <div id="monthlyConfig" class="schedule-config" style="display: none;">
                                                    <label class="form-label">Day of Month & Time</label>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <x-team.forms.input
                                                            name="schedule_config[day_of_month]"
                                                            label="Day"
                                                            type="number"
                                                            placeholder="Day (1-31)"
                                                            :value="old('schedule_config.day_of_month', 1)"
                                                            min="1"
                                                            max="31"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[hour]"
                                                            label="Hour"
                                                            type="number"
                                                            placeholder="Hour"
                                                            :value="old('schedule_config.hour', 9)"
                                                            min="0"
                                                            max="23"
                                                            class="w-full" />
                                                        <x-team.forms.input
                                                            name="schedule_config[minute]"
                                                            label="Minute"
                                                            type="number"
                                                            placeholder="Minute"
                                                            :value="old('schedule_config.minute', 0)"
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

                            {{-- Message Configuration --}}
                            <x-team.card title="Message Configuration" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    
                                    {{-- Message Type --}}
                                    <div class="lg:col-span-2">
                                        @php
                                            $messageTypes = collect([
                                                'text' => 'Text Message',
                                                'template' => 'Template Message'
                                            ]);
                                        @endphp
                                        <x-team.forms.select
                                            name="message_type"
                                            label="Message Type"
                                            :options="$messageTypes"
                                            :selected="old('message_type', 'text')"
                                            required="true"
                                            id="messageType"
                                            class="w-full" />
                                    </div>

                                    {{-- Text Message Content --}}
                                    <div class="lg:col-span-2" id="textMessageContent">
                                        <x-team.forms.textarea
                                            name="message_content"
                                            label="Message Content"
                                            placeholder="Enter your message content..."
                                            :value="old('message_content')"
                                            rows="4"
                                            helpText="Maximum 1000 characters. You can use variables like {client_name}, {phone}, etc."
                                            required="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Template Selection --}}
                                    <div class="lg:col-span-2" id="templateSelection" style="display: none;">
                                        @php
                                            $templateOptions = collect(['' => 'Select Template'])
                                                ->merge($templates->mapWithKeys(fn($template) => [$template => $template]));
                                        @endphp
                                        <x-team.forms.select
                                            name="template_name"
                                            label="Template Name"
                                            :options="$templateOptions"
                                            :selected="old('template_name')"
                                            searchable="true"
                                            id="templateName"
                                            class="w-full" />
                                    </div>

                                    {{-- Template Variables Info --}}
                                    <div class="lg:col-span-2" id="templateVariablesInfo" style="display: none;">
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
                            </x-team.card>

                            {{-- Lead Filtering --}}
                            <x-team.card title="Lead Filtering" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    
                                    {{-- Lead Status Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[status][]"
                                            label="Lead Status"
                                            :options="$leadStatuses"
                                            :selected="old('lead_filters.status', [])"
                                            placeholder="All statuses"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Lead Type Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[lead_type][]"
                                            label="Lead Type"
                                            :options="$leadTypes"
                                            :selected="old('lead_filters.lead_type', [])"
                                            placeholder="All types"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Source Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[source][]"
                                            label="Source"
                                            :options="$sources"
                                            :selected="old('lead_filters.source', [])"
                                            placeholder="All sources"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Branch Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[branch][]"
                                            label="Branch"
                                            :options="$branches"
                                            :selected="old('lead_filters.branch', [])"
                                            placeholder="All branches"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Country Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[country][]"
                                            label="Country"
                                            :options="$countries"
                                            :selected="old('lead_filters.country', [])"
                                            placeholder="All countries"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Coaching Filter --}}
                                    <div>
                                        <x-team.forms.select
                                            name="lead_filters[coaching][]"
                                            label="Coaching"
                                            :options="$coachings"
                                            :selected="old('lead_filters.coaching', [])"
                                            placeholder="All coachings"
                                            multiple="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                </div>
                            </x-team.card>

                            {{-- Campaign Rules --}}
                            <x-team.card title="Campaign Rules" headerClass="">
                                <div id="rules-repeater">
                                    <div data-repeater-list="rules">
                                        <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                                            <button type="button" class="absolute top-0 right-0 m-2 text-red-500 remove-rule" data-repeater-delete>
                                                <i class="ki-filled ki-trash text-lg text-destructive"></i>
                                            </button>
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                                                <!-- Field Name -->
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
                                                
                                                <!-- Operator -->
                                                <div class="col-span-1">
                                                    <x-team.forms.select 
                                                        name="operator" 
                                                        label="Operator"
                                                        :options="$operators" 
                                                        class="operator" 
                                                        placeholder="Select operator"
                                                        searchable="true"
                                                        required />
                                                </div>
                                                
                                                <!-- Field Value -->
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
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" data-repeater-create>+ Add Rule</button>
                                    </div>
                                </div>
                            </x-team.card>

                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="xl:col-span-1">
                        <div class="flex flex-col gap-5">
                            
                            {{-- Campaign Summary --}}
                            <x-team.card title="Campaign Summary" headerClass="">
                                <div class="py-5">
                                    <div id="campaignSummary" class="space-y-2 text-sm">
                                        <div><strong>Campaign:</strong> <span id="summaryName">-</span></div>
                                        <div><strong>Type:</strong> <span id="summaryType">-</span></div>
                                        <div><strong>Message Type:</strong> <span id="summaryMessageType">-</span></div>
                                        <div><strong>Execution:</strong> <span id="summaryExecution">-</span></div>
                                        <div><strong>Priority:</strong> <span id="summaryPriority">Medium</span></div>
                                    </div>
                                </div>
                            </x-team.card>

                            {{-- Actions --}}
                            <x-team.card title="Actions" headerClass="">
                                <div class="py-5 space-y-3">
                                    <button type="submit" class="kt-btn kt-btn-success w-full">
                                        <i class="ki-filled ki-rocket"></i>
                                        Create Campaign
                                    </button>
                                    <a href="{{ route('team.automation.whatsapp.campaigns.index') }}" class="kt-btn kt-btn-secondary w-full">
                                        <i class="ki-filled ki-arrow-left"></i>
                                        Cancel
                                    </a>
                                </div>
                            </x-team.card>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </x-slot>

    @push('scripts')
        <script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
        <script>
        $(document).ready(function() {
            // Initialize repeater for rules
            $('#rules-repeater').repeater({
                initEmpty: false,
                defaultValues: {},
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });

            // Initialize form state on page load
            initializeFormState();

            // Handle form submission to only include visible schedule config fields
            $('form#campaignForm').on('submit', function(e) {
                // Disable hidden schedule config inputs before submission
                $('.schedule-config:hidden input, .schedule-config:hidden select').prop('disabled', true);
            });

            $(document).on('change', '#executionType', function() {
                const type = $(this).val();
                updateSummary();
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
                console.log(messageType);
                updateSummary();
                handleMessageTypeChange(messageType);
            });

            // Template name change handler - simplified since variables are auto-mapped
            $(document).on('change', '#templateName', function() {
                const templateName = $(this).val();
                // Variables are automatically mapped via WhatsappTemplateVariableMapping
                // No manual configuration needed
            });

            // Update summary on form changes
            $('input, select, textarea').change(updateSummary);

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
                
                updateSummary();
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
                if (messageType === 'template') {
                    $('#templateSelection').show();
                    $('#textMessageContent').hide();
                    $('#templateVariablesInfo').show();
                    $('#textMessageContent textarea').removeAttr('required');
                } else {
                    $('#templateSelection').hide();
                    $('#textMessageContent').show();
                    $('#templateVariablesInfo').hide();
                    $('#textMessageContent textarea').attr('required', 'required');
                }
            }
        });

        function updateSummary() {
            const name = $('#campaignForm input[name="name"]').val() || '-';
            const type = $('#executionType option:selected').text() || '-';
            const messageType = $('#messageType option:selected').text() || '-';
            const priority = $('#campaignForm select[name="priority"] option:selected').text() || 'Medium';
            
            $('#summaryName').text(name);
            $('#summaryType').text(type);
            $('#summaryMessageType').text(messageType);
            $('#summaryPriority').text(priority);
        }
        </script>
    @endpush
</x-team.layout.app>
