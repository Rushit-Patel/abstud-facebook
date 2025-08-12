@props([
    'label' => '',
    'name' => '',
    'id' => '',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'required' => false,
    'icon' => '',
    'size' => 'sm', 
    'color' => 'primary', // primary, success, warning, danger
    'style' => 'menu', // menu, inline, block
    'class' => '',
    'labelClass' => '',
    'wrapperClass' => '',
])

@php
    $inputId = $id ?: $name;
    $isChecked = old($name, $checked) ? true : false;
    
    // Switch size classes
    $sizeClasses = [
        'sm' => 'kt-switch-sm',
        'md' => 'kt-switch-md', 
        'lg' => 'kt-switch-lg'
    ];
    
    // Switch color classes
    $colorClasses = [
        'primary' => 'kt-switch-primary',
        'success' => 'kt-switch-success',
        'warning' => 'kt-switch-warning',
        'danger' => 'kt-switch-danger'
    ];
    
    $switchClass = 'kt-switch ' . ($sizeClasses[$size] ?? 'kt-switch-sm') . ' ' . ($colorClasses[$color] ?? 'kt-switch-primary') . ' ' . $class;
    
    // Style-based wrapper classes
    $styleClasses = [
        'menu' => 'kt-dropdown-menu-link',
        'inline' => 'flex items-center gap-2.5',
        'block' => 'flex flex-col gap-2'
    ];
    
    $wrapperClasses = ($styleClasses[$style] ?? 'flex items-center gap-2.5') . ' ' . $wrapperClass;
    $labelClasses = 'flex items-center gap-2 ' . $labelClass;
@endphp

<div class="{{ $wrapperClasses }}">
    @if($style === 'menu')
        {{-- Menu style: icon + label on left, switch on right --}}
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
        <input 
            type="checkbox" 
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            class="ms-auto {{ $switchClass }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes }}
        />
    @elseif($style === 'block')
        {{-- Block style: label on top, switch below --}}
        @if($label)
            <label for="{{ $inputId }}" class="{{ $labelClasses }}">
                @if($icon)
                    <i class="{{ $icon }}"></i>
                @endif
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input 
            type="checkbox" 
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            class="{{ $switchClass }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes }}
        />
    @else
        {{-- Inline style: label and switch side by side --}}
        @if($label)
            <label for="{{ $inputId }}" class="{{ $labelClasses }}">
                @if($icon)
                    <i class="{{ $icon }}"></i>
                @endif
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input 
            type="checkbox" 
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            class="{{ $switchClass }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes }}
        />
    @endif
    
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
