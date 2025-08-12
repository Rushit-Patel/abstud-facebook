{{-- Team Checkbox Component --}}
@props([
    'label' => false,
    'name' => '',
    'id' => '',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'required' => false,
    'size' => 'sm',
    'color' => 'primary',
    'style' => 'default', // default, inline, badge, stacked
    'help' => '',
    'class' => '',
    'labelClass' => '',
    'wrapperClass' => '',
    'attributes' => new \Illuminate\Support\HtmlString(''),
])

@php
    $inputId = $id ?: $name;
    $isChecked = old($name, $checked) ? true : false;

    // Checkbox size classes
    $sizeClasses = [
        'sm' => 'kt-checkbox-sm',
        'md' => 'kt-checkbox-md',
        'lg' => 'kt-checkbox-lg'
    ];

    // Checkbox color classes
    $colorClasses = [
        'primary' => 'kt-checkbox-primary',
        'success' => 'kt-checkbox-success',
        'warning' => 'kt-checkbox-warning',
        'danger' => 'kt-checkbox-danger'
    ];

    $checkboxClass = 'kt-checkbox ' . ($sizeClasses[$size] ?? 'kt-checkbox-sm') . ' ' . ($colorClasses[$color] ?? '') . ' ' . $class;

    // Style-based wrapper classes
    $styleClasses = [
        'default' => 'flex flex-col gap-1',
        'inline' => 'flex items-center gap-2',
        'badge' => 'flex items-center',
        'stacked' => 'space-y-2'
    ];

    $wrapperClasses = ($styleClasses[$style] ?? 'flex flex-col gap-1') . ' ' . $wrapperClass;
    $mainLabelClasses = 'kt-form-label text-mono ' . $labelClass;

    // Badge-specific classes
    $badgeClasses = 'kt-badge kt-badge-outline rounded-full';
@endphp

<div class="{{ $wrapperClasses }}">
    @if($label && $style !== 'inline' && $style !== 'badge')
        <label class="{{ $mainLabelClasses }}" for="{{ $inputId }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($style === 'badge')
        <label class="{{ $badgeClasses }}">
            @if ($label)
                {{ $label }}
            @endif
            @if($required)
                <span class="text-red-500">*</span>
            @endif
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ $value }}"
                @if($isChecked) checked @endif
                @if($disabled) disabled @endif
                @if($required) required @endif
                {{ $attributes }}
            />
        </label>
    @else
        <label class="kt-label {{ $style === 'inline' ? 'flex items-center gap-2' : '' }}">
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ $value }}"
                class="{{ $checkboxClass }}"
                @if($isChecked) checked @endif
                @if($disabled) disabled @endif
                @if($required) required @endif
                {{ $attributes }}
            />
            @if($label && $style === 'inline')
                <span class="{{ $mainLabelClasses }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </span>
            @endif
        </label>
    @endif

    @if($help)
        <p class="text-sm text-gray-500 mt-1">{{ $help }}</p>
    @endif

    @error($name)
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
