@extends('client.layouts.guest')

@section('card-width', 'max-w-[800px]')

@section('content')
    <x-team.card title="Service Information" headerClass="">
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($purposes as $purpose)
                <div class="col-span-1">
                    <a class="cursor-pointer transition-all duration-200 hover:shadow" href="{{ route('client.guest.academic-info',[ $client_id, $purpose->id]) }}">
                        <div class="kt-card hover:bg-accent/60 transition-all duration-200">
                            <div class="kt-card-content p-2  flex items-center flex-wrap w-full">
                                <div class="flex md:items-center gap-4">
                                    <div class="kt-card bg-accent/50 shadow-none relative">
                                        <img alt="img" class="w-full h-auto transition-all duration-200 hover:opacity-80" src="{{ asset($purpose->image) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </x-team.card>
@endsection