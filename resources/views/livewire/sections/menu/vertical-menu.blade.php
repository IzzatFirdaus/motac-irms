<<<<<<< HEAD
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
=======
{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
    @php
        // $configData is made available via Helper::appClasses()
        // $menuData is made available globally by MenuServiceProvider loading verticalMenu.json [cite: 79]
        // $role is a public property from the VerticalMenu.php Livewire component [cite: 1]
        $configData = Helper::appClasses();
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

        @if (!isset($navbarFull))
            {{-- Condition from original template, $navbarFull might be a global/config variable --}}
            <div class="app-brand demo">
                <a href="{{ url('/') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        {{-- Use appLogo from $configData for consistency --}}
                        {{-- Ensure _partials.macros or direct img tag points to the correct logo --}}
                        {{-- For example, if _partials.macros expects a path: --}}
                        {{-- @include('_partials.macros', ['logo_path' => $configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg', "height"=>20]) --}}
                        {{-- Or directly using an img tag: --}}
                        <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo-icon.svg') }}"
                            alt="App Logo" height="22">
                    </span>
                    <span
                        class="app-brand-text demo menu-text fw-bold ms-2">{{ $configData['templateName'] ?? config('app.name') }}</span>
                </a>

                {{-- REVISED: Removed style="visibility: hidden" to ensure toggle is visible --}}
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                    <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                    <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                </a>
>>>>>>> 9e861a6 (040625 edits)
            </div>
            <div class="text-muted small" style="font-family: 'Inter', Arial, sans-serif;">
                {{ $user->email }}
            </div>
        </div>
    </div>
    @endif

<<<<<<< HEAD
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
=======
        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
            @if (isset($menuData) && isset($menuData->menu))
                @foreach ($menuData->menu as $menu)
                    {{-- Role-based access check using $role from VerticalMenu.php component --}}
                    {{-- The JSON uses "role": ["Role1", "Role2"] [cite: 4] --}}
                    @php
                        $menuRoles = isset($menu->role) ? (is_array($menu->role) ? $menu->role : [$menu->role]) : [];
                    @endphp
                    @if (
                        $role === 'Admin' ||
                            (isset($menu->role) && !empty($menuRoles) && in_array($role, $menuRoles)) ||
                            (!isset($menu->role) && isset($menu->menuHeader)))
                        {{-- Show header if no specific role or if user has role. Admin sees all. --}}

                        {{-- Menu headers from verticalMenu.json [cite: 4] --}}
                        @if (isset($menu->menuHeader))
                            <li class="menu-header small text-uppercase">
                                <span
                                    class="menu-header-text">{{ isset($menu->name) ? __($menu->name) : (isset($menu->menuHeader) ? __($menu->menuHeader) : '') }}</span>
                            </li>
                        @else
                            {{-- Active menu item logic --}}
                            @php
                                $activeClass = null;
                                $currentRouteName = Route::currentRouteName();

                                // Exact match for slug or routeName specified in JSON [cite: 4]
                                if ($currentRouteName === ($menu->routeName ?? ($menu->slug ?? null))) {
                                    $activeClass = 'active';
                                }
                                // If it has a submenu, check if current route name starts with any of its slug prefixes or routeNamePrefix
                                // This makes the parent "active open"
                                elseif (isset($menu->submenu)) {
                                    $matchAgainst = $menu->routeNamePrefix ?? ($menu->slug ?? null);
                                    if ($matchAgainst) {
                                        if (is_array($matchAgainst)) {
                                            foreach ($matchAgainst as $slug_or_prefix) {
                                                if (str_starts_with($currentRouteName, $slug_or_prefix)) {
                                                    $activeClass = 'active open';
                                                    break;
                                                }
                                            }
                                        } else {
                                            if (str_starts_with($currentRouteName, $matchAgainst)) {
                                                $activeClass = 'active open';
                                            }
                                        }
                                    }
                                }
                            @endphp

                            {{-- Main menu item --}}
                            <li class="menu-item {{ $activeClass }}">
                                <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                                    class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                                    @if (isset($menu->target) and !empty($menu->target)) target="{{ $menu->target }}" @endif>
                                    @isset($menu->icon)
                                        <i class="{{ $menu->icon }}"></i>
                                    @endisset
                                    <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                                    @isset($menu->badge)
                                        <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                            {{ $menu->badge[1] }}</div>
                                    @endisset
                                </a>

                                {{-- Include submenu if it exists --}}
                                @isset($menu->submenu)
                                    {{-- Pass $menu->submenu and $role to the submenu partial --}}
                                    @include('layouts.sections.menu.submenu', [
                                        'menu' => $menu->submenu,
                                        'role' => $role,
                                        'configData' => $configData,
                                    ])
                                @endisset
                            </li>
                        @endif
                    @endif
                @endforeach
            @else
                <li class="menu-item"><a href="#" class="menu-link">
                        <div>{{ __('menu.not_available') }}</div>
                    </a></li>
            @endif
        </ul>
    </aside>
</div>
>>>>>>> 9e861a6 (040625 edits)
