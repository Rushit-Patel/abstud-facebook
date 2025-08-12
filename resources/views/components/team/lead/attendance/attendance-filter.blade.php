@props([
    'id' => 'attendance_filter_drawer',
    'title' => 'Attendance Filters',
    'branches' => \Illuminate\Support\Collection::make(),
    'coachings' => \Illuminate\Support\Collection::make(),
])

<x-team.drawer.drawer id="{{ $id }}" title="{{ $title }}">
    <x-slot name="body">
        <form id="attendanceFilterForm" method="POST" >
            @csrf
            @method('POST')
            {{-- Date Range Filter --}}
            <div class="flex flex-col gap-3 px-5" hidden>
                <span class="text-sm font-medium text-mono">
                    Date Range
                </span>
                <x-team.forms.range-datepicker
                    name="date"
                    value="{{ request('date') }}"
                    placeholder="Select Date Range"
                    class="w-full"
                />
            </div>
            @haspermission('coaching:show-all')
                <div class="flex items-center gap-1 px-5 mb-3">
                    <span class="text-sm font-medium text-mono">
                        Branch
                    </span>
                </div>
                <div class="px-5">
                    <div class="flex flex-wrap gap-2.5 mb-2">
                        @foreach ($branches as $branch)
                        <x-team.forms.checkbox
                                name="branch[]"
                                id="branch_filter"
                                value="{{ $branch->id }}"
                                label="{{ $branch->branch_name }}"
                                style="inline"
                                checked="{{ in_array($branch->id, (array) request('branch', [])) }}"
                            />
                        @endforeach
                    </div>
                </div>
            @endhaspermission

            @haspermission('coaching:show-branch')
            <div class="border-b border-border mb-4 mt-5"></div>
            <input type="hidden" name="branch[]" id="branch_filter" value="{{ auth()->user()->branch_id }}">
            @endhaspermission
            <div class="flex flex-col gap-3 px-5">
                <x-team.forms.select
                        name="coaching[]"
                        id="coaching_select_multiple"
                        label="Coaching"
                        :options="$coachings"
                        :selected="old('coaching')"
                        placeholder="Select coaching"
                        searchable="true"
                        multiple="true"
                    />
            </div>

            <div class="flex flex-col gap-3 px-5 mt-2">
                <x-team.forms.select
                        name="batch_id[]"
                        label="Batch"
                        id="batch_select_multiple"
                        :options="[]"
                        :selected="old('batch_id')"
                        placeholder="Select batch"
                        searchable="true"
                        multiple="true"
                    />
               </div>

        </form>
    </x-slot>
    <x-slot name="footer">
        <button type="button" class="kt-btn kt-btn-outline" onclick="resetAttendanceFilters()">
            Reset
        </button>
        <button type="submit" form="attendanceFilterForm" class="kt-btn kt-btn-primary">
            Apply Filters
        </button>
    </x-slot>
</x-team.drawer.drawer>
