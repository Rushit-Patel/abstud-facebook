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
                <!-- Overview Tab -->
                    <div class="grid grid-cols-1">
                        <!-- Left Column - Client Details -->
                        <div class="lg:col-span-4 space-y-6 order-2 lg:order-1">
                        @haspermission('invoice:create')
                            <x-team.card>
                                    <x-slot name="header">
                                        <h3 class="text-base font-semibold flex items-center gap-2">
                                            <i class="ki-filled ki-chart-line-up text-purple-600"></i>
                                            Invoice/Payent Details
                                        </h3>
                                        @if($client->getInvoice && $client->getInvoice->count() > 0)
                                            <div>
                                                @haspermission('invoice:create')
                                                    <button class="kt-btn kt-btn-sm kt-btn-primary ml-2" data-kt-modal-toggle="#invoice_data">
                                                        <i class="ki-filled ki-plus"></i>
                                                            New invoice
                                                    </button>
                                                @endhaspermission
                                            </div>
                                        @endif
                                    </x-slot>

                                    @if($client->getInvoice && $client->getInvoice->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Lead Purpose</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Invoice Date</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Service</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Total Amount</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Discount</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Payable Amount</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Billing Company</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Added By</th>
                                                        @if(auth()->user()->can('invoice:edit') || auth()->user()->can('invoice:delete'))
                                                            <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($client->getInvoice as $index => $invoice)
                                                        <tr>
                                                            <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->clientLead->getPurpose->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->invoice_date ? date('d M Y', strtotime($invoice->invoice_date)) : '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->getService?->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->total_amount ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->discount ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->payable_amount ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->getBillingcompany->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $invoice->AddedByOwner->name ?? '-' }}</td>

                                                            @if(auth()->user()->can('invoice:edit') || auth()->user()->can('invoice:delete'))
                                                                <td class="px-4 py-2 text-gray-800">
                                                                    @haspermission('invoice:edit')
                                                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#invoice_data" data-invoice-id="{{ $invoice->id }}">
                                                                            <i class="ki-filled ki-pencil"></i>
                                                                        </button>
                                                                    @endhaspermission
                                                                    @haspermission('invoice:delete')
                                                                        <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#invoice_delete_modal" data-form_action="{{route('team.invoice.Destroy', $invoice->id)}}">
                                                                            <i class="ki-filled ki-trash text-md"></i>
                                                                        </button>
                                                                    @endhaspermission
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    @else
                                    <div class="border-t border-gray-100 pt-4">
                                            <div class="text-center py-4 text-gray-500">
                                                <i class="ki-filled ki-calendar text-2xl text-gray-300 mb-2"></i>
                                                <p class="text-sm">No invoice found for this service.</p>
                                                @haspermission('invoice:create')
                                                    <button class="kt-btn kt-btn-sm kt-btn-primary mt-2" data-kt-modal-toggle="#invoice_data">
                                                        <i class="ki-filled ki-plus"></i>
                                                            Now genrate invoice
                                                    </button>
                                                @endhaspermission
                                            </div>
                                        </div>
                                    @endif
                                </x-team.card>
                            @endhaspermission


                            <x-team.card>
                                <x-slot name="header">
                                    <h3 class="text-base font-semibold flex items-center gap-2">
                                        <i class="ki-filled ki-chart-line-up text-purple-600"></i>
                                        Lead Registration Details
                                    </h3>
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
                                                    </div>

                                                </div>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm border-t border-gray-100 pt-4">
                                                    @if($lead->getForeignCountry)
                                                        <div>
                                                            <span class="text-gray-500">Country:</span>
                                                            <div class="font-medium">{{ $lead->getForeignCountry ? $lead->getForeignCountry->name : 'N/A' }}</div>
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
                                        @if($lead->getRegister && $lead->getRegister->count() > 0)
                                        <hr>
                                        {{-- <h3 class="text-base font-semibold flex items-center gap-2">
                                            Register Details
                                        </h3> --}}
                                    <div class="flex items-center justify-between">
                                            <h3 class="text-base font-semibold flex items-center gap-2">
                                                Register Details
                                            </h3>
                                            <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#register_data" data-client-lead-id="{{ $lead->id }}">
                                                <i class="ki-filled ki-plus"></i>
                                                    Add Register
                                            </button>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Date</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Purpose</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Country</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Coaching</th>
                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Assigned Owner</th>
                                                        @haspermission('demo:edit')
                                                            <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                                                        @endhaspermission
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach ($lead->getRegister as $index => $register)
                                                        <tr>
                                                            <td class="px-4 py-2 text-gray-800">{{$index + 1 }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $register->reg_date ? date('d M Y', strtotime($register->reg_date)) : '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $register->getPurpose?->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $register->getForeignCountry?->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $register->getCoaching?->name ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-800">{{ $register->assignedOwner?->name ?? '-' }}</td>
                                                                <td class="px-4 py-2 text-gray-800">
                                                                    @haspermission('lead:edit')
                                                                        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#register_data" data-client-lead-id="{{ $lead->id }}" data-reg-id="{{ $register->id }}">
                                                                            <i class="ki-filled ki-pencil"></i>
                                                                                Edit
                                                                        </button>
                                                                    @endhaspermission
                                                                    @haspermission('lead:delete')
                                                                    <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#delete_modal" data-form_action="{{route('team.registrations.destroy', $register->id)}}">
                                                                        <i class="ki-filled ki-trash text-md"></i>
                                                                    </button>
                                                                    @endhaspermission
                                                                </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <div class="border-t border-gray-100 pt-4">
                                            <div class="text-center py-4 text-gray-500">
                                                <i class="ki-filled ki-calendar text-2xl text-gray-300 mb-2"></i>
                                                <p class="text-sm">No registration found for this Purpose.</p>
                                                {{-- <button class="mt-2 kt-btn kt-btn-sm kt-btn-light">
                                                    <i class="ki-filled ki-plus mr-1"></i>
                                                    Now register
                                                </button> --}}
                                                <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#register_data" data-client-lead-id="{{ $lead->id }}">
                                                    <i class="ki-filled ki-plus"></i>
                                                        Now Register
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </x-team.card>

                        </div>
                        <!-- Right Column - Leads & Activities -->
                    </div>
            </div>
        </div>

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

        <div class="kt-modal" data-kt-modal="true" id="invoice_data">
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

                <form action="{{ route('team.invoice.details.profile',$client->id) }}" method="POST" class="form" >
                    @csrf
                    <div id="invoiceModalContent" class="rounded-lg bg-muted w-full grow min-h-[22px] items-center justify-center">
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
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete Registration"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this Registration? This action cannot be undone."
/>

<x-team.modals.delete-modal
    id="invoice_delete_modal"
    title="Delete Invoice"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this invoice? This action cannot be undone."
/>


<script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
<script>
    $(document).ready(function () {
        const clientId = @json($client->id);

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

        $('[data-kt-modal-toggle="#invoice_data"]').on('click', function () {
            $('#invoiceModalContent').html('Loading...');
            var clientInvoiceId = $(this).data('invoice-id');
            $.ajax({
                url: '{{ route('team.get.invoice.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId,
                    client_invoice_id: clientInvoiceId,
                },
                success: function (response) {
                    $('#invoiceModalContent').html(response);
                    initFlatpickr();
                },
                error: function () {
                    $('#invoiceModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        function initFlatpickr() {
            flatpickr(".flatpickr", {
                dateFormat: "d/m/Y",
                allowInput: true
            });
        }

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

    });
</script>
