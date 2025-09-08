{{--
    MYDS-compliant Navigation Link Component
    - Renders a navigation link (li > a) for navbars and side menus
    - Applies MYDS color tokens, typography, spacing, accessibility, and icon standards
    - Follows MyGOVEA principles: minimalism, clarity, accessibility, hierarchy, and user control

    Props:
    - $active: bool - Whether the link is active (default: false)
    - $icon: string|null - Optional Bootstrap/MYDS icon class (e.g. 'bi-house')
    - $attributes: Link attributes (href, aria, etc.)

    Usage:
    <x-nav-link :active="request()->routeIs('dashboard')" href="{{ route('dashboard') }}" icon="bi-house">
        Dashboard
    </x-nav-link>
--}}

@props(['active' => false, 'icon' => null])

@php
    // MYDS-compliant classes for nav link
    $baseClasses = 'myds-navbar-link d-flex align-items-center fw-medium px-3 py-2 rounded-md transition-colors';
    $activeClasses = 'active fw-semibold text-primary bg-primary-50 border-start border-primary';
    $inactiveClasses = 'text-body-secondary border-start border-transparent hover:bg-primary-100 focus:bg-primary-100';

    // Accessibility: aria-current for active state
    $ariaCurrent = ($active ?? false) ? 'page' : null;

    // Compose final classes
    $classes = $baseClasses . ' ' . (($active ?? false) ? $activeClasses : $inactiveClasses);
@endphp

<li class="nav-item" role="none">
    <a
        {{ $attributes->merge([
            'class' => $classes,
            'aria-current' => $ariaCurrent,
            'role' => 'menuitem',
            'tabindex' => '0',
        ]) }}
    >
        @if($icon)
            <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
</li>

{{--
    === MYDS & MyGOVEA Principles Applied ===
    - Minimalist, clear design: only necessary icon/text, clear active/inactive state
    - Accessibility: aria-current for current page, role attributes, focusable/tabindex
    - Hierarchy: active link is visually distinct, aiding navigation
    - Typography: MYDS font weight, spacing
    - Responsive & adaptive: MYDS classes adapt across breakpoints
    - User control: visible focus/hover state, keyboard accessible
--}}
