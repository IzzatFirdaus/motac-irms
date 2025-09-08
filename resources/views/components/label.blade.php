{{--
    resources/views/components/label.blade.php

    MYDS-compliant form label component.
    - Applies MYDS typography, spacing, and accessibility standards.
    - Designed for clear hierarchy, readability, and proper association with form controls.
    - Compliant with MyGOVEA Principles: Tipografi, Minimalis, Seragam, Struktur Hierarki, Komponen UI/UX.

    Props:
    - $value: string - Label text (optional, can use slot instead)
    - $size: string - Label size: 'sm', 'md', 'lg' (default: 'md')

    Usage:
    <x-label for="email" value="{{ __('Email Address') }}" />
    <x-label for="name" size="lg">{{ __('Full Name') }}</x-label>
--}}

@props([
    'value' => null,
    'size' => 'md', // MYDS label size: sm, md, lg
])

@php
    // Map MYDS sizes to font classes and spacing
    $sizeClasses = [
        'sm' => 'myds-label-sm', // font-size: 14px; line-height: 20px;
        'md' => 'myds-label-md', // font-size: 16px; line-height: 24px;
        'lg' => 'myds-label-lg', // font-size: 18px; line-height: 26px;
    ];
    $labelClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<label {{ $attributes->merge(['class' => "form-label fw-medium {$labelClass}"]) }}>
  {{-- Use slot if value is not set for flexibility --}}
  {{ $value ?? $slot }}
</label>

{{--
    MYDS Label Anatomy Reference:
    - Font: Inter, medium (500)
    - Color: var(--myds-txt-black-900)
    - Spacing: 8px below label before input (see custom.css)
    - Accessibility: Always use 'for' attribute for proper association

    Example CSS in custom.css:
    .myds-label-md { font-size: 16px; line-height: 24px; color: var(--myds-txt-black-900); }
    .myds-label-sm { font-size: 14px; line-height: 20px; color: var(--myds-txt-black-900); }
    .myds-label-lg { font-size: 18px; line-height: 26px; color: var(--myds-txt-black-900); }
--}}
