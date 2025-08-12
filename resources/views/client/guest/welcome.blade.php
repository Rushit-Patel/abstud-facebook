@extends('client.layouts.guest')

@section('content')
    <form action="{{ route('client.guest.welcome.post') }}" class="kt-card-content flex flex-col gap-5 p-5" id="sign_up_form" method="post">
        @csrf
        @method('POST')
        <div class="flex items-center">
            <h4 class="text-1xl font-bold ">
                Welcome to <span class="">{{ session('branch')->branch_name ?? 'Our Branch' }}</span>
            </h4>
        </div>
        <div class="flex items-center gap-2">
            <span class="border-t border-border w-full">
            </span>
        </div>
        <div class="flex flex-col gap-1">
            <x-team.forms.mobile-input
                name="mobile_no"
                label="Mobile Number"
                placeholder="Enter your mobile number"
                class="required mobile"
                required
            />
        </div>
        <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
            Submit
        </button>
    </form>
@endsection