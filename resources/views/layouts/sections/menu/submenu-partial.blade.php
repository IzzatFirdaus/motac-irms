{{-- resources/views/layouts/sections/menu/submenu-partial.blade.php --}}
@foreach ($menuItems as $menu)
    @php
        $menu = (object) $menu;
        // If currentUserRole isn't passed down, get it again.
        $currentUserRole = $currentUserRole ?? (Auth::check() ? Auth::user()?->getRoleNames()->first() : null);

        // 1. Check if the user has permission to see this menu item
        $canView = false;
        if (!isset($menu->role)) {
            $canView = true; // No role restriction, visible to all.
        } else {
            // Admin can see everything. Otherwise, check if user's role is in the list.
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

                // 2. Check for active state (current route matches the item's routeName)
                if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                    $isActive = true;
                }
                // 3. Check for active state (current route starts with the item's routeNamePrefix)
                elseif (isset($menu->routeNamePrefix)) {
                    // Split by comma allows for multiple prefixes, e.g., for "My Applications"
                    $prefixes = explode(',', $menu->routeNamePrefix);
                    foreach ($prefixes as $prefix) {
                        if (str_starts_with((string)$currentRouteName, trim($prefix))) {
                            $isActive = true;
                            break;
                        }
                    }
                }

                // 4. Determine the link URL
                $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
                // For accessibility, dropdown toggles should link to '#'
                if ($hasSubmenu && $menuHref === 'javascript:void(0);') {
                    $menuHref = '#';
                }
            @endphp

            <li class="menu-item {{ $isActive ? 'active' : '' }}" role="none">
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
