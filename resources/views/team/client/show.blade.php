@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Lead', 'url' => route('team.lead.index')],
        ['title' => $client->first_name . ' ' . $client->last_name . '\'s Profile']
    ];
@endphp

<x-team.layout.app title="{{ $client->first_name . ' ' . $client->last_name }}'s Profile" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Minimal Profile Header -->
            <style>
                .hero-bg {
                    background-image: url('/default/images/2600x1200/bg-1-dark.png');
                }
                .dark .hero-bg {
                    background-image: url('/default/images/2600x1200/bg-1-dark.png');
                }
            </style>

            <x-team.profile.profile-header
                :client="$client"
            />

            <!-- Tab Content -->
            <div class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left Column - Client Details -->
                    <div class="lg:col-span-4 space-y-6 order-2 lg:order-1">
                        <!-- Personal Information -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-user text-blue-600"></i>
                                    Personal Details
                                </h3>
                            </x-slot>
                            <div class="space-y-3">
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Full Name</span>
                                    <span class="col-span-2 text-sm font-medium">
                                        <span class="copy-text cursor-pointer hover:underline transition">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</span>
                                        <i class="ki-filled ki-copy text-primary"></i>
                                    </span>
                                </div>
                                @if($client->gender)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Gender</span>
                                    <span class="col-span-2 text-sm font-medium">{{ ucfirst($client->gender) }}</span>
                                </div>
                                @endif
                                @if($client->date_of_birth)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Date of Birth</span>
                                    <span class="col-span-2 text-sm font-medium">{{ date('M d, Y', strtotime($client->date_of_birth)) }}</span>
                                </div>
                                @endif
                                @if($client->maratial_status)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Marital Status</span>
                                    <span class="col-span-2 text-sm font-medium">{{ ucfirst($client?->maratialStatus?->name) }}</span>
                                </div>
                                @endif
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Email</span>
                                    <span class="col-span-2 text-sm font-medium">
                                        <span class="copy-text cursor-pointer hover:underline transition">{{ $client->email_id }}</span>
                                        <i class="ki-filled ki-copy text-primary"></i>
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Mobile</span>
                                    <span class="col-span-2 text-sm font-medium">
                                        <span class="copy-text cursor-pointer hover:underline transition">+{{ $client->country_code }} {{ $client->mobile_no }}</span>
                                        <i class="ki-filled ki-copy text-primary"></i>
                                    </span>
                                </div>
                                @if($client->whatsapp_no)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">WhatsApp</span>
                                    <span class="col-span-2 text-sm font-medium">
                                        <span class="copy-text cursor-pointer hover:underline transition">+{{ $client->whatsapp_country_code }} {{ $client->whatsapp_no }}</span>
                                        <i class="ki-filled ki-copy text-primary"></i>
                                    </span>
                                </div>
                                @endif
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Location</span>
                                    <span class="col-span-2 text-sm font-medium">{{ $client?->getCountry?->name }}, {{ $client->getState?->name }}, {{ $client->getCity?->name }}</span>
                                </div>
                                @if($client->address)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Address</span>
                                    <span class="col-span-2 text-sm font-medium">{{ $client->address }}</span>
                                </div>
                                @endif
                                @if($client->getSource)
                                <div class="grid grid-cols-3 gap-2 py-2">
                                    <span class="text-sm text-gray-500">Source</span>
                                    <span class="col-span-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $client->getSource->name }}
                                        </span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </x-team.card>

                        <!-- Passport Details -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-passport text-red-600"></i>
                                    Passport Details
                                </h3>
                                @haspermission('lead:edit')
                                    @if(isset($client->passportDetails) && $client->passportDetails->count() > 0)
                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_passport">
                                            <i class="ki-filled ki-pencil"></i>
                                        </button>
                                    @endif
                                @endhaspermission
                            </x-slot>
                            @if($client->passportDetails)
                            <div class="space-y-3">
                                @if($client->passportDetails->passport_number)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Passport No.</span>
                                    <span class="col-span-2 text-sm font-medium">{{ $client->passportDetails->passport_number }}</span>
                                </div>
                                @endif
                                @if($client->passportDetails->passport_expiry_date)
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-500">Expiry Date</span>
                                    <span class="col-span-2 text-sm font-medium">{{ date('M d, Y', strtotime($client->passportDetails->passport_expiry_date)) }}</span>
                                </div>
                                @endif
                                @if($client->passportDetails->passport_copy)
                                <div class="grid grid-cols-3 gap-2 py-2">
                                    <span class="text-sm text-gray-500">Document</span>
                                    <span class="col-span-2">
                                        <a href="{{ asset('storage/' . $client->passportDetails->passport_copy) }}"
                                            class="kt-btn kt-btn-xs kt-btn-light"
                                            target="_blank">
                                            <i class="ki-filled ki-eye mr-1"></i>
                                            View Copy
                                        </a>
                                    </span>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="text-center py-6">
                                <i class="ki-filled ki-passport text-2xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">No passport details available</p>
                                @haspermission('lead:edit')
                                    <button class="kt-btn kt-btn-sm kt-btn-primary mt-2" data-kt-modal-toggle="#add_passport">
                                        <i class="ki-filled ki-plus mr-1"></i>
                                            Add Passport
                                    </button>
                                @endhaspermission
                            </div>
                            @endif
                        </x-team.card>
                        <!-- Recent Activity Timeline -->

                        <!-- Immigration Details -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-geolocation text-orange-600"></i>
                                    Immigration Details
                                </h3>
                            </x-slot>
                            <div class="space-y-6">
                                <!-- Relatives in Foreign Country -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Relatives in Foreign Country</h5>
                                        @haspermission('lead:edit')
                                            @if(isset($client->getClientRelativeDetails) && $client->getClientRelativeDetails->count() > 0)
                                                <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_relative_country">
                                                    <i class="ki-filled ki-pencil"></i>
                                                </button>
                                            @endif
                                        @endhaspermission
                                    </div>

                                    @if($client->getClientRelativeDetails)
                                    <div class="overflow-x-auto">
                                        <table class="w-full border border-gray-200 rounded-lg">
                                            <thead>
                                                <tr class="bg-gray-50 border-b border-gray-200">
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-700">Relationship</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-700">Country</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-700">Visa Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-gray-900">
                                                            {{ $client->getClientRelativeDetails?->getRelationship?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-gray-900">
                                                            {{ $client->getClientRelativeDetails?->getCountry?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-gray-900">
                                                            {{ $client->getClientRelativeDetails?->getTypeOfVisa?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-500">No relatives in foreign country</p>
                                        @haspermission('lead:edit')
                                            <button class="kt-btn kt-btn-sm kt-btn-primary mt-2" data-kt-modal-toggle="#add_relative_country">
                                                <i class="ki-filled ki-plus mr-1"></i>
                                                    Add relative country
                                            </button>
                                        @endhaspermission
                                    </div>
                                    @endif
                                </div>

                                <!-- Previous Visa Rejections -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Previous Visa Rejections</h5>
                                        @haspermission('lead:edit')
                                            @if(isset($client->visaRejectionDetails) && $client->visaRejectionDetails->count() > 0)
                                                <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_rejection_country">
                                                    <i class="ki-filled ki-pencil"></i>
                                                </button>
                                            @endif
                                        @endhaspermission
                                    </div>
                                    @if($client->visaRejectionDetails && $client->visaRejectionDetails->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full border border-gray-200 rounded-lg">
                                            <thead>
                                                <tr class="bg-red-50 border-b border-red-200">
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-red-700">Country</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-red-700">Rejection Date</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-red-700">Visa Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($client->visaRejectionDetails as $rejection)
                                                <tr class="border-b border-red-100 hover:bg-red-50 transition-colors">
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-red-800 font-medium">
                                                            {{ $rejection->getCountry?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-red-800">
                                                            {{ $rejection->rejection_month_year ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-red-800">
                                                            {{ $rejection->getTypeOfVisa?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-500">No previous rejections</p>
                                        @haspermission('lead:edit')
                                            <button class="kt-btn kt-btn-sm kt-btn-primary mt-2" data-kt-modal-toggle="#add_rejection_country">
                                                <i class="ki-filled ki-plus mr-1"></i>
                                                    Add rejection country
                                            </button>
                                        @endhaspermission

                                    </div>
                                    @endif
                                </div>

                                <!-- Countries Visited -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Countries Visited</h5>
                                        @haspermission('lead:edit')
                                            @if(isset($client->anyVisitedDetails) && $client->anyVisitedDetails->count() > 0)
                                                <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_visited_country">
                                                    <i class="ki-filled ki-pencil"></i>
                                                </button>
                                            @endif
                                        @endhaspermission
                                    </div>

                                    @if($client->anyVisitedDetails && $client->anyVisitedDetails->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full border border-gray-200 rounded-lg">
                                            <thead>
                                                <tr class="bg-green-50 border-b border-green-200">
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-green-700">Country</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-green-700">Visa Type</th>
                                                    <th class="text-left py-2 px-3 text-xs font-semibold text-green-700">Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($client->anyVisitedDetails as $visit)
                                                <tr class="border-b border-green-100 hover:bg-green-50 transition-colors">
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-green-800 font-medium">
                                                            {{ $visit->getVisitedCountry?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-green-800">
                                                            {{ $visit->getVisitedVisaType?->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span class="text-sm text-green-800">
                                                            {{ date('M Y', strtotime($visit->start_date)) }} - {{ date('M Y', strtotime($visit->end_date)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-500">No countries visited</p>
                                        @haspermission('lead:edit')
                                        <button class="kt-btn kt-btn-sm kt-btn-primary mt-2" data-kt-modal-toggle="#add_visited_country">
                                            <i class="ki-filled ki-plus mr-1"></i>
                                                Add visited country
                                        </button>
                                        @endhaspermission
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </x-team.card>
                    </div>
                    <div class="lg:col-span-8 space-y-6 order-1 lg:order-2">
                        <!-- Quick Stats -->
                        {{-- <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                        </div> --}}

                        <!-- Detailed Leads with Follow-ups -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-chart-line-up text-purple-600"></i>
                                    Lead Details
                                </h3>
                                @haspermission('lead:edit')
                                    <a href="{{ route('team.client.new.lead', $client->id) }}" class="kt-btn kt-btn-sm kt-btn-primary" target="_blank">
                                        <i class="ki-filled ki-plus mr-1"></i>
                                        New Lead
                                    </a>
                                @endhaspermission
                            </x-slot>

                            @if($client->leads && $client->leads->count() > 0)
                            <div class="space-y-6">
                                @foreach($client->leads as $lead)
                                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                                    <!-- Lead Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex justify-between mb-2">
                                                <h5 class="text-md font-semibold text-gray-900">
                                                    {{ $lead->getPurpose ? $lead->getPurpose->name : 'Unknown Purpose' }}
                                                </h5>
                                                <div>
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $lead->status == 'active' ? 'bg-green-100 text-green-800' : ($lead->status == 'closed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                        {{ $lead?->getStatus?->name ?: 'Pending' }}
                                                        -
                                                        {{ $lead?->getSubStatus?->name ?: 'N/A' }}
                                                    </span>
                                                    @haspermission('lead:edit')
                                                        <a href="{{ route('team.lead.edit', $lead->id) }}" class="kt-btn kt-btn-sm kt-btn-primary ml-2">
                                                            <i class="ki-filled ki-pencil"></i>
                                                        </a>
                                                    @endhaspermission
                                                    @if($lead->purpose == '2')
                                                        @haspermission('lead:edit')
                                                            <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_demo" data-client-lead-id="{{ $lead->id }}">
                                                                <i class="ki-filled ki-plus"></i>
                                                                    Add Demo
                                                            </button>
                                                        @endhaspermission
                                                    @endif
                                                    <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#register_data" data-client-lead-id="{{ $lead->id }}">
                                                        <i class="ki-filled ki-plus"></i>
                                                            Now Register
                                                    </button>
                                                </div>

                                            </div>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm border-t border-gray-100 pt-4">
                                                @if($lead->getForeignCountry)
                                                    @php
                                                        $secondCountryIds = explode(',', $lead->second_country); // 1,2 ko [1,2] me badalna
                                                        $countries = $lead->getForeignCountry
                                                            ->whereIn('id', $secondCountryIds) // sirf selected IDs ka data
                                                            ->pluck('name')
                                                            ->implode(', ');
                                                    @endphp

                                                    <div>
                                                        <span class="text-gray-500">Country:</span>
                                                        <div class="font-medium">{{ $lead->getForeignCountry ? $lead->getForeignCountry->name : 'N/A' }}, {{ $countries }}</div>
                                                    </div>
                                                @endif
                                                @if($lead->coaching && $lead->getCoaching)
                                                    <div>
                                                        <span class="text-gray-500">Coaching:</span>
                                                        <div class="font-medium">{{ $lead->getCoaching ? $lead->getCoaching->name : 'N/A' }}</div>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-gray-500">Date:</span>
                                                    <div class="font-medium">{{ $lead->client_date ? date('M d, Y', strtotime($lead->client_date)) : 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Owner:</span>
                                                    <div class="font-medium">{{ $lead->assignedOwner ? $lead->assignedOwner->name : 'Unassigned' }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Branch:</span>
                                                    <div class="font-medium">{{ $lead->getBranch ? $lead->getBranch->branch_name : 'N/A' }}</div>
                                                </div>
                                            </div>

                                            @if($lead->remark)
                                            <div class="mt-2 p-2 bg-gray-50 rounded text-sm">
                                                <strong>Remark:</strong> {{ $lead->remark }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Follow-ups Section -->
                                    @if($lead->getFollowUps && $lead->getFollowUps->count() > 0)
                                    <div class="border-t border-gray-100 pt-4">
                                        <h6 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                                            <i class="ki-filled ki-calendar text-blue-500"></i>
                                            Follow-ups ({{ $lead->getFollowUps->count() }})
                                        </h6>
                                        <div class="space-y-3 max-h-60 overflow-y-auto">
                                            @foreach($lead->getFollowUps->sortByDesc('followup_date')->take(5) as $followup)
                                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="ki-filled ki-message-text text-blue-600 text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-sm font-medium text-gray-900">
                                                            {{ $followup->followup_date ? date('M d, Y', strtotime($followup->followup_date)) : 'No Date' }}
                                                        </span>
                                                        @if(isset($followup->status))
                                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                                {{ $followup->status == 0 ? 'bg-yellow-100 text-yellow-800' : ($followup->status == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ $followup->status == 0 ? 'Pending' : ($followup->status == 1 ? 'Completed' : ucfirst($followup->status)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($followup->communication)
                                                    <div class="text-xs text-blue-600 mb-1">
                                                        <i class="ki-filled ki-phone mr-1"></i>
                                                        {{ ucfirst($followup->communication) }}
                                                    </div>
                                                    @endif
                                                    @if($followup->remarks)
                                                    <p class="text-sm text-gray-600">{{ $followup->remarks }}</p>
                                                    @endif
                                                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                                        <span>By: {{ $followup->createdByUser ? $followup->createdByUser->name : 'Unknown' }}</span>
                                                        <span>{{ $followup->created_at ? $followup->created_at->format('h:i A') : '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            @if($lead->getFollowUps->count() > 5)
                                            <div class="text-center">
                                                <button class="text-sm text-blue-600 hover:text-blue-800">
                                                    View all {{ $lead->getFollowUps->count() }} follow-ups
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="border-t border-gray-100 pt-4">
                                        <div class="text-center py-4 text-gray-500">
                                            <i class="ki-filled ki-calendar text-2xl text-gray-300 mb-2"></i>
                                            <p class="text-sm">No follow-ups recorded yet</p>
                                            <button type="button"
                                                data-kt-modal-toggle="#add-followUp"
                                                data-form_action="{{ route('team.lead-follow-up.store',['client_lead_id' => $lead->id]) }}"
                                                class="mt-2 kt-btn kt-btn-sm kt-btn-light open-followup-modal">
                                                <i class="ki-filled ki-plus text-xs mr-1"></i>Follow-up
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-12">
                                <i class="ki-filled ki-chart-line-up text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-600 mb-2">No leads found</h4>
                                <p class="text-gray-500 mb-4">This client doesn't have any leads yet.</p>
                                <button class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-plus mr-2"></i>
                                    Create New Lead
                                </button>
                            </div>
                            @endif
                        </x-team.card>

                        <!-- Education Details -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-graduation-cap text-purple-600"></i>
                                    Education Details
                                </h3>
                                @haspermission('lead:edit')
                                    @if(isset($client->educationDetails) && $client->educationDetails->count() > 0)
                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_education">
                                            <i class="ki-filled ki-pencil mr-1"></i>
                                                Edit Education
                                        </button>
                                    @endif
                                @endhaspermission
                            </x-slot>

                            @if($client->educationDetails && $client->educationDetails->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Education Level</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Stream</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Passing Year</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Result</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Backlog</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Institute/Board</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client->educationDetails as $education)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4">
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ $education->getEducationLevel ? $education->getEducationLevel->name : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="text-sm text-gray-700">
                                                    {{ $education->getEducationStream ? $education->getEducationStream->name : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="text-sm text-gray-700">
                                                    {{ $education->passing_year ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($education->result)
                                                <span class="text-sm text-gray-700">
                                                    {{ $education->result }}
                                                </span>
                                                @else
                                                <span class="text-sm text-gray-500">N/A</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($education->no_of_backlog)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    {{ $education->no_of_backlog }}
                                                </span>
                                                @else
                                                <span class="text-sm text-gray-500">0</span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-4">
                                                <span class="text-sm text-gray-700">
                                                    @if ($education->institute!='')
                                                        {{ $education->institute ?? 'N/A' }}
                                                    @endif

                                                    @if($education->getEducationBoard && $education->getEducationBoard?->name)
                                                        {{ $education->getEducationBoard->name }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="ki-filled ki-graduation-cap text-3xl text-gray-300 mb-3"></i>
                                <h4 class="text-sm font-medium text-gray-600 mb-1">No Education Details</h4>
                                <p class="text-xs text-gray-500 mb-3">No education information has been added yet.</p>
                                @haspermission('lead:edit')
                                    <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_education">
                                        <i class="ki-filled ki-plus mr-1"></i>
                                            Add Education
                                    </button>
                                @endhaspermission
                            </div>
                            @endif
                        </x-team.card>

                        <!-- English Proficiency Tests -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-message-text text-indigo-600"></i>
                                    English Proficiency Tests
                                </h3>
                                @haspermission('lead:edit')
                                    @if(isset($client->examData) && $client->examData->count() > 0)
                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_english_test">
                                            <i class="ki-filled ki-pencil mr-1"></i>
                                                Edit Test
                                        </button>
                                    @endif
                                @endhaspermission
                            </x-slot>

                            @php
                                $englishTests = collect();
                                foreach($client->leads as $lead) {
                                    $englishTests = $englishTests->merge($lead->examData);
                                }
                            @endphp

                            @if($englishTests->count() > 0)
                            <div class="space-y-4">
                                @foreach($englishTests as $test)
                                <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between mb-3">
                                        <h5 class="font-medium text-gray-900">{{ $test->getExam?->name ? '' . $test->getExam->name : 'N/A' }}</h5>
                                        @if($test->exam_date)
                                            <p class="text-sm text-gray-500">
                                                <i class="ki-filled ki-calendar text-gray-400 mr-1"></i>
                                                {{ date('d/m/Y', strtotime($test->exam_date)) }}
                                            </p>
                                        @endif
                                    </div>

                                    @if($test->exam_dataScore && $test->exam_dataScore->count() > 0)
                                    <div class="grid grid-cols-4 gap-2 text-sm">
                                        @foreach($test->exam_dataScore as $score)
                                        <div class="">
                                            <span class="text-gray-600">{{ $score->getTestScoreName?->name ?? 'Score' }}:</span>
                                            <span class="font-medium">{{ $score->score ?? 'N/A' }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="ki-filled ki-message-text text-3xl text-gray-300 mb-3"></i>
                                <h4 class="text-sm font-medium text-gray-600 mb-1">No English Tests</h4>
                                <p class="text-xs text-gray-500 mb-3">No English proficiency tests recorded yet.</p>
                                @haspermission('lead:edit')
                                    <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_english_test">
                                        <i class="ki-filled ki-plus mr-1"></i>
                                            Add Test
                                    </button>
                                @endhaspermission
                            </div>
                            @endif
                        </x-team.card>

                        <!-- Employment Information -->
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-briefcase text-green-600"></i>
                                    Employment Information
                                </h3>
                                @haspermission('lead:edit')
                                    @if(isset($client->employmentDetails) && $client->employmentDetails->count() > 0)
                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_employment">
                                            <i class="ki-filled ki-pencil mr-1"></i>
                                                Edit Employment
                                        </button>
                                    @endif
                                @endhaspermission
                            </x-slot>

                            @if($client->employmentDetails && $client->employmentDetails->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Company Name</th>
                                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Designation</th>
                                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Duration</th>
                                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Experience</th>
                                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($client->employmentDetails as $employment)
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <td class="py-3 px-4">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ $employment->company_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="text-sm text-gray-700">
                                                        {{ $employment->designation ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($employment->start_date)
                                                    <span class="text-sm text-gray-700">
                                                        <i class="ki-filled ki-calendar text-gray-400 mr-1"></i>
                                                        {{ date('M Y', strtotime($employment->start_date)) }} - {{ $employment->end_date ? date('M Y', strtotime($employment->end_date)) : 'Present' }}
                                                    </span>
                                                    @else
                                                    <span class="text-sm text-gray-500">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($employment->no_of_year)
                                                    <span class="text-sm text-gray-700">
                                                        <i class="ki-filled ki-time text-gray-400 mr-1"></i>
                                                        {{ $employment->no_of_year }} years
                                                    </span>
                                                    @else
                                                    <span class="text-sm text-gray-500">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($employment->is_working)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Current
                                                    </span>
                                                    @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        Past
                                                    </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                            <div class="text-center py-8">
                                <i class="ki-filled ki-briefcase text-3xl text-gray-300 mb-3"></i>
                                <h4 class="text-sm font-medium text-gray-600 mb-1">No Employment Details</h4>
                                <p class="text-xs text-gray-500 mb-3">No employment information has been added yet.</p>
                                @haspermission('lead:edit')
                                    <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#add_employment">
                                        <i class="ki-filled ki-plus mr-1"></i>
                                            Add Employment
                                    </button>
                                @endhaspermission

                            </div>
                            @endif
                        </x-team.card>
                    </div>
                    <!-- Middle Column - Education & Employment -->
                    <div class="lg:col-span-8 space-y-6 order-3 lg:order-3">

                    </div>
                    <!-- Right Column - Leads & Activities -->
                </div>
            </div>
        </div>

        <x-team.lead.add-follow-up
            id="add-followUp"
            title="Add Follow-up"
            formId="followUpForm"
            :client_lead_id="request('client_lead_id')"
        />



        {{-- Education Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_education">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->educationDetails) && $client->educationDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Education
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_education">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.education.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="educationModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] flex items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_education">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- English Proficiency Test --}}
        <div class="kt-modal" data-kt-modal="true" id="add_english_test">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->examData) && $client->examData->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Test
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_english_test">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.english.proficiency.tests.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="EnglishTestModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] flex items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_english_test">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>


        {{-- Employment Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_employment">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->educationDetails) && $client->educationDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Employment
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_employment">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.employment.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="EmploymentModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] flex items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_employment">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>


        {{-- Passport Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_passport">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->passportDetails) && $client->passportDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Passport
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_passport">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.passport.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="passportModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] flex items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_passport">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- Rejection Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_rejection_country">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->visaRejectionDetails) && $client->visaRejectionDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Rejection Country
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_rejection_country">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.rejection.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="rejectionModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] items-center justify-center p-4">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_rejection_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- Relative Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_relative_country">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->getClientRelativeDetails) && $client->getClientRelativeDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Relative Country
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_relative_country">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.relative.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="relativeModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] flex items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_relative_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- Visited Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_visited_country">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                @if(isset($client->anyVisitedDetails) && $client->anyVisitedDetails->count() > 0)
                    Edit
                @else
                    Add
                @endif
                Visited Country
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_visited_country">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.visited.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="visitedModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] p-4 items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_visited_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>


        {{-- Demo Model --}}
        <div class="kt-modal" data-kt-modal="true" id="add_demo">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                    Add Demo
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_visited_country">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.demo.details.profile',$client->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div id="DemoModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_visited_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{-- Register Model --}}
        <div class="kt-modal" data-kt-modal="true" id="register_data">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                </h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_visited_country">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
                <div class="kt-modal-body">

                <form action="{{ route('team.register.details.profile',$client->id) }}" method="POST" class="form" >
                    @csrf
                    <div id="RegisterModalContent" class="rounded-lg bg-muted w-full grow min-h-[22px] items-center justify-center">
                        Loading...
                    </div>

                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_visited_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

    </x-slot>

    <!-- Tab JavaScript -->
    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabLinks = document.querySelectorAll('.tab-link');
                const tabPanels = document.querySelectorAll('.tab-panel');

                tabLinks.forEach(tabLink => {
                    tabLink.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');

                        // Remove active class from all tab links
                        tabLinks.forEach(link => {
                            link.classList.remove('active', 'text-gray-900', 'border-primary');
                            link.classList.add('text-gray-500', 'border-transparent');
                        });

                        // Hide all tab panels
                        tabPanels.forEach(panel => {
                            panel.classList.add('hidden');
                            panel.classList.remove('active');
                        });

                        // Add active class to clicked tab
                        this.classList.add('active', 'text-gray-900', 'border-primary');
                        this.classList.remove('text-gray-500', 'border-transparent');

                        // Show target panel
                        const targetPanel = document.getElementById(targetTab);
                        if (targetPanel) {
                            targetPanel.classList.remove('hidden');
                            targetPanel.classList.add('active');
                        }
                    });
                });
            });
        </script>
    </x-slot>

</x-team.layout.app>


<script>
    $(document).ready(function () {
        $('#avatar-input').on('change', function () {
            let form = $('#avatar-upload-form')[0];
            let formData = new FormData(form);

            $.ajax({
                url: '{{ route('team.avatar.upload') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success && response.avatar_url) {
                        $('#avatar-preview').html(
                            `<img src="${response.avatar_url}" class="w-full h-full object-cover rounded-full" alt="Avatar">`
                        );
                    } else {
                        alert('Upload failed.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert('Error uploading image.');
                }
            });
        });
    });
</script>

<script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
<script>
    $(document).ready(function () {
        const clientId = @json($client->id);

        // Open modal and load education form via AJAX
        $('[data-kt-modal-toggle="#add_education"]').on('click', function () {
            $('#educationModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.education.form') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#educationModalContent').html(response);
                    initializeEducationRepeater();
                    EducationLevelAjax();
                },
                error: function () {
                    $('#educationModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load English Test form via AJAX
        $('[data-kt-modal-toggle="#add_english_test"]').on('click', function () {
            $('#EnglishTestModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.english.proficiency.test') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#EnglishTestModalContent').html(response);
                    EnglishProficiencyFormValidation();
                    initFlatpickr();
                },
                error: function () {
                    $('#EnglishTestModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load employment form via AJAX
        $('[data-kt-modal-toggle="#add_employment"]').on('click', function () {
            $('#EmploymentModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.employment.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#EmploymentModalContent').html(response);
                    EmploymentDate();
                    initFlatpickr();
                    $("#is_employmentCheckbox").prop("checked", true);
                },
                error: function () {
                    $('#EmploymentModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load passport form via AJAX
        $('[data-kt-modal-toggle="#add_passport"]').on('click', function () {
            $('#passportModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.passport.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#passportModalContent').html(response);
                    PassportDate();
                    initFlatpickr();
                    $("#passportCheckbox").prop("checked", true);
                },
                error: function () {
                    $('#passportModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load rejection form via AJAX
        $('[data-kt-modal-toggle="#add_rejection_country"]').on('click', function () {
            $('#rejectionModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.rejection.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#rejectionModalContent').html(response);
                    RejectionCountry();
                    $("#is_visa_rejectionCheckbox").prop("checked", true);
                },
                error: function () {
                    $('#rejectionModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load relative form via AJAX
        $('[data-kt-modal-toggle="#add_relative_country"]').on('click', function () {
            $('#relativeModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.relative.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#relativeModalContent').html(response);
                    RelativeDataCountry();
                    $("#is_relativeCheckbox").prop("checked", true);
                },
                error: function () {
                    $('#relativeModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load visited Country form via AJAX
        $('[data-kt-modal-toggle="#add_visited_country"]').on('click', function () {
            $('#visitedModalContent').html('Loading...');

            $.ajax({
                url: '{{ route('team.get.visited.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function (response) {
                    $('#visitedModalContent').html(response);
                    VsisitedCountry();
                    initFlatpickr();
                    $("#is_visitedCheckbox").prop("checked", true);
                },
                error: function () {
                    $('#visitedModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load Demo form via AJAX
        $('[data-kt-modal-toggle="#add_demo"]').on('click', function () {
            $('#DemoModalContent').html('Loading...');
            var clientLeadId = $(this).data('client-lead-id');
            $.ajax({
                url: '{{ route('team.get.demo.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId,
                    client_lead_id: clientLeadId
                },
                success: function (response) {
                    $('#DemoModalContent').html(response);
                    initFlatpickr();
                    DemoDataBatch();
                },
                error: function () {
                    $('#DemoModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        // Open modal and load Registration form via AJAX
        $('[data-kt-modal-toggle="#register_data"]').on('click', function () {
            $('#RegisterModalContent').html('Loading...');
            var clientLeadId = $(this).data('client-lead-id');
            var clientRegId = $(this).data('reg-id');
            $.ajax({
                url: '{{ route('team.get.register.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId,
                    client_lead_id: clientLeadId,
                    client_reg_id: clientRegId,
                },
                success: function (response) {
                    $('#RegisterModalContent').html(response);
                    initFlatpickr();
                    registerData();
                },
                error: function () {
                    $('#RegisterModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        function registerData(){
            const $purposeSelect = $('select[name="purpose"]');
            const $countryDiv = $('#countryDiv');
            const $SecondcountryDiv = $('#SecondcountryDiv');
            const $coachingDiv = $('#coachingDiv');

            const $countrySelect = $('select[name="country"]');
            const $coachingSelect = $('select[name="coaching"]');

            if ($purposeSelect.length === 0 || $countrySelect.length === 0 || $coachingSelect.length === 0) {
                console.error('Required elements not found. Check your form element names.');
                return;
            }
            function toggleFields(value) {
                if (value === '2') {
                    // Show coaching
                    $coachingDiv.show();
                    $coachingSelect.prop('required', true);

                    // Hide country
                    $countryDiv.hide();
                    $SecondcountryDiv.hide();
                    $countrySelect.prop('required', false).val('').trigger('change');
                } else {
                    // Show country
                    $countryDiv.show();
                    $SecondcountryDiv.show();
                    $countrySelect.prop('required', true);

                    // Hide coaching
                    $coachingDiv.hide();
                    $coachingSelect.prop('required', false).val('').trigger('change');
                }
            }
            $purposeSelect.on('change', function () {
                toggleFields($(this).val());
            });
            // Trigger once on page load
            toggleFields($purposeSelect.val());
        }

        function DemoDataBatch() {

            $(document).ready(function () {
                $('#coaching_select').on('change', function () {
                    var coachingId = $(this).val();

                    // Clear previous options
                    $('#batch_select').empty().append('<option value="">Loading...</option>');

                    if (coachingId) {
                        $.ajax({
                            url: '{{ route('team.get.coaching.batch') }}', // Create this route
                            type: 'GET',
                            data: { coaching_id: coachingId },
                            success: function (response) {
                                $('#batch_select').empty().append('<option value="">Select batch</option>');
                                $.each(response, function (key, batch) {
                                    $('#batch_select').append(
                                        $('<option>', {
                                            value: batch.id,
                                            text: batch.name
                                        })
                                    );
                                });
                            },
                            error: function () {
                                $('#batch_select').empty().append('<option value="">No batches found</option>');
                            }
                        });
                    } else {
                        $('#batch_select').empty().append('<option value="">Select batch</option>');
                    }
                });
            });
        }

        function VsisitedCountry(){
            $(document).ready(function () {
                const $isVisitedCheckbox = $('#is_visitedCheckbox');
                const $visitedSection = $('#visited-section');

                function toggleVisitedSection() {
                    if ($isVisitedCheckbox.is(':checked')) {
                        $visitedSection.removeClass('hidden');
                        $visitedSection.find('select, input').attr('required', 'required');
                    } else {
                        $visitedSection.addClass('hidden');
                        $visitedSection.find('select, input').removeAttr('required');
                    }
                }

                $isVisitedCheckbox.on('change', toggleVisitedSection);
                toggleVisitedSection(); // On load

                function initFlatpickr($container) {
                    $container.find('.flatpickr').each(function () {
                        flatpickr(this, {
                            dateFormat: "d/m/Y",
                        });
                    });
                }
                // Init repeater

                $('#visited-repeater').repeater({
                    initEmpty: false,
                    defaultValues: {
                        'visited_country': '',
                        'visited_visa_type': '',
                        'start_date': '',
                        'end_date': '',
                    },
                    show: function () {
                        $(this).slideDown();

                        // Assign dynamic IDs
                        $(this).find('input, select, textarea').each(function () {
                            var name = $(this).attr('name');
                            if (name) {
                                var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                                $(this).attr('id', id);
                            }
                        });

                        // Re-initialize Select2
                        $(this).find('select').each(function () {
                            var $select = $(this);
                            $select.parent().find('.select2-container').remove();
                            $select.select2({ width: '100%' });
                        });
                        initFlatpickr($(this));
                    },
                    hide: function (deleteElement) {
                        $(this).find('select').each(function () {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                        });
                        $(this).slideUp(deleteElement);
                    }
                });
            });
        }
        function RelativeDataCountry(){
            $(document).ready(function () {
                var $checkbox = $('#is_relativeCheckbox');
                var $relativeFields = $('#relative-fields');
                var $requiredInputs = $('#relative_relationship, #relative_country, #visa_type');

                function toggleRelativeFields() {
                    if (!$checkbox.length || !$relativeFields.length) return;

                    if ($checkbox.is(':checked')) {
                        $relativeFields.removeClass('hidden');
                        $requiredInputs.each(function () {
                            $(this).attr('required', 'required');
                        });
                    } else {
                        $relativeFields.addClass('hidden');
                        $requiredInputs.each(function () {
                            $(this).removeAttr('required');
                        });
                    }
                }

                // Initial load check
                toggleRelativeFields();

                // On checkbox change
                $checkbox.on('change', toggleRelativeFields);
            });
        }

        function RejectionCountry(){
            $(document).ready(function () {
                const $isRejectionCheckbox = $('#is_visa_rejectionCheckbox');
                const $rejectionSection = $('#visa-rejection-section');

                function toggleRejectionSection() {
                    if ($isRejectionCheckbox.is(':checked')) {
                        $rejectionSection.removeClass('hidden');
                        $rejectionSection.find('select, input').attr('required', 'required');
                    } else {
                        $rejectionSection.addClass('hidden');
                        $rejectionSection.find('select, input').removeAttr('required');
                    }
                }
                $isRejectionCheckbox.on('change', toggleRejectionSection);
                toggleRejectionSection();


                $('#visa-rejection-repeater').repeater({
                    initEmpty: false,
                    defaultValues: {
                        'rejection_country': '',
                        'rejection_month_year': '',
                        'rejection_visa_type': ''
                    },
                    show: function () {
                        $(this).slideDown();

                        // Assign dynamic IDs
                        $(this).find('input, select, textarea').each(function () {
                            var name = $(this).attr('name');
                            if (name) {
                                var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                                $(this).attr('id', id);
                            }
                        });

                        // Re-initialize Select2
                        $(this).find('select').each(function () {
                            var $select = $(this);
                            $select.parent().find('.select2-container').remove();
                            $select.select2({ width: '100%' });
                        });
                    },
                    hide: function (deleteElement) {
                        $(this).find('select').each(function () {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                        });
                        $(this).slideUp(deleteElement);
                    }
                });
            });
        }

        function PassportDate(){
            $(document).ready(function () {
                const $checkbox = $('#passportCheckbox');
                const $passportFields = $('#passportFields');
                const $requiredInputs = [
                    $('#passport_number'),
                    $('#passport_expiry_date'),
                    $('#passport_copy')
                ].filter(function ($el) {
                    return $el.length > 0; // Ensure element exists
                });

                function togglePassportFields() {
                    if ($checkbox.length === 0 || $passportFields.length === 0) return;

                    if ($checkbox.is(':checked')) {
                        $passportFields.removeClass('hidden');
                        $requiredInputs.forEach(function ($input) {
                            if ($input.attr('type') !== 'file') {
                                $input.attr('required', 'required');
                            }
                        });
                    } else {
                        $passportFields.addClass('hidden');
                        $requiredInputs.forEach(function ($input) {
                            $input.removeAttr('required');
                            if ($input.attr('type') !== 'file') {
                                $input.val('');
                            }
                        });
                    }
                }

                // Initial check
                togglePassportFields();

                // Bind change event
                $checkbox.on('change', togglePassportFields);
            });
        }

        function EmploymentDate(){
            $(document).ready(function () {
                const $isEmploymentCheckbox = $('#is_employmentCheckbox');
                const $employmentSection = $('#employment-section');
                const $employmentRepeater = $('#employment-repeater');

                // Toggle section based on main checkbox
                function toggleEmploymentSection() {
                    const isChecked = $isEmploymentCheckbox.is(':checked');
                    $employmentSection.toggleClass('hidden', !isChecked);

                    $employmentRepeater.find('.employment-item').each(function () {
                        const $item = $(this);
                        const $isWorking = $item.find('[name*="[is_working]"], [name="is_working"]');

                        $item.find('[name*="[company_name]"], [name="company_name"]').prop('required', isChecked);
                        $item.find('[name*="[designation]"], [name="designation"]').prop('required', isChecked);
                        $item.find('[name*="[start_date]"], [name="start_date"]').prop('required', isChecked);

                        toggleEndDateVisibility($item);
                    });
                }

                // Show/hide end date & years based on employment + working status
                function toggleEndDateVisibility($item) {
                    const isEmploymentChecked = $isEmploymentCheckbox.is(':checked');
                    const isWorkingChecked = $item.find('[name*="[is_working]"], [name="is_working"]').is(':checked');

                    const $endDateWrapper = $item.find('.field-end-date');
                    const $noOfYearWrapper = $item.find('.field-no-of-year');
                    const $endDate = $item.find('[name*="[end_date]"], [name="end_date"]');
                    const $noOfYear = $item.find('[name*="[no_of_year]"], [name="no_of_year"]');

                    if (isEmploymentChecked && !isWorkingChecked) {
                        $endDateWrapper.removeClass('hidden');
                        $noOfYearWrapper.removeClass('hidden');
                        $endDate.prop('required', true);
                        $noOfYear.prop('required', true);
                    } else {
                        $endDateWrapper.addClass('hidden');
                        $noOfYearWrapper.addClass('hidden');
                        $endDate.prop('required', false).val('');
                        $noOfYear.prop('required', false).val('');
                    }
                }

                // Flatpickr init
                function initializeFlatpickr($container) {
                    $container.find('.flatpickr').each(function () {
                        flatpickr(this, {
                            dateFormat: 'd/m/Y'
                        });
                    });
                }

                toggleEmploymentSection();

                // Main checkbox change
                $isEmploymentCheckbox.on('change', toggleEmploymentSection);

                // Existing working checkboxes
                $employmentRepeater.find('.employment-item').each(function () {
                    const $item = $(this);
                    $item.find('[name*="[is_working]"], [name="is_working"]').on('change', function () {
                        toggleEndDateVisibility($item);
                    });
                    toggleEndDateVisibility($item);
                });

                // Repeater
                $('#employment-repeater').repeater({
                    initEmpty: false,
                    defaultValues: {
                        'company_name': '',
                        'designation': '',
                        'start_date': ''
                    },
                    show: function () {
                        $(this).slideDown();

                        const $item = $(this);
                        const index = $employmentRepeater.find('.employment-item').length - 1;

                        // Rename inputs
                        $item.find('input, select, textarea').each(function () {
                            let name = $(this).attr('name');
                            if (name && name.indexOf('[') === -1) {
                                name = `employment[${index}][${name}]`;
                                $(this).attr('name', name);
                                $(this).attr('id', name.replace(/\[/g, '_').replace(/\]/g, ''));
                            }
                        });

                        $item.addClass('employment-item');

                        initializeFlatpickr($item);
                        toggleEndDateVisibility($item);

                        $item.find('[name*="[is_working]"], [name="is_working"]').on('change', function () {
                            toggleEndDateVisibility($item);
                        });

                        $item.find('select').each(function () {
                            const $select = $(this);
                            $select.parent().find('.select2-container').remove();
                            $select.select2({ width: '100%' });
                        });

                        $employmentRepeater.find('.employment-item').each(function (i) {
                            $(this).find('.remove-employment').toggleClass('hidden', i === 0);
                        });
                    },
                    hide: function (deleteElement) {
                        const $item = $(this);
                        $item.find('select').each(function () {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                        });
                        $item.slideUp(deleteElement);
                    }
                });
            });
        }

        function initFlatpickr() {
            flatpickr(".flatpickr", {
                dateFormat: "d/m/Y",
                allowInput: true
            });
        }

        function EnglishProficiencyFormValidation(){

            $(document).ready(function () {
                function toggleModules() {
                    $('.test-checkbox').each(function () {
                        var $checkbox = $(this);
                        var $target = $($checkbox.data('target'));

                        if ($checkbox.is(':checked')) {
                            $target.show();
                            $target.find('input[type="text"]').prop('required', true);
                        } else {
                            $target.hide();
                            $target.find('input[type="text"]').prop('required', false).val('');
                        }
                    });
                }

                toggleModules();
                $('.test-checkbox').on('change', toggleModules);
                function validateScore(input) {
                    const $input = $(input);
                    const $errorMsg = $input.closest('.relative').find('.error-message');
                    const value = parseFloat($input.val());
                    const min = parseFloat($input.data('min'));
                    const max = parseFloat($input.data('max'));
                    const step = parseFloat($input.data('step'));

                    // Clear previous error
                    $errorMsg.addClass('hidden').text('');
                    $input.removeClass('border-red-500');

                    if (!$input.val()) {
                        return true; // Let required validation handle empty values
                    }

                    if (isNaN(value)) {
                        $errorMsg.removeClass('hidden').text('Please enter a valid number');
                        $input.addClass('border-red-500');
                        return false;
                    }

                    if (value < min || value > max) {
                        $errorMsg.removeClass('hidden').text(`Score must be between ${min} and ${max}`);
                        $input.addClass('border-red-500');
                        return false;
                    }

                    // Check if value follows the step increment
                    const remainder = (value - min) % step;
                    console.log(`Value: ${value}, Min: ${min}, Step: ${step}, Remainder: ${remainder}`);

                    if (Math.abs(remainder) > 0.001 && Math.abs(remainder - step) > 0.001) {
                        $errorMsg.removeClass('hidden').text(`Score must be in increments of ${step}`);
                        $input.addClass('border-red-500');
                        return false;
                    }

                    return true;
                }

                // Attach validation to score inputs
                $(document).on('input blur', '.score-input', function() {
                    validateScore(this);
                });
            });

        }

        function initializeEducationRepeater() {
            $('#education-repeater').repeater({
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
        }

        function EducationLevelAjax(){
            $(document).on('change', '.education_level', function () {
                var selectedLevel = $(this).val();
                var $parent = $(this).closest('[data-repeater-item]');
                handleEducationLevelChange($parent, selectedLevel);
            });

            // Function to load streams and toggle fields
            function handleEducationLevelChange($parent, selectedLevel, selectedStream = null) {
                if (!selectedLevel) return;

                var $streamSelect = $parent.find('.education_stream');

                $.ajax({
                    url: '/team/get-education-streams/' + selectedLevel,
                    type: 'GET',
                    success: function (response) {
                        $streamSelect.empty().append('<option value="">Select Stream</option>');

                        $.each(response.streams || {}, function (key, value) {
                            const isSelected = selectedStream && selectedStream == key ? 'selected' : '';
                            $streamSelect.append(`<option value="${key}" ${isSelected}>${value}</option>`);
                        });

                        var requiredDetails = response.required_details || [];
                        var allFields = ['board', 'language', 'stream', 'passing_year', 'result', 'no_of_backlog', 'institute'];

                        allFields.forEach(function (field) {
                            var $fieldWrapper = $parent.find('.field-' + field);
                            if (requiredDetails.includes(field)) {
                                $fieldWrapper.show();
                                $fieldWrapper.find('input, select').attr('required', true);
                            } else {
                                $fieldWrapper.hide();
                                $fieldWrapper.find('input, select').removeAttr('required');
                            }
                        });
                    }
                });
            }

            // Initialize existing data on page load (edit mode)
            $('[data-repeater-item]').each(function () {
                var $parent = $(this);
                var selectedLevel = $parent.find('.education_level').val();
                var selectedStream = $parent.find('.education_stream').val(); // already selected value from backend
                handleEducationLevelChange($parent, selectedLevel, selectedStream);
            });
        }
    });

</script>
