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
            </div>
        @endif

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
