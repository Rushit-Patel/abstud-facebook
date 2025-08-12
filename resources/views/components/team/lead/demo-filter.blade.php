@props([
    'id' => 'demo_filter_drawer',
    'title' => 'Demo Filters',
    'branches' => \Illuminate\Support\Collection::make(),
])

<x-team.drawer.drawer id="{{ $id }}" title="{{ $title }}">
    <x-slot name="body">
        <form id="demoFilterForm" method="POST" >
            @csrf
            @method('POST')
            {{-- Date Range Filter --}}
            <div class="flex flex-col gap-3 px-5">
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
            @haspermission('demo:show-all')
            <div class="border-b border-border mb-4 mt-5"></div>
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

            @haspermission('demo:show-branch')
            <div class="border-b border-border mb-4 mt-5"></div>
            <input type="hidden" name="branch[]" id="branch_filter" value="{{ auth()->user()->branch_id }}">
            <div class="flex flex-col gap-3 px-5">
                <x-team.forms.select
                        name="owner[]"
                        id="owner"
                        label="User"
                        :options="[]"
                        :selected="old('owner')"
                        placeholder="Select user"
                        searchable="true"
                        multiple="true"
                    />
            </div>
            @endhaspermission
        </form>
    </x-slot>
    <x-slot name="footer">
        <button type="button" class="kt-btn kt-btn-outline" onclick="resetDemoFilters()">
            Reset
        </button>
        <button type="submit" form="demoFilterForm" class="kt-btn kt-btn-primary">
            Apply Filters
        </button>
    </x-slot>
</x-team.drawer.drawer>
