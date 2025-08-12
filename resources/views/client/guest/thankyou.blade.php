@extends('client.layouts.guest')

@section('content')
	<div class="kt-card-content flex flex-col gap-5 p-5">
		<p>
			<strong>Thank You for Visiting Us!</strong>
		</p>
		<p>
			We sincerely appreciate your visit to our {{ session('branch')->branch_name ?? 'Our Branch' }} and your interest for {{ $clientLead?->getPurpose?->name }}.

            @if (isset($clientLead?->getCoaching->name))
				{{-- {{ $clientLead?->getCoaching?->name }} --}}
			@else
				{{-- {{ $clientLead?->getForeignCountry->name }} --}}
			@endif
			.
		</p>
		<span class="border-t border-border w-full pt-3">
			<p>
				We’re happy to inform you that we’ve successfully received your inquiry.One of our experienced executives will be in touch with you shortly to assist you further.
			</p>
			<p class="mt-5">
				Your journey begins here, and we’re excited to be a part of it.
			</p>
		</span>
		<a href="{{ $appData['companySetting']->website_url }}" class="kt-btn kt-btn-sm kt-btn-primary">Visit Website</a>
    </div>
@endsection
