{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">
    @if (!($navbarFull ?? false))
        <div class="app-brand demo px-3 py-2 border-bottom">
            <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}"
                        alt="{{ __('Logo Aplikasi') }}" height="32">
                </span>
                <span class="app-brand-text fw-semibold">{{ __($configData['templateName'] ?? 'Sistem MOTAC') }}</span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link ms-auto">
                <i class="ti ti-x ti-sm align-middle d-block d-xl-none"></i>
            </a>
        </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
            @foreach ($menuData->menu as $menu)
                @php
                    $canViewMenu = false;
                    if ($role === 'Admin') {
                        $canViewMenu = true;
                    } elseif (isset($menu->role)) {
                        $canViewMenu = in_array($role, (array) $menu->role);
                    } else {
                        $canViewMenu = true;
                    }

                    $isActive = false;
                    $currentRouteName = Route::currentRouteName();

                    if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                        $isActive = true;
                    } elseif (
                        isset($menu->routeNamePrefix) &&
                        str_starts_with($currentRouteName, $menu->routeNamePrefix)
                    ) {
                        $isActive = true;
                    } elseif (!empty($menu->submenu)) {
                        foreach ($menu->submenu as $subItem) {
                            if (isset($subItem->routeName) && $subItem->routeName === $currentRouteName) {
                                $isActive = true;
                                break;
                            }
                        }
                    }

                    $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu);
                    $menuItemClass = $isActive ? ($hasSubmenu ? 'active open' : 'active') : '';
                    $menuLinkClass = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
                    $menuHref =
                        $menu->url ??
                        (isset($menu->routeName) && Route::has($menu->routeName)
                            ? route($menu->routeName)
                            : 'javascript:void(0);');
                @endphp

                @if ($canViewMenu)
                    @if (isset($menu->menuHeader))
                        <li class="menu-header small text-uppercase text-muted fw-bold">
                            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                        </li>
                    @else
                        <li class="menu-item {{ $menuItemClass }}">
                            <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}"
                                @if (isset($menu->target)) target="{{ $menu->target }}" @endif>
                                @isset($menu->icon)
                                    <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                                @endisset
                                <div class="menu-item-label">{{ __($menu->name ?? '-') }}</div>
                                @isset($menu->badge)
                                    <span class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                        {{ __($menu->badge[1]) }}
                                    </span>
                                @endisset
                            </a>

                            @if ($hasSubmenu)
                                @include('layouts.sections.menu.submenu', [
                                    'menu' => $menu->submenu,
                                    'configData' => $configData,
                                    'currentUserRole' => $role,
                                ])
                            @endif
                        </li>
                    @endif
                @endif
            @endforeach
        @else
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-alert-circle"></i>
                    <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
