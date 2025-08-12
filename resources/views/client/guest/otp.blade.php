@extends('client.layouts.guest')

@section('content')
<form action="{{ route('client.guest.otp.verify') }}" class="kt-card-content flex flex-col gap-5 p-10" method="POST">
    @csrf

    <div class="text-center mb-2">
        <h3 class="text-lg font-medium text-mono mb-5">
            Verify your phone
        </h3>
        <div class="flex flex-col">
            <span class="text-sm text-secondary-foreground mb-1.5">
                Enter the verification code we sent to
            </span>
            <a class="text-sm font-medium text-mono" href="#">
                {{ $mobile_no }}
            </a>
        </div>
    </div>

    <input type="hidden" name="mobile_no" value="{{ $mobile_no }}">
    <div class="flex flex-wrap justify-center gap-2">
        
        @for($i = 0; $i < 4; $i++)
            <input
                class="kt-input focus:border-primary/10 focus:ring-3 focus:ring-primary/10 size-10 shrink-0 px-0 text-center"
                maxlength="1"
                name="code_{{ $i }}"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                autocomplete="one-time-code"
                required
                value="{{ $otpRecord->otp[$i] }}"
            />
        @endfor
    </div>
    @if ($errors->has('otp'))
        <div class="text-destructive text-sm text-center">
            {{ $errors->first('otp') }}
        </div>
    @endif

    <div class="flex items-center justify-center mb-2">
        <span class="text-2sm text-secondary-foreground me-1.5">
            Didn’t receive a code? (37s)
        </span>
        <a class="text-2sm kt-link" href="#">
            Resend
        </a>
    </div>

    <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
        Continue
    </button>
</form>
@endsection
