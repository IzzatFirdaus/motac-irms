{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
    {{-- The main container for the vertical menu --}}
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">

        {{-- Brand Logo section at the top of the menu --}}
        <div class="app-brand demo px-3 py-2 border-bottom">
            <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo-icon.svg') }}"
                        alt="{{ __('Logo Aplikasi') }}" height="32">
                </span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __($configData['templateName'] ?? config('app.name')) }}</span>
            </a>

            {{-- Mobile menu toggle (optional, theme-dependent) --}}
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" aria-label="{{ __('Tutup/Buka Menu Sisi') }}">
                <i class="bi bi-list d-block fs-4 align-middle"></i>
            </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" role="menu">
            {{-- Check if the menu data from the component is valid --}}
            @if (isset($menuData) && !empty($menuData->menu))
                {{--
                  This is the entry point for the menu rendering.
                  It includes the recursive partial and passes the top-level menu items to it.
                --}}
                @include('layouts.sections.menu.submenu-partial', ['menuItems' => $menuData->menu, 'currentUserRole' => $role])
            @else
                {{-- Fallback message if menu data fails to load --}}
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
