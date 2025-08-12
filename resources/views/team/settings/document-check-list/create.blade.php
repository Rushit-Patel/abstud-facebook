@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Document Check List', 'url' => route('team.settings.document-check-list.index')],
    ['title' => 'Create Document Check List']
];
@endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<x-team.layout.app title="Create Document Check List" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Document Check List
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new document-check-list to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.document-check-list.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Document Check List Information" headerClass="">
                <form action="{{ route('team.settings.document-check-list.store') }}" method="POST" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <!-- Create a 3-column grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <!-- Document Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Document Name"
                                        type="text"
                                        placeholder="Enter document name"
                                        :value="old('name')"
                                        required />

                                    <!-- Category -->
                                    <x-team.forms.select
                                        name="category_id"
                                        label="Category"
                                        :options="$documentCategory"
                                        :selected="old('category_id', [])"
                                        placeholder="Select Category"
                                        searchable="true"
                                        required="true"
                                        id="category_id"
                                    />

                                    <!-- Applicable For -->
                                    <x-team.forms.select
                                        name="applicable_for[]"
                                        label="Applicable For"
                                        :options="$purpose"
                                        :selected="old('applicable_for', [])"
                                        placeholder="Select applicable for"
                                        searchable="true"
                                        required="true"
                                        multiple="true"
                                        id="applicable_for"
                                    />

                                    <x-team.forms.select
                                        name="country[]"
                                        label="Country"
                                        :options="$country"
                                        {{-- :options="['all' => 'All'] + $country" --}}
                                        :selected="old('country', [])"
                                        placeholder="Select country"
                                        searchable="true"
                                        multiple="true"
                                        id="country"
                                    />

                                    <x-team.forms.select
                                        name="coaching[]"
                                        label="Coaching"
                                        :options="$coaching"
                                        {{-- :options="['all' => 'All'] + $coaching" --}}
                                        :selected="old('coaching', [])"
                                        placeholder="Select coaching"
                                        searchable="true"
                                        multiple="true"
                                        id="coaching"
                                    />

                                    <!-- Type -->
                                    @php
                                        $type = [
                                            'Required' => 'Required',
                                            'Recommanded' => 'Recommanded',
                                            'Optional' => 'Optional',
                                        ];
                                    @endphp
                                    <x-team.forms.select
                                        name="type"
                                        label="Type"
                                        :options="$type"
                                        :selected="old('type', [])"
                                        placeholder="Select type"
                                        searchable="true"
                                        required="true"
                                        id="type"
                                    />

                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-1 mt-3 gap-5">
                                    <!-- Document Name -->
                                    <x-team.forms.input
                                        name="tags"
                                        label="Tag"
                                        id="tags" {{-- important: for JS targeting --}}
                                        :value="old('tags')"
                                        placeholder="Add tags"
                                        searchable="true"
                                        required="true"
                                    />

                                </div>

                            </x-team.card>
                        </div>

                        <!-- Status -->
                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="status"
                                                type="checkbox"
                                                value="1"
                                                {{ old('status', true) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this document-check-list inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.document-check-list.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Document Check List
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" />
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var input = document.querySelector('#tags');
                if (input) {
                    new Tagify(input);
                }
            });
        </script>

        <script>
            // Form validation and enhancement
            $(document).ready(function() {
                // Add any additional form enhancements here

                // Focus on name field
                $('#name').focus();

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>

       <script>
        $(document).ready(function () {
            function handleSelectAll(selectId) {
                $('#' + selectId).on('change', function () {
                    var selectedValues = $(this).val();

                    if (selectedValues && selectedValues.includes('all')) {
                        // Sabhi options select karo except "all"
                        $(this).find('option').prop('selected', true);
                        $(this).find('option[value="all"]').prop('selected', false);

                        // UI refresh (agar select2/choices use ho raha hai)
                        $(this).trigger('change');
                    }
                });
            }

            handleSelectAll('country');
            handleSelectAll('coaching');
        });
        </script>



    @endpush
</x-team.layout.app>
