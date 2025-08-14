{{-- resources/views/layouts/sections/menu/submenu-partial.blade.php --}}
@foreach ($menuItems as $menu)
    @php
        $menu = (object) $menu;
        $currentUserRole = $currentUserRole ?? (Auth::check() ? Auth::user()?->getRoleNames()->first() : null);

        // 1. Check if the user has permission to see this menu item
        $canView = false;
        if (!isset($menu->role)) {
            $canView = true; // No role restriction
        } else {
            $canView = $currentUserRole === 'Admin' || in_array($currentUserRole, (array) $menu->role);
        }
    @endphp

    @if ($canView)
        {{-- A. RENDER A MENU HEADER --}}
        @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase text-muted fw-bold">
                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>

        {{-- B. RENDER A MENU ITEM --}}
        @else
            @php
                $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
                $currentRouteName = Route::currentRouteName();
                $isActive = false;

                // 2. Check for active state (current route matches item)
                if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                    $isActive = true;
                }
                // 3. Check for active state (current route is a child of the item)
                elseif (isset($menu->routeNamePrefix)) {
                    // Split prefixes by comma to check against multiple possibilities
                    $prefixes = explode(',', $menu->routeNamePrefix);
                    foreach ($prefixes as $prefix) {
                        if (str_starts_with((string)$currentRouteName, trim($prefix))) {
                            $isActive = true;
                            break;
                        }
                    }
                }

                // 4. Determine link URL
                $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
                if ($hasSubmenu && $menuHref === 'javascript:void(0);') {
                    $menuHref = '#'; // Use '#' for dropdown toggles for better accessibility
                }
            @endphp

            <li class="menu-item {{ $isActive ? 'active' : '' }}">
                <a href="{{ $menuHref }}"
                    class="{{ $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link' }}"
                    @if (isset($menu->target) && !$hasSubmenu) target="{{ $menu->target }}" rel="noopener noreferrer" @endif
                    @if ($hasSubmenu) role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" @endif
                >
                    @isset($menu->icon)
                        <i class="menu-icon bi bi-{{ $menu->icon }}"></i>
                    @endisset
                    <div>{{ __($menu->name ?? '-') }}</div>
                    @isset($menu->badge)
                        <span class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ __($menu->badge[1]) }}</span>
                    @endisset
                </a>

                {{-- RECURSION: If there is a submenu, include this same file again --}}
                @if ($hasSubmenu)
                    <ul class="menu-sub" style="{{ $isActive ? 'display: block;' : '' }}">
                        @include('layouts.sections.menu.submenu-partial', ['menuItems' => $menu->submenu, 'currentUserRole' => $currentUserRole])
                    </ul>
                @endif
            </li>
        @endif
    @endif
@endforeach
