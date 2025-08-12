@props([
    'id' => 'lead_filter_drawer',
    'title' => 'Lead Filters',
    'status' => \Illuminate\Support\Collection::make(),
    'subStatus' => \Illuminate\Support\Collection::make(),
    'branches' => \Illuminate\Support\Collection::make(),
    'leadTypes' => \Illuminate\Support\Collection::make(),
    'sources' => \Illuminate\Support\Collection::make(),
    'purpose' => \Illuminate\Support\Collection::make(),
    'countries' => \Illuminate\Support\Collection::make(),
    'coaching' => \Illuminate\Support\Collection::make(),
    'users' => \Illuminate\Support\Collection::make(),
])

<x-team.drawer.drawer id="{{ $id }}" title="{{ $title }}">
    <x-slot name="body">
        <form id="leadFilterForm" method="POST" >
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
            <div class="border-b border-border mb-4 mt-5"></div>
            {{-- Lead Status Filter --}}
            <div class="flex items-center gap-1 px-5 mb-3">
                <span class="text-sm font-medium text-mono">
                    Status
                </span>
            </div>
            <div class="px-5">
                <div class="flex flex-wrap gap-2.5 mb-2">
                    @foreach ($status as $stat)
                        <x-team.forms.checkbox
                            name="status[]"
                            value="{{ $stat->id }}"
                            label="{{ $stat->name }}"
                            style="badge"
                            checked="{{ in_array($stat->id, (array) request('status')) }}"
                        />
                    @endforeach
                </div>
            </div>

            @haspermission('lead:show-all')
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

            @haspermission('lead:show-branch')
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

            <div class="border-b border-border mb-4 mt-5"></div>
            {{-- Lead Source Filter --}}
            <div class="flex flex-col gap-3 px-5">
                <span class="text-sm font-medium text-mono">
                    Lead Source
                </span>
                <div class="flex flex-wrap gap-2.5">
                    @foreach ($sources as $source)
                        <x-team.forms.checkbox
                            name="source[]"
                            value="{{ $source->id }}"
                            label="{{ $source->name }}"
                            style="badge"
                            checked="{{ in_array($source->id, request('source', [])) }}"
                        />
                    @endforeach
                </div>
            </div>
            {{-- Date Range Filter --}}
            <div class="border-b border-border mb-4 mt-5"></div>
            {{-- Lead Type Filter --}}
            <div class="flex flex-col gap-3 px-5 lg:mb-10">
                <span class="text-sm font-medium text-mono">
                    Lead Type
                </span>
                <div class="flex flex-wrap gap-2.5">
                    @foreach ($leadTypes as $type)
                        <x-team.forms.checkbox
                            name="lead_type[]"
                            value="{{ $type->id }}"
                            label="{{ $type->name }}"
                            style="badge"
                            checked="{{ in_array($type->id, request('lead_type', [])) }}"
                        />
                    @endforeach
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot name="footer">
        <button type="button" class="kt-btn kt-btn-outline" onclick="resetLeadFilters()">
            Reset
        </button>
        <button type="submit" form="leadFilterForm" class="kt-btn kt-btn-primary">
            Apply Filters
        </button>
    </x-slot>
</x-team.drawer.drawer>
