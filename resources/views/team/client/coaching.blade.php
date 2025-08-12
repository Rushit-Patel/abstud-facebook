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
                            <!-- Personal Information -->
                            <x-team.card>
                                <x-slot name="header">
                                    <h3 class="text-base font-semibold flex items-center gap-2">
                                        <i class="ki-filled ki-user text-blue-600"></i>
                                        Demo Details
                                    </h3>
                                </x-slot>
                                <div class="space-y-3">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <tbody class="divide-y divide-gray-100">
                                                @if($client->getDemoCoaching->count())
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Coaching</th>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Batch</th>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Demo Date</th>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Assigned Owner</th>
                                                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                                                                    @haspermission('demo:edit')
                                                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                                                                    @endhaspermission
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach($client->getDemoCoaching as $index => $demo)
                                                                    <tr>
                                                                        <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                                                                        <td class="px-4 py-2 text-gray-800">{{ $demo->getDemoCoaching?->name ?? '-' }}</td>
                                                                        <td class="px-4 py-2 text-gray-800">{{ $demo->getDemoBatch?->name ?? '-' }}</td>
                                                                        <td class="px-4 py-2 text-gray-800">{{ $demo->demo_date ? date('d M Y', strtotime($demo->demo_date)) : '-' }}</td>
                                                                        <td class="px-4 py-2 text-gray-800">{{ $demo->getDemoAssignOwner?->name ?? '-' }}</td>
                                                                        <td class="px-4 py-2 text-gray-800">
                                                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $demo->status_color }}">
                                                                                {{ $demo->status_label ?? '-' }}
                                                                            </span>
                                                                        </td>
                                                                        @haspermission('demo:edit')
                                                                        <td class="px-4 py-2 text-gray-800">
                                                                            <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-modal-toggle="#edit_demo" data-demo-id="{{ $demo->id }}">
                                                                                <i class="ki-filled ki-pensile"></i>
                                                                                    Edit
                                                                            </button>
                                                                        </td>
                                                                        @endhaspermission
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4 text-gray-500">No demo details available.</div>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </x-team.card>

                        </div>
                        <!-- Right Column - Leads & Activities -->
                    </div>
            </div>
        </div>

        <div class="kt-modal" data-kt-modal="true" id="edit_demo">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                    Edit Demo
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
                    <div id="EditDemoModalContent" class="rounded-lg bg-muted w-full grow min-h-[250px] items-center justify-center">
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

<script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
<script>
    $(document).ready(function () {
        const clientId = @json($client->id);

        $('[data-kt-modal-toggle="#edit_demo"]').on('click', function () {
            $('#EditDemoModalContent').html('Loading...');
            var clientDemoId = $(this).data('demo-id');
            $.ajax({
                url: '{{ route('team.get.demo.details') }}',
                type: 'GET',
                data: {
                    client_id: clientId,
                    demo_id: clientDemoId
                },
                success: function (response) {
                    $('#EditDemoModalContent').html(response);
                    initFlatpickr();
                    const selectedBatchId = $('#EditDemoModalContent').find('#selected_batch').val(); // 👈 Get from hidden input
                    DemoDataBatch(selectedBatchId);

                },
                error: function () {
                    $('#EditDemoModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

        function DemoDataBatch(selectedBatchId = null) {
            $('#coaching_select').on('change', function () {
                var coachingId = $(this).val();
                $('#batch_select').empty().append('<option value="">Loading...</option>');

                if (coachingId) {
                    $.ajax({
                        url: '{{ route('team.get.coaching.batch') }}',
                        type: 'GET',
                        data: { coaching_id: coachingId },
                        success: function (response) {
                            $('#batch_select').empty().append('<option value="">Select batch</option>');
                            $.each(response, function (key, batch) {
                                $('#batch_select').append(
                                    $('<option>', {
                                        value: batch.id,
                                        text: batch.name,
                                        selected: selectedBatchId == batch.id // ✅ Preselect batch
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

            // ✅ Trigger change to auto-load if value exists on edit
            let selectedCoachingId = $('#coaching_select').val();
            if (selectedCoachingId) {
                $('#coaching_select').trigger('change');
            }
        }
        function initFlatpickr() {
            flatpickr(".flatpickr", {
                dateFormat: "d/m/Y",
                allowInput: true
            });
        }

    });
</script>
