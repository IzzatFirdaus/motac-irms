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
    'trueText' => __('Ya'),
    'falseText' => __('Tidak'),
    'showText' => true,
    // MYDS badge classes for status (see custom.css)
    'trueClass' => 'myds-badge status-approved px-3 py-1 fw-semibold',
    'falseClass' => 'myds-badge status-rejected px-3 py-1 fw-semibold',
    // MYDS icons (Bootstrap Icons as fallback)
    'iconTrue' => 'bi-check-circle-fill',
    'iconFalse' => 'bi-x-circle-fill',
])

@php
    $text = $value ? $trueText : $falseText;
    $cssClass = $value ? $trueClass : $falseClass;
    $iconToShow = $value ? $iconTrue : $iconFalse;
    $ariaLabel = $value ? __('Disahkan: Ya') : __('Disahkan: Tidak');
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
