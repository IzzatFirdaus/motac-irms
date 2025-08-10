{{--
    Canonical MOTAC IRMS vertical sidebar menu using Livewire.
    Uses recursive submenu-partial for all submenus.
    CSS and JS are loaded as assets, not inline.

    Updated:
    - Scoped for .motac-vertical-menu, consistent with sidebar.css.
    - If user is a guest, shows only guest menu items (those with 'guestOnly' => true in config/menu.php).
    - If authenticated, shows only their allowed menu items (never guest-only).
    - Fallback: If no menu, show login link (for guest) or "No menu available" (for authenticated).
--}}

<aside id="layout-menu" class="motac-vertical-menu" aria-label="Navigasi Sistem">
    <div class="sidebar-header">
        <span class="sidebar-logo">
            <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}"
                alt="{{ __('Logo Aplikasi') }}" height="32">
        </span>
        <span class="sidebar-title fw-semibold">{{ __($configData['templateName'] ?? 'MOTAC IRMS') }}</span>
        {{-- Ministry name commented out as per requirements --}}
    </div>

    <ul class="sidebar-menu">
        {{-- Render menu if menuData and its 'menu' property exist and are a non-empty array --}}
        @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu) && count($menuData->menu))
            {{-- Use the unified, recursive partial for all submenus --}}
            @include('layouts.sections.menu.submenu-partial', [
                'menuItems' => $menuData->menu,
                'role' => $role,
                'configData' => $configData,
                'currentRouteName' => $currentRouteName,
            ])
        @else
            {{-- Fallback for guests or no menu data --}}
            @guest
            <li class="menu-item">
                <a href="{{ route('login') }}" class="menu-link">
                    <i class="menu-icon bi bi-box-arrow-in-right"></i>
                    <div>{{ __('Sila log masuk untuk akses sistem dalaman') }}</div>
                </a>
            </li>
            @else
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link">
                    <i class="menu-icon bi bi-alert-circle"></i>
                    <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
                </a>
            </li>
            @endguest
        @endif
    </ul>
    {{--
        CSS and JS are now loaded via assets for compliance and maintainability.
        Add the following in your main layout:
        <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
        <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    --}}
</aside>
