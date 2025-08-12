{{-- Team Input Field Component --}}
@props([
    'label',
    'name',
    'id' => null,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'icon' => null,
    'readonly' => false,
    'rows' => 2,
])
<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="kt-form-label text-mono">
        {{ $label }}
        @if($required)
            <span class="text-destructive">*</span>
        @endif
    </label>
    @php
    if($id === null) {
        $id = $name;
    }
    @endphp
    <textarea class="kt-textarea" name="{{ $name }}" id="{{ $id }}" placeholder="{{ $placeholder }}" rows="{{ $rows }}" @if($readonly) readonly @endif @if($required) required @endif>{{ old($name, $value) }}</textarea>
    @error($name)
    <span class="text-destructive text-sm mt-1">
        {{ $message = $errors->first($name) }}
    </span>
    @enderror
</div>

