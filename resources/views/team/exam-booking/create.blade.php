@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Exam Date Booking', 'url' => route('team.exam-booking.index')],
        ['title' => 'Create Exam Date Booking']
    ];
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<x-team.layout.app title="Create Exam Date Booking" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Exam Date Booking
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new exam book to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.exam-booking.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
                <form action="{{ route('team.exam-booking.update',$clientCoaching->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" >
                            <x-team.lead.exam-booking-details
                            :englishProficiencyTest="$englishProficiencyTest"
                            :examCenter="$examCenter"
                            :examWay="$examWay"
                            />
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.exam-booking.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Exam Date Booking
                        </button>
                    </div>
                </form>
            {{-- </x-team.card> --}}
        </div>
    </x-slot>
    @push('scripts')
        @include('team.lead.lead-js')
    @endpush
</x-team.layout.app>
