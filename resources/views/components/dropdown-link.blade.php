{{--
    resources/views/components/dropdown-link.blade.php

    Individual dropdown menu item component.
    Provides consistent styling for dropdown links.

    Usage:
    <x-dropdown-link href="{{ route('profile') }}">{{ __('Profile') }}</x-dropdown-link>
    <x-dropdown-link href="{{ route('settings') }}" class="text-danger">{{ __('Settings') }}</x-dropdown-link>

    Dependencies: Bootstrap 5
--}}
<a {{ $attributes->merge(['class' => 'dropdown-item']) }}>{{ $slot }}</a>
