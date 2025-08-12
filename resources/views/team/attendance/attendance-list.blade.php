

@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Attendance Detail', 'url' => route('team.coaching.pending')],
    ['title' => 'Attendance Detail']
];
@endphp
<x-team.layout.app title="Attendance Detail" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Attendance Detail
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Attendance Detail to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.attendance.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Attendance Information" headerClass="">
                @php
                    $attendances = $clientCoaching->getAttendances;

                    $totalRecords = $attendances->count();
                    $presentCount = $attendances->where('status', 'present')->count();
                    $absentCount = $attendances->where('status', 'absent')->count();
                    $nothingCount = $attendances->where('status', 'nothing')->count();
                @endphp

                @if ($totalRecords > 0)
                    <x-team.card>
                        <div class="overflow-x-auto">

                            <p class="mb-3 font-semibold text-gray-700">
                                Total Records: {{ $totalRecords }} |
                                Present: {{ $presentCount }} |
                                Absent: {{ $absentCount }} |
                                Nothing: {{ $nothingCount }}
                            </p>

                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Date</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($attendances as $index => $attendance)
                                        <tr>
                                            <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-gray-800">{{ $attendance->attendance_date ? date('d M Y', strtotime($attendance->attendance_date)) : '-' }}</td>
                                            <td class="px-4 py-2 text-gray-800">{{ ucfirst($attendance->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-team.card>
                @else
                    <p>No records found.</p>
                @endif


            </x-team.card>
        </div>

    </x-slot>
@push('scripts')
    @include('team.lead.lead-js')
@endpush

</x-team.layout.app>
