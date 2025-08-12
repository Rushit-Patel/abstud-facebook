@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Profile', 'url' => route('team.client.show', $client)],
        ['title' => 'Create Lead']
    ];
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<x-team.layout.app title="Create Lead" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Lead
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new lead to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.client.show', $client) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Profile
                    </a>
                </div>
            </div>
            <form action="{{ route('team.client.new.lead.store', $client) }}" method="POST" class="form" enctype="multipart/form-data">
                @csrf
                <x-team.card title="Other Service " headerClass="">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.datepicker
                                    label="Lead Date"
                                    name="client_date"
                                    id="client_date"
                                    placeholder="Select lead date"
                                    required="true"
                                    dateFormat="Y-m-d"
                                    :value="old('client_date', \Carbon\Carbon::today()->format('d/m/Y'))"
                                    class="w-full flatpickr" />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="purpose"
                                    label="Purpose"
                                    :options="$purposes"
                                    :selected="old('purpose', $purposes->keys()->first())"
                                    placeholder="Select purpose"
                                    required="true"
                                    searchable="true"
                                    id="purposeSelect"
                                    inputId="purposeSelect"
                                    class="purpose-select" />
                            </div>
                        </div>
                        <div class="col-span-1" id="countryDiv" style="display:none;">
                            <div class="grid gap-5">
                                <x-team.forms.select name="country" label="Country" :options="$country"
                                    :selected="old('country')" placeholder="Select country" searchable="true"
                                    required="true" />
                            </div>
                        </div>
                        <div class="col-span-1" id="SecondcountryDiv" style="display:none;">
                            <div class="grid gap-5">
                                <x-team.forms.select name="second_country[]" label="Second Country" :options="$country"
                                    :selected="old('second_country')" placeholder="Select second country" searchable="true"
                                    multiple="true" />
                            </div>
                        </div>
                        <div class="col-span-1" id="coachingDiv" style="display:none;">
                            <div class="grid gap-5">
                                <x-team.forms.select name="coaching" label="Coaching" :options="$coaching"
                                    :selected="old('coaching')" placeholder="Select coaching" searchable="true"
                                    required="true" />
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 py-5">
                    <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="lead_type" label="Lead Type" :options="$leadTypes"
                                    :selected="old('lead_type')" placeholder="Select lead type" required="true"
                                    searchable="true" />
                            </div>
                        </div>
                    <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="branch" label="Branch" :options="$branch" :selected="null"
                                    placeholder="Select branch" required searchable="true" />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="source" label="Source" :options="$sources"
                                    :selected="old('source')" placeholder="Select source" searchable="true" required />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input id="remark" type="text" name="remark" label="Source Remark"
                                    :value="old('remark')" placeholder="Enter source remark" />
                            </div>
                        </div>

                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                    <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="assign_owner" label="Counsellor" :options="$assign_owner"
                                    :selected="old('assign_owner')" placeholder="Select counsellor" required
                                    searchable="true" />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="tags"
                                    label="Tag"
                                    id="tags" {{-- important: for JS targeting --}}
                                    :value="old('tags')"
                                    placeholder="Add tags"
                                    searchable="true"
                                />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="lead_status" label="Lead Status" :options="$lead_status"
                                    :selected="old('lead_status')" placeholder="Select lead status" searchable="true"
                                    required="true" id="lead_status" />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select name="lead_sub_status" label="Lead Sub Status" :options="[]"
                                    :selected="old('lead_sub_status')" placeholder="Select lead sub status"
                                    searchable="true" required="true" id="lead_sub_status" />
                            </div>
                        </div>
                    </div>
                </x-team.card>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                    <a href="{{ route('team.client.show', $client) }}" class="kt-btn kt-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Create Lead
                    </button>
                </div>
            </form>
        </div>
    </x-slot>
    @push('scripts')
        <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>
        <script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        @include('team.lead.lead-js')
    @endpush
</x-team.layout.app>
