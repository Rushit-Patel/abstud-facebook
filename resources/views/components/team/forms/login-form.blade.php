{{-- Team Login Form Component --}}
@props([
    'action',
    'method' => 'POST'
])
<div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1">
    <div class="kt-card max-w-[370px] w-full">
        <div class="kt-card-content flex flex-col gap-5 p-10">
            <div class="flex justify-center mb-3 ">
                <div class="kt-btn kt-btn-outline p-5 ">
                    <x-team.auth.logo companyLogo="{{ $appData['companyLogo'] }}" companyName="{{ $appData['companyName'] }}" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="border-t border-border w-full"></span>
                <span class="text-xs text-muted-foreground font-medium uppercase">
                    
                </span>
                <span class="border-t border-border w-full"></span>
            </div>
            <form class=" flex flex-col gap-5" id="sign_in_form" action="{{ $action }}" method="{{ $method }}">
                @csrf
                {{ $slot }}
            </form>
        </div>
    </div>
</div>