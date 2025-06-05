{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
    @php
        // $menuData, $role, and $configData are available as public properties from the component.
        // These were set in the mount() method.
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
            </div>
        @endif

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" role="menu">
            {{-- This $menuData refers to the public $menuData property of the Livewire component --}}
            @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu) && count($menuData->menu) > 0)
                @foreach ($menuData->menu as $menu)
                    @php
                        // Ensure $menu is an object if it's coming from JSON decoded as assoc array then cast to object
                        // If $menu is already an object from config/menu.php, this is fine.
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
                        $currentRouteName = Route::currentRouteName(); // Make sure Route facade is used or $currentRouteName is passed

                        if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                            $isActive = true;
                        } elseif (isset($menu->routeNamePrefix) && $currentRouteName && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
                            $isActive = true;
                        } elseif (!empty($menu->submenu)) {
                            // This active check for submenus might need to be more robust for deeper levels
                            // For now, checking direct children:
                            foreach ($menu->submenu as $subItem) {
                                $subItem = (object) $subItem; // Ensure object access
                                if (isset($subItem->routeName) && $subItem->routeName === $currentRouteName) {
                                    $isActive = true; break;
                                }
                                if (isset($subItem->routeNamePrefix) && $currentRouteName && str_starts_with($currentRouteName, $subItem->routeNamePrefix)){
                                    $isActive = true; break;
                                }
                                // Add recursive check here if needed for deeper submenus to activate parent
                            }
                        }

                        $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0;
                        $menuItemClass = $isActive ? ($hasSubmenu ? 'active open' : 'active') : '';
                        $menuLinkClass = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
                        $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has((string)$menu->routeName) ? route((string)$menu->routeName) : 'javascript:void(0);');
                    @endphp

                    @if ($canViewMenu)
                        @if (isset($menu->menuHeader))
                            <li class="menu-header small text-uppercase text-muted fw-bold" role="none">
                                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                            </li>
                        @else
                            <li class="menu-item {{ $menuItemClass }}" role="none">
                                <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}"
                                    role="menuitem"
                                    @if (isset($menu->target) && !empty($menu->target)) target="{{ $menu->target }}" rel="noopener noreferrer" @endif
                                    @if ($hasSubmenu) aria-haspopup="true" aria-expanded="{{ $isActive ? 'true' : 'false' }}" @endif>
                                    @isset($menu->icon)
                                        {{-- Ensure Bootstrap icons are used if that's the chosen format --}}
                                        {{-- If $menu->icon contains full class like 'tf-icons ti ti-icon', then: <i class="{{ $menu->icon }}"></i> --}}
                                        {{-- If $menu->icon is just 'icon-name' for Bootstrap: --}}
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
                                    @include('layouts.sections.menu.submenu', [
                                        'menu' => $menu->submenu, // Pass the submenu array
                                        'role' => $currentUserRoleForMenu,
                                        'configData' => $configData,
                                        'currentRouteName' => $currentRouteName
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
