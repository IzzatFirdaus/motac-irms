{{--
    resources/views/components/back-button.blade.php

    Reusable back button component with consistent styling and optional text.
    Commonly used for navigation between pages.

    Props:
    - $route: string - The URL or route to navigate to (required)
    - $text: string - Button text (default: 'Kembali')
    - $icon: string - Bootstrap icon class (default: 'bi-arrow-left')

    Usage:
    <x-back-button :route="route('users.index')" />
    <x-back-button :route="$previousUrl" :text="__('Go Back')" />
    <x-back-button :route="route('dashboard')" :text="__('Dashboard')" icon="bi-house" />

    Dependencies: Bootstrap 5, Bootstrap Icons
--}}
@props([
    'route',
    'text' => __('Kembali'),
    'icon' => 'bi-arrow-left'
])

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'btn btn-outline-secondary d-inline-flex align-items-center']) }}>
    <i class="bi {{ $icon }} @if($text) me-1 @endif"></i>
    @if($text)
        <span>{{ $text }}</span>
    @endif
</a>
