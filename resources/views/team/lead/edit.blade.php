@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Lead', 'url' => route('team.lead.index')],
    ['title' => 'Edit Lead']
];
@endphp
<x-team.layout.app title="Edit Lead" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Lead
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Edit lead to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.lead.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Lead Information" headerClass="">
                <form action="{{ route('team.lead.update',$clientLead->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <!-- First Name -->
                                <x-team.forms.datepicker
                                    label="Lead Date"
                                    name="client_date"
                                    id="client_date"
                                    placeholder="Select lead date"
                                    disabled="true"
                                    :value="old('client_date', \Carbon\Carbon::parse($clientLead->client_date)->format('d-m-Y'))"
                                    class="w-full flatpickr font-bold"
                                />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="purpose"
                                    label="Purpose"
                                    :options="$purposes"
                                    :selected="old('purpose', $clientLead?->purpose)"
                                    placeholder="Select purpose"
                                    required
                                    searchable="true"
                                    id="purposeSelect"
                                    inputId="purposeSelect"
                                    class="purpose-select"
                                />
                            </div>
                        </div>
                        <div class="col-span-1" id="countryDiv" style="display:none;">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="country"
                                    label="Country"
                                    :options="$country"
                                    :selected="old('country' ,$clientLead?->country)"
                                    placeholder="Select country"
                                    required
                                    searchable="true"
                                />
                            </div>
                        </div>
                        <div class="col-span-1" id="SecondcountryDiv" style="display:none;">
                                <div class="grid gap-5">
                                    <x-team.forms.select name="second_country[]" label="Second Country" :options="$country"
                                        :selected="old('second_country', $clientLead?->second_country ? explode(',', $clientLead->second_country) : [])" placeholder="Select second country" searchable="true"
                                        multiple="true" />
                                </div>
                            </div>
                        <div class="col-span-1" id="coachingDiv" style="display:none;">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="coaching"
                                    label="Coaching"
                                    :options="$coaching"
                                    :selected="old('coaching' ,$clientLead?->coaching)"
                                    placeholder="Select coaching"
                                    required
                                    searchable="true"
                                />
                            </div>
                        </div>
                        <div class="col-span-1" >
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="lead_type"
                                    label="Lead Type"
                                    :options="$leadTypes"
                                    :selected="old('lead_type' ,$clientLead?->client?->lead_type)"
                                    placeholder="Select lead type"
                                    required
                                    searchable="true"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="first_name"
                                    label="First Name"
                                    type="text"
                                    placeholder="Enter first name"
                                    :value="old('first_name' ,$clientLead?->client?->first_name)"
                                    required />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="middle_name"
                                    label="Middle Name"
                                    type="text"
                                    placeholder="Enter middle name"
                                    :value="old('middle_name' ,$clientLead?->client?->middle_name)" />
                            </div>
                        </div>
                        <div class="col-span-1" >
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="last_name"
                                    label="Last Name"
                                    type="text"
                                    placeholder="Enter last name"
                                    :value="old('last_name' ,$clientLead?->client?->last_name)"
                                    required/>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.mobile-input
                                    name="mobile_no"
                                    label="Mobile no"
                                    type="tel"
                                    placeholder="Enter mobile no"
                                    :value="old('mobile_no', $clientLead?->client?->mobile_no)"
                                    required/>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5 relative">
                                <x-team.forms.mobile-input
                                        name="whatsapp_no"
                                        label="Whatsapp no"
                                        type="tel"
                                        placeholder="Enter whatsapp no"
                                        :value="old('whatsapp_no' , $clientLead?->client?->whatsapp_no)"
                                        required/>
                                        <!-- Copy Button -->
                                    <button type="button" onclick="copyMobileToWhatsapp()"
                                        class="absolute py-2  top-7 end-0 text-sm rounded kt-btn kt-btn-sm kt-btn-ghost" style="margin-left: 89px; margin-top: -3px;">
                                        <i class="ki-filled ki-copy"></i> 
                                    </button>
                            </div>
                        </div>
                        <div class="col-span-1" >
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="email_id"
                                    label="Email id"
                                    type="text"
                                    placeholder="Enter email "
                                    :value="old('email_id' , $clientLead?->client?->email_id)"
                                    required/>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                @php
                                    $gender = [
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ];
                                @endphp
                                <x-team.forms.select
                                    name="gender"
                                    label="Gender"
                                    :options="$gender"
                                    :selected="old('gender' ,$clientLead?->client?->gender)"
                                    placeholder="Select gender"
                                    searchable="true" />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="maratial_status"
                                    label="Marital Status"
                                    :options="$maritalStatus"
                                    :selected="old('maratial_status' ,$clientLead?->client?->maratial_status)"
                                    placeholder="Select Marital Status"
                                    searchable="true" />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">

                                <x-team.forms.datepicker
                                    label="Date of Birth"
                                    name="date_of_birth"
                                    id="date_of_birth"
                                    placeholder="Select Date of Birth "
                                    maxDate="today"
                                    dateFormat="Y-m-d"
                                    :value="old('date_of_birth', \Carbon\Carbon::parse($clientLead?->client?->date_of_birth)->format('d-m-Y') )"
                                    class="w-full flatpickr" />

                            </div>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 {{ Auth::user()->hasPermissionTo('lead:show-all') ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}  gap-5 py-5">
                        @if(Auth::user()->hasPermissionTo('lead:show-all'))
                            <div class="col-span-1">
                                <div class="grid gap-5">
                                    <x-team.forms.select
                                        name="branch"
                                        label="Branch"
                                        :options="$branch"
                                        :selected="old('branch', $clientLead?->branch)"
                                        placeholder="Select branch"
                                        required
                                        searchable="true"
                                    />
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="branch" value="{{ Auth::user()->branch_id }}">
                                <select name="branch" class="form-control" hidden>
                                    <option value="{{ Auth::user()->branch_id }}" selected></option>
                                </select>
                        @endif
                        <div class="col-span-1">
                                <div class="grid gap-5">
                                    <x-team.forms.select
                                            name="country_id"
                                            label="Country"
                                            :options="$countries ?? []"
                                            :selected="$clientLead->client->country ?? old('country_id')"
                                            placeholder="Select Country"
                                            required="true"
                                            searchable="true"
                                        />
                                </div>
                        </div>
                        <div class="col-span-1">
                                <div class="grid gap-5">
                                      <x-team.forms.select
                                            name="state_id"
                                            label="State/Province"
                                            :options="$states ?? []"
                                            :selected="$clientLead->client->state ?? old('state_id')"
                                            placeholder="Select State"
                                            required="true"
                                            searchable="true"
                                        />
                                </div>
                        </div>
                        <div class="col-span-1" >
                                <div class="grid gap-5">
                                          <x-team.forms.select
                                            name="city_id"
                                            label="City"
                                            :options="$cities ?? []"
                                            :selected="$clientLead->client->city ?? old('city_id')"
                                            placeholder="Select City"
                                            required="true"
                                            searchable="true"
                                        />
                                </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.textarea
                                    id="address"
                                    name="address"
                                    label="Address"
                                    :value="old('address',$clientLead?->client?->address)"
                                    placeholder="Enter address"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="source"
                                    label="Source"
                                    :options="$sources"
                                    :selected="old('source', $clientLead?->source ?? $clientLead?->client?->source)"
                                    placeholder="Select source"
                                    searchable="true"
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    id="remark"
                                    type="text"
                                    name="remark"
                                    label="Source Remark"
                                    :value="old('remark' , $clientLead?->remark)"
                                    placeholder="Enter source remark"
                                />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="assign_owner"
                                    label="Counsellor"
                                    :options="$assign_owner"
                                    :selected="old('assign_owner' , $clientLead?->assign_owner)"
                                    placeholder="Select counsellor"
                                    required
                                    searchable="true"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="tags"
                                    label="Tag"
                                    id="tags"
                                    :value="old('tags', $clientLead?->tag)" {{-- fill with old() or DB --}}
                                    placeholder="Add tags"
                                />

                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="lead_status"
                                    label="Lead Status"
                                    :options="$lead_status"
                                    :selected="old('lead_status',$clientLead?->status)"
                                    placeholder="Select lead status"
                                    searchable="true"
                                    required
                                    id="lead_status"
                                />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.select
                                    name="lead_sub_status"
                                    label="Lead Sub Status"
                                    :options="$lead_sub_status"
                                    :selected="old('lead_sub_status' ,$clientLead?->sub_status)"
                                    placeholder="Select lead sub status"
                                    searchable="true"
                                    required
                                    id="lead_sub_status"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5">
                                <x-team.forms.textarea
                                    id="genralRemarks"
                                    name="genral_remark"
                                    label="Genral Remarks"
                                    :value="old('genral_remark' ,$clientLead?->genral_remark)"
                                    placeholder="Enter genral remarks"
                                />
                            </div>
                        </div>
                    </div>
                    {{-- <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" id="lead-statistics-container">
                            <x-team.lead.edit-education
                                :educationLevel="$educationLevel"
                                :educationBoard="$educationBoard"
                                :educationStream="$educationStream"
                                :educations="$clientLead?->client?->educationDetails"
                                 />
                        </div>
                    </div> --}}

                    {{-- <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" id="lead-statistics-container">
                            <x-team.lead.edit-english-proficiency
                                :englishProficiencyTest="$englishProficiencyTest"
                                :clientLead="$clientLead"
                            />
                        </div>
                    </div> --}}

                    {{-- <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" id="lead-statistics-container">
                            <x-team.lead.passport-details
                                :passportData="$clientLead?->client?->passportDetails"
                                 />
                        </div>
                    </div> --}}

                    {{-- <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" id="lead-statistics-container">
                            <x-team.card title="Immigration Details" headerClass="bg-secondary-100">
                                <x-team.lead.immigration-details
                                    :countrys="$country"
                                    :typeOfRelations="$typeOfRelation"
                                    :otherVisaTypes="$otherVisaType"
                                    :relativeData="$clientLead?->client?->getClientRelativeDetails"
                                    :rejectionData="$clientLead?->client?->visaRejectionDetails"
                                    :visitedData="$clientLead?->client?->anyVisitedDetails"
                                    />
                            </x-team.card>
                        </div>
                    </div> --}}

                    {{-- <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" id="lead-statistics-container">
                            <x-team.lead.employment-details
                                :employmentData="$clientLead?->client?->employmentDetails"
                            />
                        </div>
                    </div> --}}
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.lead.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Lead
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

@push('scripts')
    <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    @include('team.lead.lead-js')

    {{-- Country State City Location AJAX for Edit --}}
    <script>
        $(document).ready(function() {
            // Initialize Location AJAX for country/state/city dropdowns
            LocationAjax.init({
                countrySelector: '#country_id',
                stateSelector: '#state_id',
                citySelector: '#city_id',
                statesRoute: '{{ route("team.settings.company.states", ":countryId") }}'.replace(':countryId', ''),
                citiesRoute: '{{ route("team.settings.company.cities", ":stateId") }}'.replace(':stateId', '')
            });
            // If editing existing company with location data, set the values
            @if($clientLead && $clientLead->country && $clientLead->state && $clientLead->city)
                // Set initial values for edit mode
                setTimeout(function() {
                    LocationAjax.setSelectedValues({
                        country_id: '{{ $clientLead?->country }}',
                        state_id: '{{ $clientLead->state }}',
                        city_id: '{{ $clientLead->city }}'
                    });
                }, 100);
            @endif
        });
    </script>
@endpush
</x-team.layout.app>
