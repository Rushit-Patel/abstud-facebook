@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Exam Date Booking', 'url' => route('team.exam-booking.index')],
    ['title' => 'Edit Exam Date Booking']
];
@endphp
<x-team.layout.app title="Edit Exam Date Booking" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Exam Date Booking
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Edit Exam Date Booking to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.exam-booking.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Exam Date Booking Information" headerClass="">
                <form action="{{ route('team.exam-booking.Update',$BookingData->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="lg:col-span-1" >
                            <x-team.lead.exam-booking-details
                            :englishProficiencyTest="$englishProficiencyTest"
                            :bookingData="$BookingData"
                            :examCenter="$examCenter"
                            :examWay="$examWay"
                            />
                        </div>
                    </div>


                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.exam-booking.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Exam Date Booking
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

@push('scripts')
    @include('team.lead.lead-js')
@endpush
</x-team.layout.app>
