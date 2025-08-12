{{-- Team Login Button Component --}}
@props([
    'type' => 'submit',
    'loading' => false,
    'loadingText' => 'Signing in...'
])
<button type="{{ $type }}" class="kt-btn kt-btn-primary flex justify-center grow" @if($loading) disabled @endif>
     @if($loading)
        {{ $loadingText }}
    @else
        {{ $slot }}
    @endif
</button>