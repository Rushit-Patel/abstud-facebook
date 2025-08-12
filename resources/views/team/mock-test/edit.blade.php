@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Mock Test', 'url' => route('team.mock-test.index')],
    ['title' => 'Edit Mock Test']
];
@endphp
<x-team.layout.app title="Edit Mock Test" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Mock Test
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Edit Mock Test to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.mock-test.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Mock Test Information" headerClass="">
                <form action="{{ route('team.mock-test.update', $mockTest) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-team.lead.mock-test-details
                        :coaching="$coaching"
                        :branch="$branch"
                        :mockTest="$mockTest"
                    />


                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.mock-test.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Mock Test
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

@push('scripts')
    @include('team.lead.lead-js')
    <script>
        flatpickr("#time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false
        });
    </script>
@endpush

</x-team.layout.app>
