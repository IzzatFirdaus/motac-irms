{{--
    resources/views/components/secondary-button.blade.php

    Outlined secondary button for non-destructive actions.
    Accepts an optional icon class.

    Props:
    - $icon: string|null - Bootstrap icon class to show before text

    Usage:
    <x-secondary-button>Cancel</x-secondary-button>
    <x-secondary-button icon="bi-arrow-left">Back</x-secondary-button>
--}}
@props(['icon' => null])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary text-uppercase d-inline-flex align-items-center']) }}>
    @if($icon)<i class="bi {{ $icon }} me-1"></i>@endif
    {{ $slot }}
</button>
