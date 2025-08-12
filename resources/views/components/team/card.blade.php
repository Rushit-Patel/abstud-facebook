{{-- Team Card Component --}}
@props([
    'title' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'cardClass' => '',
    'titleClass' => null,
])

<div {{ $attributes->merge(['class' => "kt-card $cardClass"]) }}>
    @if($title || isset($header))
        <div class="kt-card-header  {{ $headerClass }}">
            @if($title)
                <h3 class="kt-card-title text-primary {{$titleClass}}">
                    {{ $title }}
                </h3>
            @endif
            @isset($header)
                {{ $header }}
            @endisset
        </div>
    @endif

    <div class="kt-card-content  {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
