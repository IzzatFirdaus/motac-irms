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
        // $menuData, $role, and $configData are available as public properties from the component.
        $currentUserRoleForMenu = $role; // Use the $role property from the component.
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="{{ __('Navigasi Sistem') }}">
        @if (!($configData['navbarFull'] ?? false))
            <div class="app-brand demo px-3 py-2 border-bottom">
                <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo-icon.svg') }}"
                            alt="{{ __('Logo Aplikasi') }}" height="32">
                    </span>
                    <span
                        class="app-brand-text demo menu-text fw-bold ms-2">{{ __($configData['templateName'] ?? config('app.name', 'Sistem MOTAC')) }}</span>
                </a>
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" aria-label="{{ __('Tutup/Buka Menu Sisi') }}">
                    <i class="bi bi-x-lg d-block d-xl-none fs-4 align-middle"></i>
                    <i class="bi bi-list d-none d-xl-block fs-4 align-middle menu-toggle-icon"></i>
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

        <ul class="menu-inner py-1" role="menu">
            @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu) && count($menuData->menu) > 0)
                @foreach ($menuData->menu as $menu)
                    @php
                        $menu = (object) $menu;
                        $canViewMenu = false;
                        if ($currentUserRoleForMenu === 'Admin') {
                            $canViewMenu = true;
                        } elseif (isset($menu->role)) {
                            $canViewMenu = in_array($currentUserRoleForMenu, (array) $menu->role);
                        } else {
                            $canViewMenu = isset($menu->menuHeader) ? true : Auth::check();
                        }

                        $isActive = false;
                        $currentRouteName = Route::currentRouteName();

                        if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                            $isActive = true;
                        } elseif (isset($menu->routeNamePrefix) && $currentRouteName && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
                            $isActive = true;
                        } elseif (!empty($menu->submenu)) {
                            // Check if any child (recursive) is active to mark parent as active/open
                            // This might require the same recursive check function or a simplified version
                            // For now, relying on the isMotacMenuItemActiveRecursiveCheckView from recursive-submenu.blade.php for its children
                            // For the parent itself, we can use a flag that recursive-submenu might update or check its direct children
                            $isAnyChildActive = false;
                            if (function_exists('isMotacMenuItemActiveRecursiveCheckView')) { // Check if function from recursive-submenu is loaded/available
                                isMotacMenuItemActiveRecursiveCheckView($menu, $currentRouteName, $isAnyChildActive, $currentUserRoleForMenu);
                            }
                             if ($isAnyChildActive) {
                                $isActive = true;
                            }
                        }

                        $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0;
                        // For top-level items that are parents, href should be javascript:void(0) if no direct link
                        $menuHref = ($hasSubmenu && empty($menu->url) && empty($menu->routeName))
                                    ? 'javascript:void(0);'
                                    : ($menu->url ?? (isset($menu->routeName) && Route::has((string)$menu->routeName) ? route((string)$menu->routeName) : 'javascript:void(0);'));

                        $menuLinkClass = 'menu-link' . ($hasSubmenu ? ' menu-toggle' : '');
                        $menuItemClass = $isActive ? 'active' : ''; // Simpler active class for parent, 'open' will be handled by collapse 'show'
                        if ($hasSubmenu && $isActive) {
                           // For BS5 collapse, parent being 'active' doesn't automatically mean 'open'
                           // 'open' or 'show' on the ul.menu-sub is controlled by $isParentBranchActive in recursive-submenu
                        }
                        $firstLevelSubmenuId = $hasSubmenu ? 'submenu-' . Illuminate\Support\Str::slug($menu->name ?? 'menu') . '-' . $loop->index : null;
                    @endphp

                    @if ($canViewMenu)
                        @if (isset($menu->menuHeader))
                            <li class="menu-header small text-uppercase text-muted fw-bold" role="none">
                                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                            </li>
                        @else
                            <li class="menu-item {{ $menuItemClass }}" role="none">
                                <a href="{{ $menuHref }}"
                                   class="{{ $menuLinkClass }}"
                                   role="menuitem"
                                   @if (isset($menu->target) && !empty($menu->target) && !$hasSubmenu) target="{{ $menu->target }}" rel="noopener noreferrer" @endif
                                   @if ($hasSubmenu)
                                       data-bs-toggle="collapse"
                                       data-bs-target="#{{ $firstLevelSubmenuId }}"
                                       aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                       aria-controls="{{ $firstLevelSubmenuId }}"
                                   @endif
                                >
                                    @isset($menu->icon)
                                        <i class="menu-icon bi bi-{{ $menu->icon }}"></i>
                                    @endisset
                                    <div class="menu-item-label">{{ __(($menu->name ?? null) ? $menu->name : '-') }}</div>
                                    @isset($menu->badge)
                                        <span class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                            {{ __($menu->badge[1]) }}
                                        </span>
                                    @endisset
                                </a>

                                @if ($hasSubmenu)
                                    @php
                                        // Determine the class for 'active open' based on layout
                                        $activeOpenClassForSubmenu = ($configData['myLayout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
                                    @endphp
                                    @include('livewire.sections.menu.recursive-submenu', [
                                        'submenuItems' => $menu->submenu,
                                        'parentSubmenuId' => $firstLevelSubmenuId,
                                        'isParentBranchActive' => $isActive, // This will add 'show' to the ul.menu-sub.collapse
                                        'currentRole' => $currentUserRoleForMenu,
                                        'configData' => $configData,
                                        'currentRouteName' => $currentRouteName,
                                        'activeOpenClass' => $activeOpenClassForSubmenu, // Pass this down
                                        'level' => 1 // Starting level for the first set of submenus
                                    ])
                                @endif
                            </li>
                        @endif
                    @endif
                @endforeach
            @else
                <li class="menu-item" role="none">
                    <a href="javascript:void(0);" class="menu-link" role="menuitem">
                        <i class="menu-icon bi bi-exclamation-circle-fill"></i>
                        <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
>>>>>>> 9e861a6 (040625 edits)
