{{--
    MYDS-compliant Vertical Menu Section
    resources/views/livewire/sections/menu/vertical-menu.blade.php

    Displays the main navigation menu using MYDS design system principles:
    - Uses MYDS color tokens, spacing, typography, icons and radius.
    - Accessible: ARIA roles, keyboard navigation, semantic structure.
    - Follows MyGOVEA: minimal, clear, seragam, documented, error prevention.

    NOTE: Uses asset() and route() helpers for static/image and URL generation.
    For static analysis environments, fallbacks are provided in case these functions
    are not available (PHP0417 warning).

    Props:
    - $menuItems: array of menu items (icon, title, route, active)
    - $logo: logo image filename (string)
    - $user: authenticated user object (optional, for profile section)

    Usage:
    @livewire('sections.menu.vertical-menu', ['menuItems' => $menus, 'logo' => 'motac-logo.png', 'user' => Auth::user()])
--}}

@props([
    'menuItems' => [],
    'logo' => 'motac-logo.png',
    'user' => null,
])

@php
    // Helper for asset() and route() for compatibility with static analysis
    $logoUrl = function_exists('asset')
        ? asset('assets/img/' . $logo)
        : '/assets/img/' . $logo;

    $getRoute = function($routeName, $params = []) {
        return function_exists('route')
            ? route($routeName, $params)
            : ('/' . str_replace('.', '/', $routeName) . (!empty($params) ? '/' . implode('/', $params) : ''));
    };
@endphp

<nav class="myds-vertical-menu shadow-card myds-radius-l bg-white" role="navigation" aria-label="Main Navigation">
    {{-- Logo section --}}
    <div class="myds-menu-logo text-center py-4">
        <img src="{{ $logoUrl }}" alt="Logo MOTAC"
             class="img-fluid" style="max-height: 48px;" />
    </div>

    {{-- User Profile (optional) --}}
    @if($user)
    <div class="myds-menu-profile d-flex align-items-center px-4 py-2 mb-3">
        <img src="{{ isset($user->profile_photo_url) && $user->profile_photo_url ? $user->profile_photo_url : (function_exists('asset') ? asset('assets/img/avatars/default-avatar.png') : '/assets/img/avatars/default-avatar.png') }}"
             alt="{{ $user->name }} avatar"
             class="rounded-circle me-2"
             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--myds-primary-200); background: var(--myds-bg-white);" />
        <div>
            <div class="fw-semibold text-primary-700" style="font-family: 'Poppins', Arial, sans-serif;">
                {{ $user->name }}
            </div>
            <div class="text-muted small" style="font-family: 'Inter', Arial, sans-serif;">
                {{ $user->email }}
            </div>
        </div>
    </div>
    @endif

    {{-- Menu Items --}}
    <ul class="myds-menu-list list-unstyled px-2 mb-0">
        @foreach ($menuItems as $item)
            @php
                // Determine route URL
                $routeUrl = isset($item['route'])
                    ? (is_array($item['route']) ? $getRoute($item['route'][0], $item['route'][1] ?? []) : $getRoute($item['route']))
                    : '#';
            @endphp
            <li class="myds-menu-item mb-2" role="none">
                <a href="{{ $routeUrl }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-md fw-medium transition-colors
                        {{ ($item['active'] ?? false) ? 'bg-primary-50 text-primary-700 border-start border-primary' : 'text-body-secondary border-start border-transparent hover:bg-primary-100 focus:bg-primary-100' }}"
                   role="menuitem"
                   tabindex="0"
                   aria-current="{{ ($item['active'] ?? false) ? 'page' : null }}"
                >
                    {{-- Icon --}}
                    @if(isset($item['icon']))
                        <i class="bi {{ $item['icon'] }} fs-5 me-2" aria-hidden="true"></i>
                    @endif
                    <span>{{ $item['title'] ?? 'Menu' }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>

{{--
    === Documentation & MYDS Compliance Notes ===
    - Logo, profile, and menu items use MYDS color, radius, shadow, and spacing tokens.
    - Icons use Bootstrap Icons, aria-hidden for accessibility.
    - Menu item uses ARIA roles: navigation, menuitem, aria-current for active state.
    - Routes and asset paths have fallbacks for environments where helpers are unavailable.
    - Keyboard accessible: tabindex="0" on menu items.
    - Responsive: use grid/spacing for vertical orientation.
    - Follows MyGOVEA: minimal, clear, error prevention, documentation.
--}}
