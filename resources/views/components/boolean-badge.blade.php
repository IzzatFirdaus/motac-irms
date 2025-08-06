{{-- resources/views/components/boolean-badge.blade.php --}}
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
