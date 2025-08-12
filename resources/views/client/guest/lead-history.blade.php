@extends('client.layouts.guest')

@section('card-width', 'max-w-[800px]')

@section('content')
    <x-team.card title="Dear {{ $clientCheck->first_name }} {{ $clientCheck->last_name }}," headerClass="">
        <div class="grid grid-cols-1 gap-5 text-center">
            @if($getClientLeads->count() > 0)
                <div class="kt-alert kt-alert-outline kt-alert-primary mb-6">
                    <div class="kt-alert-title text-lg">
                        Thank you for visiting our office. Based on our records, here are the services you've recently shown interest in. Please continue or choose another service as needed.
                    </div>
                </div>
                <div class="w-full ">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 flex flex-col items-center">
                        @foreach ($getClientLeads as $getClientLead)
                            <div>
                                {{-- <a href="{{ route('client.guest.thankyou', $getClientLead->id) }}"> --}}
                                <div class="select-box border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-200">
                                    <div class="p-4 flex flex-col items-center">
                                        <div class="select-box-icon mb-2">
                                            <img src="{{ asset($getClientLead->getPurpose->image) }}" alt="{{ $getClientLead->getPurpose->name }} icon" class="h-16 w-16 object-contain">
                                        </div>
                                        <div class="select-box-content text-center">
                                            <h4 class="text-yellow-600 text-lg font-semibold">{{ strtoupper($getClientLead->getPurpose->name) }}</h4>
                                        </div>
                                        @if(!empty($getClientLead->assignedOwner))
                                            <span class="text-sm text-gray-500 mt-2">
                                                {{ 'Last met with ' . $getClientLead->assignedOwner->name . ' on ' . \Carbon\Carbon::parse($getClientLead->updated_at)->format('d M Y') . '.' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-4 flex flex-col items-center">
                                        <form action="{{ route('client.guest.visit-history.store', base64_encode($getClientLead->id)) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="kt-btn kt-btn-sm">
                                                <span class="text-sm font-medium">Continue with {{ ucfirst($getClientLead->getPurpose->name) }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('client.guest.service', base64_encode($clientCheck->id))}}"
                        class="inline-block bg-primary hover:bg-primary-white text-white font-medium px-6 py-2 rounded-full">
                        Choose another Service
                    </a>
                </div>
            @else
                <div class="kt-alert kt-alert-outline kt-alert-destructive mt-4 mb-6">
                    <div class="kt-alert-title text-lg">
                        We couldn’t find any previous service history linked to your profile. To get started, please choose a service.
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('client.guest.service', base64_encode($clientCheck->id)) }}"
                        class="inline-block bg-primary hover:bg-primary-white text-white font-medium px-6 py-2 rounded-full">
                        Choose a Service
                    </a>
                </div>
            @endif
        </div>
    </x-team.card>
@endsection