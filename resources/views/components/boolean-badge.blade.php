{{-- resources/views/components/boolean-badge.blade.php --}}
@props([
    'value' => false, // The boolean value to display
    'trueText' => __('Ya'), // Text to display if value is true
    'falseText' => __('Tidak'), // Text to display if value is false
    'showText' => true, // Whether to display the text or just a visual indicator
    'trueClass' => 'badge bg-success-subtle text-success-emphasis rounded-pill px-2 py-1 small fw-medium', // Default class for true
    'falseClass' => 'badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1 small fw-medium', // Default class for false
    'iconTrue' => null, // Optional icon for true state (e.g., 'bi bi-check-circle-fill')
    'iconFalse' => null, // Optional icon for false state (e.g., 'bi bi-x-circle-fill')
])

@php
    $text = $value ? $trueText : $falseText;
    $cssClass = $value ? $trueClass : $falseClass;
    $iconToShow = $value ? $iconTrue : $iconFalse;
@endphp

<span {{ $attributes->merge(['class' => $cssClass]) }}>
    @if ($iconToShow)
        <i class="{{ $iconToShow }} me-1"></i>
    @endif
    @if ($showText)
        {{ $text }}
    @endif
</span>
