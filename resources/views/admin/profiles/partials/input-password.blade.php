{{-- resources/views/admin/profiles/partials/input-password.blade.php --}}
@props([
    'name',
    'label',
    'required' => false,
    'hint' => null,
    'placeholder' => '', // Added placeholder prop
])

<div class="mb-3">
    {{-- Bootstrap margin bottom --}}
    <label for="{{ $name }}" class="form-label">{{ $label }}@if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="input-group input-group-sm"> {{-- Bootstrap input group for the toggle button --}}
        <input type="password" name="{{ $name }}" id="{{ $name }}"
            class="form-control form-control-sm @error($name) is-invalid @enderror" {{-- Bootstrap classes --}}
            {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}" autocomplete="new-password">
        <button type="button" class="btn btn-outline-secondary password-toggle" {{-- Bootstrap button classes --}}
            data-target="{{ $name }}" aria-label="{{ __('Toggle password visibility') }}">
            <i class="bi bi-eye-fill"></i> {{-- Eye icon (Bootstrap Icons) --}}
        </button>
        @error($name)
            {{-- Ensure error message is correctly placed for input-groups --}}
            <div class="invalid-feedback d-block w-100"> {{-- w-100 for full width below input group --}}
                {{ $message }}
            </div>
        @else
            @if (isset($hint) && !$errors->has($name))
                {{-- Show hint only if there's no error for this field to avoid clutter --}}
                <div class="form-text w-100 mt-1 small">{{ $hint }}</div> {{-- Bootstrap form text for hint, w-100 for full width --}}
            @endif
        @enderror
    </div>
    {{-- If error exists, hint might be redundant or placed differently. Current logic shows hint if no error. --}}
    {{-- Or, always show hint if provided, regardless of error: --}}
    {{-- @if (isset($hint))
        <div class="form-text w-100 mt-1 small">{{ $hint }}</div>
    @endif --}}
</div>
