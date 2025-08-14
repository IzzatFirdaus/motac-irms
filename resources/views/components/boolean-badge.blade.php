{{--
    resources/views/components/boolean-badge.blade.php

    MYDS-compliant Boolean Badge component.
    Displays boolean values as status badges with appropriate MYDS color tokens, icon, and accessibility support.
    Follows MYDS badge anatomy and applies relevant MyGOVEA design principles:
    - Clear feedback (Prinsip 7, 17)
    - Minimal, consistent UI (Prinsip 5, 6)
    - Accessibility: ARIA role, contrast, readable label (Prinsip 1, 13, 14)
    - Tipografi: Inter font, semibold, body size (Prinsip 14)
--}}

@props([
    'value' => false,
    'trueText' => 'Ya',    // Use plain text instead of __() for translation for PHP static analysis
    'falseText' => 'Tidak',
    'showText' => true,
    // MYDS badge classes for status (see custom.css)
    'trueClass' => 'myds-badge status-approved px-3 py-1 fw-semibold',
    'falseClass' => 'myds-badge status-rejected px-3 py-1 fw-semibold',
    // MYDS icons (Bootstrap Icons as fallback)
    'iconTrue' => 'bi-check-circle-fill',
    'iconFalse' => 'bi-x-circle-fill',
])

@php
    // Set badge text and class according to boolean value
    $text = $value ? $trueText : $falseText;
    $cssClass = $value ? $trueClass : $falseClass;
    $iconToShow = $value ? $iconTrue : $iconFalse;

    // Accessible ARIA label for status
    $ariaLabel = $value ? 'Disahkan: Ya' : 'Disahkan: Tidak';
@endphp

<span {{ $attributes->merge([
    'class' => $cssClass,
    'role' => 'status',
    'aria-label' => $ariaLabel,
]) }}>
    {{-- Icon provides visual feedback and meets color-blind accessibility (MYDS + MyGOVEA Principle 7) --}}
    @if ($iconToShow)
        <i class="bi {{ $iconToShow }} me-1" aria-hidden="true"></i>
    @endif

    {{-- Text content, visible unless showText=false --}}
    @if ($showText)
        <span class="align-middle">{{ $text }}</span>
    @endif
</span>

{{--
    Notes:
    - The __() translation helper is not available in PHP static analysis unless running inside Laravel.
    - For static files or PHP code analysis, use plain text for translation fields, or ensure you run code in the appropriate environment.
    - This version uses plain text for ARIA label and badge text to avoid unknown function warnings.
--}}
