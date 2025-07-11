{{-- resources/views/layouts/sections/menu/submenu.blade.php --}}
{{-- This partial expects $menu (the array of submenu items), $role (current user's role), $configData, and $currentRouteName to be passed to it --}}
<ul class="menu-sub" role="menu">
    @if (isset($menu) && is_array($menu))
        @foreach ($menu as $submenuItem)
            @php
                // Ensure $submenuItem is an object for consistent property access
                $submenuItem = (object) $submenuItem;

                // Role check for the submenu item
                $canViewSubmenuItem = false;
                if ($role === 'Admin') {
                    // $role is the current user's role passed from the parent
    $canViewSubmenuItem = true;
} elseif (isset($submenuItem->role)) {
    $canViewSubmenuItem = in_array($role, (array) $submenuItem->role);
} else {
    // Default visibility for submenu items without a specific role: visible if user is authenticated
    $canViewSubmenuItem = Auth::check();
}

// Active state determination
$subActiveCheck = false;
if (
    isset($currentRouteName) &&
    isset($submenuItem->routeName) &&
    $currentRouteName === $submenuItem->routeName
) {
    $subActiveCheck = true;
} elseif (
    isset($currentRouteName) &&
    isset($submenuItem->routeNamePrefix) &&
    str_starts_with($currentRouteName, $submenuItem->routeNamePrefix)
) {
    $subActiveCheck = true;
}

// Check if there's a nested submenu
                $subHasSubmenu =
                    isset($submenuItem->submenu) && is_array($submenuItem->submenu) && count($submenuItem->submenu) > 0;
            @endphp

            @if ($canViewSubmenuItem)
                <li class="menu-item {{ $subActiveCheck ? 'active' : '' }} {{ $subHasSubmenu ? 'has-submenu' : '' }}">
                    <a href="{{ isset($submenuItem->routeName) ? route($submenuItem->routeName) : (isset($submenuItem->url) ? url($submenuItem->url) : '#') }}"
                        class="menu-link {{ $subHasSubmenu ? 'menu-toggle' : '' }}"
                        @if ($subHasSubmenu) data-bs-toggle="collapse" role="button" aria-expanded="{{ $subActiveCheck ? 'true' : 'false' }}" @endif>

                        @isset($submenuItem->icon)
                            {{-- Assuming Bootstrap icons, where $submenuItem->icon is just the name like 'envelope-paper-fill' --}}
                            <i class="menu-icon bi bi-{{ $submenuItem->icon }}"></i>
                        @endisset

                        {{-- Robust label rendering --}}
                        <div>{{ __($submenuItem->name ?? null ? $submenuItem->name : '-') }}</div>

                        @isset($submenuItem->badge)
                            <div class="badge bg-label-{{ $submenuItem->badge[0] }} rounded-pill ms-auto">
                                {{ __($submenuItem->badge[1]) }}</div>
                        @endisset
                    </a>

                    {{-- Recursive include for nested submenus --}}
                    @if ($subHasSubmenu)
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $submenuItem->submenu, // Pass the nested submenu array
                            'role' => $role, // Pass the current user's role
                            'configData' => $configData, // Pass configData
                            'currentRouteName' => $currentRouteName, // Pass current route name
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
