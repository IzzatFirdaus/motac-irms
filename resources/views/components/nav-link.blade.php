{{--
    resources/views/components/nav-link.blade.php

    Renders a navigation link (li > a) with support for active state and icons.
    Used in navigation bars and side menus.

    Props:
    - $active: bool - Whether the link is active (default: false)
    - $icon: string|null - Optional Bootstrap icon class (e.g. 'bi-house')
    - $attributes: Link attributes (href, etc.)

    Usage:
    <x-nav-link :active="request()->routeIs('dashboard')" href="{{ route('dashboard') }}" icon="bi-house">
        Dashboard
    </x-nav-link>
--}}
@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'nav-link active fw-semibold d-flex align-items-center'
            : 'nav-link d-flex align-items-center';
@endphp

<li class="nav-item">
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }} me-2"></i>@endif
        {{ $slot }}
    </a>
</li>
