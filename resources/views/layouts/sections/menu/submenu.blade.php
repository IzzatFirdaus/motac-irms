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
                if ($role === 'Admin') { // $role is the current user's role passed from the parent
                    $canViewSubmenuItem = true;
                } elseif (isset($submenuItem->role)) {
                    $canViewSubmenuItem = in_array($role, (array) $submenuItem->role);
                } else {
                    // Default visibility for submenu items without a specific role: visible if user is authenticated
                    $canViewSubmenuItem = Auth::check();
                }

                // Active state determination
                $subActiveCheck = false;
                if (isset($currentRouteName) && isset($submenuItem->routeName) && $currentRouteName === $submenuItem->routeName) {
                    $subActiveCheck = true;
                } elseif (isset($currentRouteName) && isset($submenuItem->routeNamePrefix) && str_starts_with($currentRouteName, $submenuItem->routeNamePrefix)) {
                    $subActiveCheck = true;
                } elseif (!empty($submenuItem->submenu)) {
                    // Recursive check for active state in nested submenus can be added here if needed
                    // For simplicity, this example doesn't go deeper for the 'open' state beyond direct children's prefix.
                    // A more complex helper function might be needed for deeply nested active states.
                    foreach($submenuItem->submenu as $nestedSub) {
                        $nestedSub = (object) $nestedSub;
                        if (isset($currentRouteName) && (($currentRouteName === ($nestedSub->routeName ?? null)) || (isset($nestedSub->routeNamePrefix) && str_starts_with($currentRouteName, $nestedSub->routeNamePrefix)))) {
                            $subActiveCheck = true; break;
                        }
                    }
                }

                $subHasSubmenu = isset($submenuItem->submenu) && is_array($submenuItem->submenu) && count($submenuItem->submenu) > 0;

                // Determine classes for active state and submenu toggling
                // $configData should be available; check for 'myLayout' or default to 'vertical'
                $layoutTypeForSubmenu = $configData['myLayout'] ?? ($configData['layout'] ?? 'vertical'); // Check both keys or default
                $activeOpenClassForSubmenu = $layoutTypeForSubmenu === 'vertical' ? 'active open' : 'active';
                $subMenuItemClass = $subActiveCheck ? ($subHasSubmenu ? $activeOpenClassForSubmenu : 'active') : '';

                // Determine href for the link
                $subHref = $submenuItem->url ?? (isset($submenuItem->routeName) && Route::has((string)$submenuItem->routeName) ? route((string)$submenuItem->routeName) : 'javascript:void(0);');
            @endphp

            @if ($canViewSubmenuItem)
                <li class="menu-item {{ $subMenuItemClass }}" role="none">
                    <a href="{{ $subHref }}"
                        class="{{ $subHasSubmenu ? 'menu-link menu-toggle' : 'menu-link' }}"
                        role="menuitem"
                        @if (isset($submenuItem->target) && !empty($submenuItem->target)) target="{{ $submenuItem->target }}" rel="noopener noreferrer" @endif
                        @if ($subHasSubmenu) aria-haspopup="true" aria-expanded="{{ $subActiveCheck ? 'true' : 'false' }}" @endif >

                        @isset($submenuItem->icon)
                            {{-- Assuming Bootstrap icons, where $submenuItem->icon is just the name like 'envelope-paper-fill' --}}
                            <i class="menu-icon bi bi-{{ $submenuItem->icon }}"></i>
                        @endisset

                        {{-- Robust label rendering --}}
                        <div>{{ __(($submenuItem->name ?? null) ? $submenuItem->name : '-') }}</div>

                        @isset($submenuItem->badge)
                            <div class="badge bg-label-{{ $submenuItem->badge[0] }} rounded-pill ms-auto">
                                {{ __($submenuItem->badge[1]) }}</div>
                        @endisset
                    </a>

                    {{-- Recursive include for nested submenus --}}
                    @if ($subHasSubmenu)
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $submenuItem->submenu, // Pass the nested submenu array
                            'role' => $role,                 // Pass the current user's role
                            'configData' => $configData,     // Pass configData
                            'currentRouteName' => $currentRouteName // Pass current route name
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
