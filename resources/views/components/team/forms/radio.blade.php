{{-- Team Radio Component --}}
@props([
    'label' => false,
    'name' => '',
    'options' => [],
    'value' => '',
    'disabled' => false,
    'required' => false,
    'size' => 'sm',
    'color' => 'primary',
    'style' => 'default', // default, inline, horizontal, vertical
    'help' => '',
    'class' => '',
    'labelClass' => '',
    'wrapperClass' => '',
    'orientation' => 'vertical', // vertical, horizontal
    'attributes' => new \Illuminate\Support\HtmlString(''),
])

@php
    $selectedValue = old($name, $value);

    // Radio size classes
    $sizeClasses = [
        'sm' => 'kt-radio-sm',
        'md' => 'kt-radio-md',
        'lg' => 'kt-radio-lg'
    ];

    // Radio color classes
    $colorClasses = [
        'primary' => 'kt-radio-primary',
        'success' => 'kt-radio-success',
        'warning' => 'kt-radio-warning',
        'danger' => 'kt-radio-danger'
    ];

    $radioClass = 'kt-radio ' . ($sizeClasses[$size] ?? 'kt-radio-sm') . ' ' . ($colorClasses[$color] ?? '') . ' ' . $class;

    // Style-based wrapper classes
    $styleClasses = [
        'default' => 'kt-form-item',
        'inline' => 'flex items-center gap-4',
        'horizontal' => 'kt-form-item',
        'vertical' => 'kt-form-item'
    ];

    $wrapperClasses = ($styleClasses[$style] ?? 'kt-form-item') . ' ' . $wrapperClass;
    $mainLabelClasses = 'kt-form-label text-mono ' . $labelClass;

    // Options container classes based on orientation
    $optionsContainerClass = $orientation === 'horizontal' ? 'flex items-center gap-4' : 'grid gap-2.5';
    
    // For inline style, override orientation
    if ($style === 'inline') {
        $optionsContainerClass = 'flex items-center gap-4';
    }
@endphp

<div class="{{ $wrapperClasses }}">
    @if($label && $style !== 'inline')
        <div class="kt-form-control mb-1">
            <label class="{{ $mainLabelClasses }}">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        </div>
    @endif

    <div class="kt-form-control mb-1">
        <div class="{{ $optionsContainerClass }}">
            @if($style === 'inline' && $label)
                <label class="{{ $mainLabelClasses }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif

            @if(is_array($options) && count($options) > 0)
                @foreach($options as $optionValue => $optionLabel)
                    @php
                        $radioId = $name . '_' . $loop->index;
                        $isSelected = $selectedValue == $optionValue;
                    @endphp
                    <div class="flex items-center gap-2.5">
                        <input
                            type="radio"
                            class="{{ $radioClass }}"
                            id="{{ $radioId }}"
                            name="{{ $name }}"
                            value="{{ $optionValue }}"
                            @if($isSelected) checked @endif
                            @if($disabled) disabled @endif
                            @if($required) required @endif
                            {{ $attributes }}
                        />
                        <label class="kt-label" for="{{ $radioId }}">
                            {{ $optionLabel }}
                        </label>
                    </div>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </div>
    </div>

    @if($help)
        <div class="kt-form-description">{{ $help }}</div>
    @endif

    @error($name)
        <div class="kt-form-message">{{ $message }}</div>
    @enderror
</div>
