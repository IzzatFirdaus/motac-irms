{{--
    resources/views/components/boolean-badge.blade.php

    Displays boolean values as styled badges with customizable appearance.
    Supports icons, custom text, and flexible styling options.

    Props:
    - $value: bool - The boolean value to display (default: false)
    - $trueText: string - Text for true state (default: 'Ya')
    - $falseText: string - Text for false state (default: 'Tidak')
    - $showText: bool - Whether to show text (default: true)
    - $trueClass: string - CSS classes for true state
    - $falseClass: string - CSS classes for false state
    - $iconTrue: string - Icon class for true state (optional)
    - $iconFalse: string - Icon class for false state (optional)

    Usage:
    <x-boolean-badge :value="$user->is_active" />
    <x-boolean-badge :value="$setting->enabled" :trueText="__('Enabled')" :falseText="__('Disabled')" />
    <x-boolean-badge :value="$status" iconTrue="bi-check-circle" iconFalse="bi-x-circle" />

    Dependencies: Bootstrap 5
--}}
@props([
    'value' => false,
    'trueText' => __('Ya'),
    'falseText' => __('Tidak'),
    'showText' => true,
    'trueClass' => 'badge bg-success-subtle text-success-emphasis rounded-pill px-2 py-1 small fw-medium',
    'falseClass' => 'badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1 small fw-medium',
    'iconTrue' => null,
    'iconFalse' => null,
])

@php
    // Determine display values based on boolean state
    $text = $value ? $trueText : $falseText;
    $cssClass = $value ? $trueClass : $falseClass;
    $iconToShow = $value ? $iconTrue : $iconFalse;
@endphp

<span {{ $attributes->merge(['class' => $cssClass]) }}>
    {{-- Optional Icon --}}
    @if ($iconToShow)
        <i class="{{ $iconToShow }} me-1"></i>
    @endif

    {{-- Text Content --}}
    @if ($showText)
        {{ $text }}
    @endif
</span>
