{{-- resources/views/admin/profiles/partials/input-text.blade.php --}}
@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'value' => '',
    'placeholder' => '', // Added placeholder prop
    'readonly' => false, // Added readonly prop
    'disabled' => false, // Added disabled prop
])

<div class="mb-3">
    {{-- Bootstrap margin bottom --}}
    <label for="{{ $name }}" class="form-label">{{ $label }}@if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $name }}"
        class="form-control form-control-sm @error($name) is-invalid @enderror" {{-- Bootstrap classes --}}
        value="{{ old($name, $value) }}" placeholder="{{ $placeholder ?: $label }}" {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }} {{ $disabled ? 'disabled' : '' }}>
    @error($name)
        <div class="invalid-feedback d-block"> {{-- Bootstrap error message --}}
            {{ $message }}
        </div>
    @enderror
</div>
