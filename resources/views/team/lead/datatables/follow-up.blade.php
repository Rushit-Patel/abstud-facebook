@php
    $lastPendingFollowUp = $follow_up->where('status', '0')->last();
    $completedCount = $follow_up->where('status', '1')->count();
@endphp

<div class="min-w-[120px]">
    @if($lastPendingFollowUp)
        {{-- Pending Follow-up --}}
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium text-orange-600">Pending</span>
            <div class="flex gap-1">
                <button
                    class="open-followup-modal p-1 hover:bg-blue-50 rounded transition-colors"
                    data-url="{{ route('team.lead-follow-up.edit', $lastPendingFollowUp->id) }}"
                    data-action="{{ route('team.lead-follow-up.update', $lastPendingFollowUp->id) }}"
                    title="Complete">
                    <i class="ki-filled ki-check text-blue-600 text-xs"></i>
                </button>
                <button
                    class="p-1 hover:bg-gray-50 rounded transition-colors"
                    onclick="viewFollowUpDetails({{ $lastPendingFollowUp->client_lead_id }})"
                    title="View Details">
                    <i class="ki-filled ki-eye text-gray-500 text-xs"></i>
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-600 truncate mb-2" title="{{ $lastPendingFollowUp->remarks }}">
            {{ Str::limit($lastPendingFollowUp->remarks, 30) }}
        </p>
    @else
        {{-- No Pending Follow-ups --}}
        @if($completedCount > 0)
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-green-600">{{ $completedCount }} Completed</span>
                <button
                    class="p-1 hover:bg-gray-50 rounded transition-colors"
                    onclick="viewAllFollowUps({{ $lead_data->id }})"
                    title="View All">
                    <i class="ki-filled ki-eye text-gray-500 text-xs"></i>
                </button>
            </div>
        @endif
        <button type="button"
            data-kt-modal-toggle="#add-followUp"
            data-form_action="{{ route('team.lead-follow-up.store',['client_lead_id' => $lead_data->id]) }}"
            class="kt-btn kt-btn-sm kt-btn-light-primary w-full open-followup-modal">
            <i class="ki-filled ki-plus text-xs mr-1"></i>Follow-up
        </button>
    @endif

    <div class="tag-wrapper" data-lead-id="{{ $lead_data->id }}">
        <span class="font-medium">
            <x-team.forms.input
                name="tags_{{ $lead_data->id }}"
                label=""
                id="tags_{{ $lead_data->id }}"
                :value="old('tags', $lead_data?->tag)"
                placeholder="Add tags"
            />
        </span>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<script>
    $(document).ready(function () {
        @isset($tags)
            var tagsArray = @json($tags);
        @else
            var tagsArray = [];
        @endisset

        // Loop through each tag input
        $('.tag-wrapper').each(function () {
            let wrapper = $(this);
            let leadId = wrapper.data('lead-id');
            let input = wrapper.find('input[id^="tags_"]')[0];

            if (input) {
                var tagify = new Tagify(input, {
                whitelist: tagsArray,
                enforceWhitelist: true,
                dropdown: {
                    enabled: 0,
                    showOnFocus: true
                }
            });

                // Pre-fill existing tags
                let oldValue = input.value;
                if (oldValue) {
                    tagify.addTags(oldValue.split(','));
                }

                // On tag change
                tagify.on('change', function () {
                    let updatedTags = tagify.value.map(tag => tag.value);

                    $.ajax({
                        url: "{{ route('team.lead.ajax.update.tag') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            lead_id: leadId,
                            tags: updatedTags.join(',')
                        },
                        success: function (response) {
                            console.log("Tags updated for lead_id " + leadId);
                        },
                        error: function (xhr) {
                            console.error("Failed to update tags for lead_id " + leadId, xhr.responseText);
                        }
                    });
                });
            }
        });
    });
</script>
