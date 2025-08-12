@props([
    'label' => '',
    'name' => '',
    'id' => '',
    'value' => '',
    'placeholder' => 'Select date',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'class' => '',
    'minDate' => '',
    'maxDate' => '',
    'dateFormat' => 'Y-m-d',
    'enableTime' => false,
    'time_24hr' => true,
    'defaultDate' => '',
])

@php
    $classes = 'kt-input pl-10 pr-3 ' . $class;
    $inputId = $id ?: $name;
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="kt-form-label text-mono">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        <input 
            type="text" 
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class=" range-flatpickr {{ $classes }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            data-flatpickr
            data-date-format="{{ $dateFormat }}"
            @if($minDate) data-min-date="{{ $minDate }}" @endif
            @if($maxDate) data-max-date="{{ $maxDate }}" @endif
            @if($enableTime) data-enable-time="true" @endif
            @if($time_24hr) data-time-24hr="true" @endif
            @if($defaultDate) data-default-date="{{ $defaultDate }}" @endif
        />
        
        <!-- Calendar Icon -->
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="ki-filled ki-calendar text-gray-400"></i>
        </div>
        
        <!-- Clear Button (shows when date is selected) -->
        <button 
            type="button" 
            class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 transition-opacity duration-200 clear-date-btn"
            onclick="clearDate('{{ $inputId }}')"
        >
            <i class="ki-filled ki-cross text-gray-400 hover:text-gray-600 text-sm"></i>
        </button>
    </div>
    
    @error($name)
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

