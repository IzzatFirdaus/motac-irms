{{-- Recursive partial for menu/submenus. Expects: $menuItems, $role, $configData, $currentRouteName --}}
@foreach ($menuItems as $menu)
    @php
        // Convert to object for property access
        $menu = (object) $menu;
        // 1. Role-Based Check: Admin sees all, else check roles
        $canView = false;
        if (Auth::check()) {
            if ($role === 'Admin' || (isset($menu->role) && in_array($role, (array) $menu->role)) || !isset($menu->role)) {
                $canView = true;
            }
        }
        // Guest users never see menu items except if explicitly allowed (rare)
        // 2. Check for submenu existence
        $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0;
        // 3. Active state determination
        $isActive = false;
        if (isset($menu->routeName) && $currentRouteName === $menu->routeName) {
            $isActive = true;
        } elseif (isset($menu->routeNamePrefix)) {
            foreach (explode(',', $menu->routeNamePrefix) as $prefix) {
                if (str_starts_with($currentRouteName, trim($prefix))) {
                    $isActive = true;
                    break;
                }
            }
        }
        // 4. Menu href
        $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
        if ($hasSubmenu) {
            $menuHref = '#';
        }
    @endphp

    @if ($canView)
        {{-- A. Render menu header --}}
        @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase text-muted fw-bold">
                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
        {{-- B. Render menu item --}}
        @else
            <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                <a href="{{ $menuHref }}"
                    class="{{ $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link' }}"
                    @if (isset($menu->target)) target="{{ $menu->target }}" rel="noopener noreferrer" @endif>
                    @isset($menu->icon)
                        <i class="menu-icon tf-icons bi bi-{{ $menu->icon }}"></i>
                    @endisset
                    <div>{{ __($menu->name ?? '-') }}</div>
                </a>
                @if ($hasSubmenu)
                    <ul class="menu-sub">
                        @include('layouts.sections.menu.submenu-partial', [
                            'menuItems' => $menu->submenu,
                            'role' => $role,
                            'configData' => $configData,
                            'currentRouteName' => $currentRouteName,
                        ])
                    </ul>
                @endif
            </li>
        @endif
    @endif
@endforeach
