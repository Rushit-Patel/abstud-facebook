@props([
    'label' => '',
    'name' => '',
    'id' => '',
    'value' => '',
    'placeholder' => 'Select time',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'class' => '',
    'minDate' => '',
    'maxDate' => '',
    'minTime' => '',
    'maxTime' => '',
    'dateFormat' => 'H:i',         // Only time format (24-hour)
    'enableTime' => true,          // Force time picker
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
            value="{{ old($name, $value ? Carbon\Carbon::parse($value)->format('H:i') : '') }}"

            placeholder="{{ $placeholder }}"
            class="timepickr {{ $classes }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            data-flatpickr
            data-enable-time="true"
            data-no-calendar="true"
            data-date-format="{{ $dateFormat }}"
            @if($time_24hr) data-time-24hr="true" @endif
            @if($minTime) data-min-time="{{ $minTime }}" @endif
            @if($maxTime) data-max-time="{{ $maxTime }}" @endif
            @if($defaultDate) data-default-date="{{ \Carbon\Carbon::parse($defaultDate)->format('H:i') }}" @endif
        />

        <!-- Clock Icon -->
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="ki-filled ki-time text-gray-400"></i>
        </div>

        <!-- Clear Button -->
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

@once
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                flatpickr(".timepickr", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                });

                // Show/Hide clear button
                document.querySelectorAll(".timepickr").forEach(function (input) {
                    input.addEventListener("input", function () {
                        const clearBtn = input.parentElement.querySelector(".clear-date-btn");
                        clearBtn.style.opacity = input.value ? "1" : "0";
                    });
                });
            });

            function clearDate(id) {
                const input = document.getElementById(id);
                input.value = '';
                const clearBtn = input.parentElement.querySelector(".clear-date-btn");
                clearBtn.style.opacity = "0";
            }
        </script>
    @endpush
@endonce
