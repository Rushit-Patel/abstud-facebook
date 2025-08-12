@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'Email Campaigns', 'url' => route('team.automation.email.campaigns.index')],
        ['title' => 'Create Campaign']
    ];
@endphp

<x-team.layout.app title="Create Email Campaign" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create Email Campaign
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Set up automated email campaigns for lead management
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.email.campaigns.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Campaigns
                    </a>
                </div>
            </div>

            <form action="{{ route('team.automation.email.campaigns.store') }}" method="POST" id="campaignForm" class="form">
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

                                    {{-- Email Template --}}
                                    <div>
                                        <x-team.forms.select
                                            name="email_template_id"
                                            label="Email Template"
                                            :options="$templates"
                                            :selected="old('email_template_id')"
                                            placeholder="Select email template"
                                            required="true"
                                            searchable="true"
                                            class="w-full" />
                                    </div>

                                    {{-- Delay --}}
                                    <div>
                                        <x-team.forms.input
                                            name="delay_minutes"
                                            label="Delay (Minutes)"
                                            type="number"
                                            placeholder="0"
                                            :value="old('delay_minutes', 0)"
                                            min="0"
                                            helpText="Time to wait before sending email (0 = immediate)"
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

                                </div>
                            </x-team.card>

                            {{-- Campaign Execution Settings --}}
                            <x-team.card title="Campaign Execution" headerClass="">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                                    
                                    {{-- Execution Type --}}
                                    <div class="lg:col-span-2">
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
                                            class=" w-full"
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
                    <div class="flex flex-col gap-5">
                        
                        {{-- Actions --}}
                        <x-team.card title="Actions" headerClass="">
                            <div class="flex flex-col gap-3 py-5">
                                <x-team.forms.button
                                    type="submit"
                                    variant="primary"
                                    class="w-full">
                                    <i class="ki-filled ki-check"></i>
                                    Create Campaign
                                </x-team.forms.button>
                                
                                <a href="{{ route('team.automation.email.campaigns.index') }}" class="kt-btn kt-btn-light w-full">
                                    <i class="ki-filled ki-cross"></i>
                                    Cancel
                                </a>
                            </div>
                        </x-team.card>

                        {{-- Campaign Guide --}}
                        <x-team.card title="Campaign Types" headerClass="">
                            <div class="flex flex-col gap-4 text-sm py-5">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-900 mb-1">One Time</div>
                                    <div class="text-blue-700 text-xs">Run once at a specific date and time</div>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="font-semibold text-green-900 mb-1">Set in Automation</div>
                                    <div class="text-green-700 text-xs">Run automatically on a schedule (daily, weekly, monthly)</div>
                                </div>
                            </div>
                        </x-team.card>

                        {{-- Tips --}}
                        <x-team.card title="Lead Automation Tips" headerClass="">
                            <div class="text-sm py-5 space-y-3">
                                <div class="flex items-start gap-2">
                                    <i class="ki-filled ki-filter text-blue-500 mt-0.5"></i>
                                    <div class="text-gray-600">Use lead filters to target specific segments</div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="ki-filled ki-time text-green-500 mt-0.5"></i>
                                    <div class="text-gray-600">Schedule campaigns to run at optimal times</div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="ki-filled ki-rocket text-purple-500 mt-0.5"></i>
                                    <div class="text-gray-600">Enable "Apply to New Leads" for ongoing automation</div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="ki-filled ki-star text-yellow-500 mt-0.5"></i>
                                    <div class="text-gray-600">Higher priority campaigns send first</div>
                                </div>
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
                // Initialize execution type handler
                const $executionTypeSelect = $('#executionType');
                const $scheduleFrequencySelect = $('#scheduleFrequency');
                
                if ($executionTypeSelect.length) {
                    $executionTypeSelect.on('change', handleExecutionTypeChange);
                    handleExecutionTypeChange(); // Initialize on page load
                }
                
                if ($scheduleFrequencySelect.length) {
                    $scheduleFrequencySelect.on('change', handleScheduleFrequencyChange);
                    handleScheduleFrequencyChange(); // Initialize on page load
                }

                // Initialize Rules Repeater
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
            });

            function handleExecutionTypeChange() {
                const executionType = $('#executionType').val();
                const $oneTimeSettings = $('#oneTimeSettings');
                const $automationSettings = $('#automationSettings');
                
                if (executionType === 'one_time') {
                    $oneTimeSettings.show();
                    $automationSettings.hide();
                } else {
                    $oneTimeSettings.hide();
                    $automationSettings.show();
                }
            }

            function handleScheduleFrequencyChange() {
                const frequency = $('#scheduleFrequency').val();
                const $configs = $('.schedule-config');
                
                // Hide all config sections
                $configs.hide();
                
                // Show relevant config section
                const $targetConfig = $('#' + frequency + 'Config');
                if ($targetConfig.length) {
                    $targetConfig.show();
                }
            }
        </script>
        @endpush
    </x-slot>
    
</x-team.layout.app>
