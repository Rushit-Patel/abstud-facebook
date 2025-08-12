@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option...',
    'required' => false,
    'searchable' => false,
    'multiple' => false,
    'disabled' => false,
    'is_selected' => false,
    'id' => null,
    'class' => '',
    'attributes' => new \Illuminate\Support\HtmlString(''),

])

@php
    // Select first option if is_selected is true and selected is empty
    if ($is_selected && (is_null($selected) || $selected === '')) {
        if (is_array($options) && count($options)) {
            $firstOption = reset($options);
            $selected = is_object($firstOption)
                ? ($firstOption->id ?? $firstOption->value ?? $firstOption)
                : $firstOption;
        } elseif ($options instanceof \Illuminate\Support\Collection && $options->isNotEmpty()) {
            $firstOption = $options->first();
            $selected = is_object($firstOption)
                ? ($firstOption->id ?? $firstOption->value ?? $firstOption)
                : $firstOption;
        }
    }
@endphp

<div class="flex flex-col gap-1.5 select-container">
    {{-- Label --}}
    @if(isset($label))
        <label for="{{ $name }}" class="kt-form-label  {{ $required ? 'required' : '' }}">
            {{ $label }}
            @if($required)
                <span class="text-destructive">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $id }}"
        class="select2 kt-select {{ $class }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
        @if(is_array($options) || $options instanceof \Illuminate\Support\Collection)
            @foreach($options as $key => $option)
                @php
                    if (is_object($option)) {
                        $optionValue = $option->id ?? $option->value ?? $option;
                        $optionText = $option->name ?? $option->text ?? $option->branch_name ?? $option;
                    } else {
                        $optionValue = $key;
                        $optionText = $option;
                    }

                    $isOptionSelected = is_array($selected)
                        ? in_array($optionValue, $selected)
                        : $selected == $optionValue;
                @endphp

                <option value="{{ $optionValue }}" {{ $isOptionSelected ? 'selected' : '' }}>
                    {{ $optionText }}
                </option>
            @endforeach
        @endif
    </select>

    @error($name)
        <div class="kt-form-error">{{ $message }}</div>
    @enderror
</div>
