{{-- resources/views/admin/profiles/partials/input-password.blade.php --}}
@props([
    'name',
    'label',
    'required' => false,
    'hint' => null,
    'placeholder' => '', // Placeholder for the password field
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="input-group input-group-sm">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control form-control-sm @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder }}"
            autocomplete="new-password"
        >
        <button type="button" class="btn btn-outline-secondary password-toggle"
            data-target="{{ $name }}" aria-label="{{ __('Toggle password visibility') }}">
            <i class="bi bi-eye-fill"></i>
        </button>
        @error($name)
            <div class="invalid-feedback d-block w-100">
                {{ $message }}
            </div>
        @else
            @if (isset($hint) && !$errors->has($name))
                <div class="form-text w-100 mt-1 small">{{ $hint }}</div>
            @endif
        @enderror
    </div>
    {{-- The hint shows below the field if provided and no error --}}
</div>
