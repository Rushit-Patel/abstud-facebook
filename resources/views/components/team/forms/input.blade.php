{{-- Team Input Field Component --}}
@props([
    'label',
    'name',
    'type' => 'text',
    'required' => false,
    'readonly' => false,
    'placeholder' => '',
    'value' => '',
    'icon' => null,
    'max' => null,
    'min' => null,
    'class' => '',
    'attributes' => new \Illuminate\Support\HtmlString(''),
])
<div class="flex flex-col gap-1.5">
    @if(isset($label))
    <label for="{{ $name }}" class="kt-form-label text-mono">
        {{ $label }}
        @if($required)
            <span class="text-destructive">*</span>
        @endif
    </label>
    @endif
    <input class="kt-input {{ $class }}" id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($readonly) readonly @endif
        {!! $attributes !!}
    />
    @error($name)
    <span class="text-destructive text-sm mt-1">
        {{ $message = $errors->first($name) }}
    </span>
    @enderror
</div>
