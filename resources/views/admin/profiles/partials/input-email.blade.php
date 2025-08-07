{{-- resources/views/admin/profiles/partials/input-email.blade.php --}}
@props([
    'name',
    'label',
    'required' => false,
    'value' => '',
    'placeholder' => '', // Placeholder text for the input
    'readonly' => false, // If true, input is not editable
    'disabled' => false, // If true, input is disabled
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input
        type="email"
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control form-control-sm @error($name) is-invalid @enderror"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder ?: $label }}"
        {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    >
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
