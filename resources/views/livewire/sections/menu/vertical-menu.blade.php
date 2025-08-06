{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
{{-- Main vertical menu rendered by Livewire. Uses recursive submenu-partial. --}}
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">
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

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        {{-- Render menu if menuData and its 'menu' property exist and are an array --}}
        @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
            {{-- Use the unified, recursive partial for all submenus --}}
            @include('layouts.sections.menu.submenu-partial', [
                'menuItems' => $menuData->menu,
                'role' => $role,
                'configData' => $configData,
                'currentRouteName' => $currentRouteName,
            ])
        @else
            {{-- Fallback for no menu data --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-alert-circle"></i>
                    <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
