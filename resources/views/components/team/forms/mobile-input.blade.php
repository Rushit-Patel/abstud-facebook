{{-- Team International Mobile Input Field Component --}}
@props([
    'label',
    'name',
    'type' => 'tel',
    'required' => false,
    'readonly' => false,
    'placeholder' => '',
    'value' => '',
    'countryCodeName' => null,
    'countryCodeValue' => '',
    'icon' => null
])

@once
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
@endonce

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="kt-form-label text-mono">
        {{ $label }}
        @if($required)
            <span class="text-destructive">*</span>
        @endif
    </label>

    <div class="relative">
        <input class="kt-input" id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
        />

        {{-- Hidden field for country code if specified --}}
        @if($countryCodeName)
            <input type="hidden"
                   id="{{ $countryCodeName }}"
                   name="{{ $countryCodeName }}"
                   value="{{ old($countryCodeName, $countryCodeValue) }}">
        @endif
    </div>

    @error($name)
    <span class="text-destructive text-sm mt-1">
        {{ $message = $errors->first($name) }}
    </span>
    @enderror

    @if($countryCodeName)
        @error($countryCodeName)
        <span class="text-destructive text-sm mt-1">
            {{ $message = $errors->first($countryCodeName) }}
        </span>
        @enderror
    @endif
</div>

@once
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {
        // Initialize all mobile input fields
        const mobileInputs = document.querySelectorAll('input[type="tel"]');

        mobileInputs.forEach(function (input) {
            const iti = window.intlTelInput(input, {
                loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
                separateDialCode: true,
                formatOnDisplay: false,
                initialCountry: "auto",
                geoIpLookup: function (success, failure) {
                    fetch("https://ipapi.co/json")
                        .then(function (res) { return res.json(); })
                        .then(function (data) { success(data.country_code); })
                        .catch(function () { success("in"); }); // Default to India
                },
            });

            // Get the field name like "id_mobile" -> "id_country_code"
            const hiddenInputId = input.id+'_country_code';

            // Create hidden input if not exists
            let hiddenInput = document.getElementById(hiddenInputId);
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = hiddenInputId;
                hiddenInput.name = hiddenInputId;
                input.parentNode.appendChild(hiddenInput);
            }

            // Set hidden input value to dialCode
            const setCountryCode = () => {
                const dialCode = iti.getSelectedCountryData().dialCode;
                hiddenInput.value = dialCode;
            };

            input.addEventListener("countrychange", setCountryCode);

            input.addEventListener('blur', function () {
                if (input.value.trim() && !iti.isValidNumber()) {
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            // Set initial country code
            setTimeout(setCountryCode, 500); // wait for auto country detection
        });
    });

    </script>
    @endpush
@endonce
