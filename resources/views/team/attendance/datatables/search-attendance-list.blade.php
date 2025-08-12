
@if ($records->count() > 0)
<x-team.card>
    <div class="overflow-x-auto">

        <!-- Apply All Box -->
        <div class="mb-3 flex items-center gap-4">
            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="apply_all" value="present"
                    class="accent-green-500 w-5 h-5 apply-all">
                <span class="text-green-600"><b>Present</b></span>
            </label>

            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="apply_all" value="absent"
                    class="accent-red-500 w-5 h-5 apply-all">
                <span class="text-red-600"><b>Absent</b></span>
            </label>

            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="apply_all" value="nothing"
                    class="accent-gray-500 w-5 h-5 apply-all">
                <span class="text-gray-600"><b>Nothing</b></span>
            </label>
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Client Details</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Joining Date</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($records as $index => $coaching)
                    <tr>


                        <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}
                        <input
                            type="hidden"
                            name="client_coaching_id[]"
                            value="{{ $coaching->id }}"
                            data-name="{{ $coaching->clientLeadDetails->first_name }} {{ $coaching->clientLeadDetails->last_name }}"
                        >

                        </td>
                        <td class="px-4 py-2 text-gray-800">
                            <div>
                                {{ $coaching->clientLeadDetails->first_name }} {{ $coaching->clientLeadDetails->last_name }}
                                <span class="bg-gray-600 text-white px-1 rounded">
                                    <b>{{ $coaching->clientLeadDetails->client_code }}</b>
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $coaching->getCoaching->name }}</div>
                            <div class="text-xs text-gray-500">{{ $coaching->getBatch->name }}({{ $coaching->getBatch->time }})</div>
                        </td>
                        <td class="px-4 py-2 text-gray-800">
                            {{ $coaching->joining_date ? date('d M Y', strtotime($coaching->joining_date)) : '-' }}
                        </td>
                        <td class="px-4 py-2 text-gray-800">
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="attendance_{{ $index }}" value="present"
                                        class="accent-green-500 w-5 h-5 attendance-radio">
                                    <span class="text-green-600"><b>Present</b></span>
                                </label>

                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="attendance_{{ $index }}" value="absent"
                                        class="accent-red-500 w-5 h-5 attendance-radio">
                                    <span class="text-red-600"><b>Absent</b></span>
                                </label>

                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="attendance_{{ $index }}" value="nothing"
                                        class="accent-gray-500 w-5 h-5 attendance-radio">
                                    <span class="text-gray-600"><b>Nothing</b></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-team.card>

<div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">

    <a href="{{ route('team.coaching.pending') }}" class="kt-btn kt-btn-secondary">
        Cancel
    </a>
    <button class="kt-btn kt-btn-primary" id="submitBtnmodel" data-kt-modal-toggle="#attendance_data"disabled>
        <i class="ki-filled ki-plus"></i>
            Submit
    </button>
</div>

@else
    <p>No records found.</p>
@endif
