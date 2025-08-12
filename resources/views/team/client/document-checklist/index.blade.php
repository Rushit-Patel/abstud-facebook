@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Lead', 'url' => route('team.lead.index')],
        ['title' => $client->first_name . ' ' . $client->last_name . '\'s Profile']
    ];
@endphp

@push('styles')
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    <style>
        .assignee-item, .monitor-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f9fafb;
        }
        .assignee-item:last-child, .monitor-item:last-child {
            margin-bottom: 0;
        }
        .recurring-fields {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #f8fafc;
        }
        .due-date-field {

        }
        .tagify {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }
        .dropzone {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            transition: border-color 0.3s ease;
        }
        .dropzone.dz-drag-hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .dropzone .dz-message {
            font-size: 1rem;
            color: #6b7280;
        }
        .dropzone .dz-preview {
            margin: 0.5rem;
        }
    </style>
@endpush

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
                                            Document Check-list Details
                                        </h3>
                                        @if($client->getInvoice && $client->getInvoice->count() > 0)
                                            <div>
                                                @haspermission('lead:edit')
                                                    <button class="kt-btn kt-btn-sm kt-btn-primary ml-2" data-kt-modal-toggle="#upload_document_modal">
                                                        <i class="ki-filled ki-plus"></i>
                                                            Create Document Checklist
                                                    </button>
                                                @endhaspermission
                                            </div>
                                        @endif
                                    </x-slot>

                                    @if($client->documentChecklists && $client->documentChecklists->count() > 0)
                                        <form action="{{ route('team.document-status.update', $client->id) }}" method="POST">
                                            @csrf
                                            <div class="filter-table-start filter-form">
                                                <div class="overflow-x-auto shadow rounded-lg border border-gray-200">
                                                    <table class="w-full text-sm text-left text-gray-700">
                                                        <thead class="bg-gray-100 text-gray-800 uppercase text-xs font-semibold">
                                                            <tr>
                                                                <th class="px-4 py-3">Document Title</th>
                                                                <th class="px-4 py-3">Requirement</th>
                                                                <th class="px-4 py-3 w-[400px]">Notes</th>
                                                                <th class="px-4 py-3">Status</th>
                                                                <th class="px-4 py-3">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($client->documentChecklists as $index => $documentlist)
                                                                <!-- Category Row -->
                                                                <tr class="bg-blue-50">
                                                                    <td class="px-4 py-2 font-semibold text-blue-800">
                                                                        {{ $documentlist->document->name }}
                                                                    </td>
                                                                    <td class="px-4 py-2 font-semibold">
                                                                        {{ $documentlist->document_type }}
                                                                    </td>
                                                                    <td class="px-4 py-2 font-semibold">{{ $documentlist->notes }}</td>

                                                                    @php
                                                                        $type = [
                                                                            'request' => 'Request',
                                                                            'uploaded' => 'Uploaded',
                                                                            're-uploaded' => 'Re-Uploaded',
                                                                        ];

                                                                        // Pehle documentUploads ka status check karein, agar null ya empty ho to document_type ka use karein
                                                                        $selectedStatus = $documentlist->status;
                                                                    @endphp

                                                                    <td class="px-4 py-2 font-semibold">
                                                                        <x-team.forms.select
                                                                            name="studentDocument[{{ $documentlist->id }}][status]"
                                                                            label=""
                                                                            :options="$type"
                                                                            :selected="$selectedStatus"
                                                                            placeholder="Select type"
                                                                            searchable="true"
                                                                            id="document_type"
                                                                        />
                                                                    </td>

                                                                    <td class="px-4 py-2">
                                                                        @if($documentlist && $documentlist->documentUploads && count($documentlist->documentUploads) > 0)

                                                                            <button class="kt-btn kt-btn-primary" data-kt-modal-toggle="#viwe_document" data-client-document-check-lists="{{ $documentlist->id }}">
                                                                                <i class="ki-filled ki-eye"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                class="kt-btn kt-btn-primary open-upload-modal"
                                                                                data-checklist-id="{{ $documentlist->id }}"
                                                                                data-client-id="{{ $documentlist->client_id }}"
                                                                                data-document-check-list-master-id="{{ $documentlist->document_check_list_id }}">
                                                                                <i class="ki-filled ki-file-up"></i>
                                                                            </button>
                                                                        @else
                                                                            <button type="button"
                                                                                class="kt-btn kt-btn-primary open-upload-modal"
                                                                                data-checklist-id="{{ $documentlist->id }}"
                                                                                data-client-id="{{ $documentlist->client_id }}"
                                                                                data-document-check-list-master-id="{{ $documentlist->document_check_list_id }}">
                                                                                <i class="ki-filled ki-file-up"></i>
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="flex justify-end mt-4 space-x-2">
                                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow" name="save_client" type="submit">
                                                        Save changes
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                    <div class="border-t border-gray-100 pt-4">
                                            <div class="text-center py-4 text-gray-500">
                                                <i class="ki-filled ki-calendar text-2xl text-gray-300 mb-2"></i>

                                                <h3 class="text-primary"><strong>No Document Checklist Found</strong></h3>
                                                <p class="font-20 font-500 clr-gray-2 mt-2 ">
                                                    <strong>There is no checklist for this client profile. <br />
                                                        you can create new checklist for this client from below button.
                                                    </strong>
                                                </p>

                                                @haspermission('lead:edit')
                                                    <a href="{{ route('team.document.create',$client->id) }}" class="kt-btn kt-btn-sm kt-btn-primary mt-2">
                                                        <i class="ki-filled ki-plus"></i>
                                                            Create Document Checklist
                                                    </a>
                                                @endhaspermission
                                            </div>
                                        </div>
                                    @endif
                                </x-team.card>
                            @endhaspermission
                        </div>
                    </div>
            </div>
        </div>

        {{-- Upload Document Modal --}}
        <div class="kt-modal hidden" id="upload_document_modal">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                    <h3 class="kt-modal-title">Upload Document</h3>
                    <button type="button" class="kt-modal-close" aria-label="Close modal" id="close_upload_modal">
                        ✕
                    </button>
                </div>
                <div class="kt-modal-body">


                    <form id="uploadForm"
                        class="dropzone"
                        action="{{ route('team.document-uploaded.store', $client->id) }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="client_document_check_list_id" id="checklistId">
                        <input type="hidden" name="client_id" id="ClientId">
                        <input type="hidden" name="status" id="DocumentStatus">
                        <input type="hidden" name="master_check_list_id" id="MasterDocuemtnChecklist">

                        <div class="dz-message needsclick">
                            <i class="ki-filled ki-file-up text-3xl text-gray-400 mb-2"></i>
                            <h3 class="text-lg font-medium text-gray-700 mb-1">Drop files here or click to upload</h3>
                            <p class="text-sm text-gray-500">You can upload multiple files. Maximum file size: 10MB per file.</p>
                        </div>

                        <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                            <a href="#" class="kt-btn kt-btn-secondary" id="cancel_upload_modal">Cancel</a>
                            <button type="submit" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-check"></i> Save Change
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="kt-modal" data-kt-modal="true" id="viwe_document">
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

                <form action="#" method="POST" class="form" >
                    @csrf
                    <div id="viewDocumentModalContent" class="rounded-lg bg-muted w-full grow min-h-[22px] items-center justify-center">
                        Loading...
                    </div>

                    {{-- <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#add_visited_country">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Save Change
                        </button>
                    </div> --}}
                </form>
                </div>
            </div>
        </div>

    </x-slot>
</x-team.layout.app>

<script src="{{ asset('assets/js/team/vendors/jquery.repeater.min.js') }}"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
    $(document).ready(function () {
        const clientId = @json($client->id);
        // Open modal
        $(document).on('click', '.open-upload-modal', function () {
            let $row = $(this).closest('tr');
            let statusValue = $row.find('select[name^="studentDocument"]').val(); // get dropdown value

            $('#checklistId').val($(this).data('checklist-id'));
            $('#ClientId').val($(this).data('client-id'));
            $('#MasterDocuemtnChecklist').val($(this).data('document-check-list-master-id'));
            $('#DocumentStatus').val(statusValue); // store status in hidden field

            $('#upload_document_modal').removeClass('hidden').fadeIn(200);
        });

        // Close modal
        $(document).on('click', '#close_upload_modal, #cancel_upload_modal', function (e) {
            e.preventDefault();
            $('#upload_document_modal').fadeOut(200, function () {
                $(this).addClass('hidden');
            });
        });


        $('[data-kt-modal-toggle="#viwe_document"]').on('click', function () {
            $('#viewDocumentModalContent').html('Loading...');
            var client_check_list_id = $(this).data('client-document-check-lists');
            $.ajax({
                url: '{{ route('team.view-client-document') }}',
                type: 'GET',
                data: {
                    client_id: clientId,
                    client_check_list_id: client_check_list_id
                },
                success: function (response) {
                    $('#viewDocumentModalContent').html(response);
                },
                error: function () {
                    $('#viewDocumentModalContent').html('<div class="text-red-500">Failed to load content.</div>');
                }
            });
        });

    });
</script>



<script>

// Immediately after dropzone script: (or top of your JS file)
Dropzone.autoDiscover = false;

$(document).ready(function() {
    // keep reference globally if needed
    let uploadedFiles = [];
    const $form = $('#uploadForm');
    const dropzoneElement = document.getElementById('uploadForm');

    if (!dropzoneElement) return;

    // Prevent double initialization
    if (dropzoneElement.dropzone) {
        console.log('Dropzone already initialized - using existing instance');
        window.myDropzone = dropzoneElement.dropzone;
        return;
    }

    const myDropzone = new Dropzone(dropzoneElement, {
        url: $form.attr('action'),      // ensure URL is provided
        paramName: "file[]",
        autoProcessQueue: false,        // do not upload until submit
        uploadMultiple: true,           // send files together (and use sendingmultiple)
        parallelUploads: 10,
        maxFiles: 10,
        maxFilesize: 10,                // MB
        addRemoveLinks: true,
        timeout: 120000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dictRemoveFile: "Remove"
    });

    // Append your form fields once when sending the batch
    myDropzone.on("sendingmultiple", function(files, xhr, formData) {
        // Append all form fields into formData
        const data = $form.serializeArray();
        $.each(data, function(i, field) {
            formData.append(field.name, field.value);
        });
    });

    // On submit button (Save Change), process queue
    $form.on('submit', function(e) {
        e.preventDefault();

        // If there are files queued, upload them with form fields
        if (myDropzone.getQueuedFiles().length > 0) {
            myDropzone.processQueue();
        } else {
            // No files: submit the form normally (will hit controller)
            // use plain JS submit to avoid infinite loop
            this.submit();
        }
    });

    myDropzone.on('successmultiple', function(files, response) {
        // handle success (response from server)
        console.log('successmultiple', response);
        // close modal / reload UI as needed
        location.reload(); // or do a partial UI update
    });

    myDropzone.on('errormultiple', function(files, response) {
        console.error('errormultiple', response);
        // show validation errors to user
    });

    myDropzone.on('removedfile', function(file) {
        // do server cleanup if file was uploaded earlier (optional)
        console.log('file removed', file.name);
    });

    window.myDropzone = myDropzone;
});

</script>

