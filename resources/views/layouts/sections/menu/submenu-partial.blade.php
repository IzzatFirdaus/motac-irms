{{-- resources/views/layouts/sections/menu/submenu-partial.blade.php --}}
@php \Illuminate\Support\Facades\Log::info('Rendering submenu-partial.blade.php'); @endphp
@foreach ($menuItems as $menu)
    @php
        $menu = (object) $menu;
        \Illuminate\Support\Facades\Log::debug('Processing menu item: ' . ($menu->name ?? 'Header: ' . ($menu->menuHeader ?? 'Untitled')));

        // 1. Role-Based Check: Check if the user has the required role.
        //    - Admins see everything.
        //    - If no 'role' is specified, the item is visible to any authenticated user.
        //    - Otherwise, the user must have at least one of the specified roles.
        $canView = false;
        if (Auth::check()) {
            \Illuminate\Support\Facades\Log::debug('User is authenticated. User roles: ' . json_encode(Auth::user()->getRoleNames())); // Assuming spatie/laravel-permission

            if (Auth::user()->hasRole('Admin')) {
                $canView = true;
                \Illuminate\Support\Facades\Log::debug('User is Admin. canView set to true for: ' . ($menu->name ?? $menu->menuHeader ?? ''));
            } elseif (!isset($menu->role)) {
                $canView = true; // No roles defined, visible to all logged-in users.
                \Illuminate\Support\Facades\Log::debug('No specific role defined. canView set to true for: ' . ($menu->name ?? $menu->menuHeader ?? ''));
            } else {
                $requiredRoles = (array) $menu->role;
                $canView = Auth::user()->hasAnyRole($requiredRoles);
                \Illuminate\Support\Facades\Log::debug('Checking roles for menu item "' . ($menu->name ?? $menu->menuHeader ?? '') . '". Required: ' . json_encode($requiredRoles) . '. Can view: ' . ($canView ? 'true' : 'false'));
            }
        } else {
            \Illuminate\Support\Facades\Log::debug('User is not authenticated for menu item: ' . ($menu->name ?? $menu->menuHeader ?? ''));
        }
    @endphp

    @if ($canView)
        {{-- Log if menu item is viewable --}}
        @php \Illuminate\Support\Facades\Log::debug('Menu item is viewable: ' . ($menu->name ?? $menu->menuHeader ?? '')); @endphp

        {{-- A. RENDER A MENU HEADER --}}
        @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase text-muted fw-bold">
                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
            {{-- Log for menu header --}}
            @php \Illuminate\Support\Facades\Log::debug('Rendered menu header: ' . $menu->menuHeader); @endphp

        {{-- B. RENDER A MENU ITEM --}}
        @else
            @php
                $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
                $currentRouteName = Route::currentRouteName();
                $isActive = false;

                // Check for active state (current route matches the item's routeName)
                if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                    $isActive = true;
                }
                // Check for active state (current route starts with the item's routeNamePrefix)
                elseif (isset($menu->routeNamePrefix)) {
                    $prefixes = explode(',', $menu->routeNamePrefix);
                    foreach ($prefixes as $prefix) {
                        if (str_starts_with((string)$currentRouteName, trim($prefix))) {
                            $isActive = true;
                            break;
                        }
                    }
                }
                \Illuminate\Support\Facades\Log::debug('Menu item "' . $menu->name . '" active check. currentRouteName: ' . $currentRouteName . ', routeName: ' . ($menu->routeName ?? 'N/A') . ', routeNamePrefix: ' . ($menu->routeNamePrefix ?? 'N/A') . '. isActive: ' . ($isActive ? 'true' : 'false'));

                $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
                if ($hasSubmenu) {
                    $menuHref = '#';
                }
            @endphp

            <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                <a href="{{ $menuHref }}"
                    class="{{ $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link' }}"
                    @if (isset($menu->target)) target="{{ $menu->target }}" rel="noopener noreferrer" @endif>
                    @isset($menu->icon)
                        <i class="menu-icon tf-icons bi bi-{{ $menu->icon }}"></i>
                    @endisset
                    <div>{{ __($menu->name ?? '-') }}</div>
                </a>

                {{-- RECURSION: If there is a submenu, include this same file again --}}
                @if ($hasSubmenu)
                    {{-- Log before recursive call --}}
                    @php \Illuminate\Support\Facades\Log::debug('Found submenu for "' . $menu->name . '". Recursively including submenu-partial.'); @endphp
                    <ul class="menu-sub">
                        @include('layouts.sections.menu.submenu-partial', ['menuItems' => $menu->submenu])
                    </ul>
                @endif
            </li>
        @endif
    @else
        {{-- Log if menu item is NOT viewable --}}
        @php \Illuminate\Support\Facades\Log::debug('Menu item is NOT viewable (due to role or no auth): ' . ($menu->name ?? $menu->menuHeader ?? '')); @endphp
    @endif
@endforeach
@php \Illuminate\Support\Facades\Log::info('Finished rendering submenu-partial.blade.php'); @endphp
