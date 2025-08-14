{{--
    resources/views/components/button.blade.php
    MYDS-compliant Primary Button Component
    - Uses MYDS color tokens, radius, shadow, typography, and accessibility.
    - Follows MYDS Design System and 18 MyGOVEA Principles (minimal, citizen-centric, accessible, clear feedback, consistent).
    - Focus ring, ARIA, keyboard accessible, spacing and motion tokens.
    - Default: type="submit", variant="primary", uppercase text.
    - Supports: leading icon, counter, full-width, disabled state.
    Usage:
      <x-button>{{ __('Save') }}</x-button>
      <x-button type="button" icon="bi-save" size="large">{{ __('Simpan') }}</x-button>
      <x-button icon="bi-check" counter="3">Submit</x-button>
--}}

@props([
    'type' => 'submit',
    'icon' => null, // Bootstrap icon class, e.g. 'bi-save'
    'counter' => null, // Numeric indicator, e.g. 3 for notifications
    'size' => 'medium', // 'small', 'medium', 'large'
    'variant' => 'primary', // 'primary', 'secondary', 'danger'
    'fullWidth' => false,
    'disabled' => false,
])

@php
    // Map MYDS button sizes to classes and spacing
    $sizeClassMap = [
        'small' => 'py-1 px-3 text-sm rounded-m',
        'medium' => 'py-2 px-4 text-base rounded-m',
        'large' => 'py-3 px-5 text-lg rounded-l',
    ];
    $sizeClass = $sizeClassMap[$size] ?? $sizeClassMap['medium'];

    // MYDS variant class mapping
    $variantClassMap = [
        'primary' => 'bg-myds-primary-600 hover:bg-myds-primary-800 text-white shadow-button focus:ring-myds-primary-400',
        'secondary' => 'bg-white hover:bg-myds-primary-50 text-myds-primary-700 border border-myds-primary-200 shadow-button focus:ring-myds-primary-200',
        'danger' => 'bg-myds-danger-600 hover:bg-myds-danger-700 text-white shadow-button focus:ring-myds-danger-400',
    ];
    $variantClass = $variantClassMap[$variant] ?? $variantClassMap['primary'];

    // Disabled state
    $disabledClass = $disabled
        ? 'opacity-50 cursor-not-allowed'
        : 'cursor-pointer';

    // Full width
    $fullWidthClass = $fullWidth ? 'w-100 d-block' : '';

    // Button classes (MYDS-compliant)
    $buttonClass = implode(' ', [
        'myds-btn',
        $sizeClass,
        $variantClass,
        $fullWidthClass,
        $disabledClass,
        'fw-semibold',
        'd-inline-flex align-items-center justify-content-center gap-2',
        'text-uppercase',
        // For accessibility: focus ring & keyboard nav
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2',
        // Motion tokens (easeoutback.short)
        'transition-all',
    ]);
@endphp

<button
    {{ $attributes->merge([
        'type' => $type,
        'class' => $buttonClass,
        'aria-disabled' => $disabled ? 'true' : 'false',
        'tabindex' => $disabled ? '-1' : '0',
        'disabled' => $disabled,
    ]) }}
>
    {{-- Leading icon (if provided) --}}
    @if($icon)
        <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>
    @endif

    {{-- Button text slot --}}
    <span>{{ $slot }}</span>

    {{-- Counter indicator (if provided) --}}
    @if($counter)
        <span class="myds-btn-counter ms-2 px-2 py-0 rounded-full bg-myds-primary-400 text-white text-xs fw-bold" aria-label="Jumlah">{{ $counter }}</span>
    @endif
</button>

{{--
    === MYDS Button Anatomy ===
    - Uses MYDS color tokens and radius for consistency.
    - Shadow and focus ring for accessibility and visual feedback.
    - ARIA attributes for screen reader support.
    - Keyboard accessible via tabindex/focus-visible.
    - Counter for notification/quantity.
    - Responsive and minimal, supports all MyGOVEA principles.
--}}
