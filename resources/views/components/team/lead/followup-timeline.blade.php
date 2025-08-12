@props([
    'followUps' => [],
    'client' => null
])

@if(count($followUps) > 0)
    <div class="space-y-6">
        @foreach($followUps as $index => $followUp)
            <div class="kt-card">
                <div class="kt-card-body p-4">
                    {{-- Header --}}
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-1">
                                Follow-up #{{ count($followUps) - $index }}
                            </h4>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="ki-filled ki-calendar text-xs mr-1"></i>
                                {{ \Carbon\Carbon::parse($followUp->followup_date)->format('d M Y') }}
                            </div>
                        </div>
                        <div class="text-right">
                            @if($followUp->status == '0')
                                <span class="kt-badge kt-badge-warning kt-badge-sm">Pending</span>
                            @else
                                <span class="kt-badge kt-badge-success kt-badge-sm">Completed</span>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($followUp->created_at)->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="space-y-3">
                        {{-- Remarks --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 uppercase tracking-wide block mb-1">
                                Remarks
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-900">
                                {{ $followUp->remarks ?: 'No remarks provided' }}
                            </div>
                        </div>

                        {{-- Communication (only for completed) --}}
                        @if($followUp->status == '1' && $followUp->communication)
                            <div>
                                <label class="text-xs font-medium text-gray-700 uppercase tracking-wide block mb-1">
                                    Communication
                                </label>
                                <div class="bg-green-50 p-3 rounded-lg text-sm text-gray-900">
                                    {{ $followUp->communication }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-between items-center pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500">
                        <span>
                            <i class="ki-filled ki-user text-xs mr-1"></i>
                            {{ $followUp->created_by_name ?? 'Unknown' }}
                        </span>
                        @if($followUp->updated_by_name && $followUp->updated_at != $followUp->created_at)
                            <span>
                                Updated by {{ $followUp->updated_by_name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="kt-card">
        <div class="kt-card-body text-center py-8">
            <i class="ki-filled ki-information text-3xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">No follow-ups found for this lead.</p>
            <p class="text-gray-400 text-sm mt-1">Follow-ups will appear here once they are created.</p>
        </div>
    </div>
@endif
