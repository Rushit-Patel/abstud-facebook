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
            <x-team.profile.profile-header :client="$client" />

            <div class="tab-content mt-6">
                <div class="grid grid-cols-1">
                    <div class="lg:col-span-4 space-y-6">
                        <x-team.card>
                            <x-slot name="header">
                                <h3 class="text-base font-semibold flex items-center gap-2">
                                    <i class="ki-filled ki-chart-line-up text-purple-600"></i>
                                    Documents Select Details
                                </h3>
                            </x-slot>

                            <form action="{{ route('team.document-checklist.store', $client->id)}}" method="POST" id="documentList">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="client_lead_id" value="{{ $client->leadLastest->id }}">

                                <div class="overflow-x-auto">
                                    <table class="min-w-full border border-gray-200 text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">
                                                    <input type="checkbox" id="checkAllDocument" class="form-checkbox">
                                                </th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">Document Title</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">Tags</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">Document Category</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">Requirement</th>
                                                <th class="px-4 py-2 border-b border-gray-200 text-left">Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documentlist as $index => $checklist)
                                            @php
                                                $existing = $existingDocs->get($checklist->id);
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    <input type="checkbox" class="form-checkbox document-checkbox"
                                                        name="studentDocument[{{ $index }}][document_check_list_id]"
                                                        value="{{ $checklist->id }}"
                                                        {{ $existing ? 'checked' : '' }}>
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    <span class="font-medium">{{ $checklist->name }}</span>
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    @php
                                                        $tagsArray = json_decode($checklist->tags, true);
                                                    @endphp
                                                    @if (is_array($tagsArray) && count($tagsArray) > 0)
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach ($tagsArray as $tag)
                                                                <span class="px-2 py-0.5 text-xs rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                                                    {{ $tag['value'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    {{ optional($checklist->documentCategory)->name }}
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    @php
                                                        $type = [
                                                            'Required' => 'Required',
                                                            'Recommanded' => 'Recommanded',
                                                            'Optional' => 'Optional',
                                                        ];
                                                    @endphp
                                                    <x-team.forms.select
                                                        name="studentDocument[{{ $index }}][document_type]"
                                                        label=""
                                                        :options="$type"
                                                        :selected="$existing ? $existing->document_type : old('type')"
                                                        placeholder="Select type"
                                                        searchable="true"
                                                        id="document_type"
                                                    />
                                                </td>
                                                <td class="px-4 py-2 border-b border-gray-200">
                                                    <x-team.forms.input
                                                        name="studentDocument[{{ $index }}][notes]"
                                                        label=""
                                                        type="text"
                                                        placeholder="Enter Notes"
                                                        :value="$existing ? $existing->notes : old('studentDocument['.$index.'][notes]')"
                                                    />
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>


                                <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                                    {{-- <a href="{{ route('team.lead.index') }}" class="kt-btn kt-btn-secondary">
                                        Cancel
                                    </a> --}}
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </x-team.card>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>

<script>
$('#checkAllDocument').on('change', function () {
        $('.document-checkbox').prop('checked', $(this).prop('checked'));
    });
$(document).ready(function () {
    $('#documentList').on('submit', function () {
        $('tbody tr').each(function () {
            var checkbox = $(this).find('.document-checkbox');
            if (!checkbox.is(':checked')) {
                // is row ke saare select/text inputs disable kar do
                $(this).find('select, input[type="text"], input[type="hidden"]').prop('disabled', true);
            }
        });
    });
});
</script>

