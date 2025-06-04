{{-- resources/views/layouts/sections/menu/submenu.blade.php --}}
{{-- This partial expects $menu (the array of submenu items), $role, and $configData to be passed to it --}}
<ul class="menu-sub">
    @if (isset($menu))
        @foreach ($menu as $submenu)
            {{-- Role-based access check for submenu items --}}
            @php
                $submenuRoles = isset($submenu->role)
                    ? (is_array($submenu->role)
                        ? $submenu->role
                        : [$submenu->role])
                    : [];
            @endphp
            @if (
                $role === 'Admin' ||
                    (isset($submenu->role) && !empty($submenuRoles) && in_array($role, $submenuRoles)) ||
                    !isset($submenu->role)) {{-- Show if Admin, user has role, or if submenu item has no specific roles defined (making it public within its parent context) --}}

                {{-- Active submenu item logic --}}
                @php
                    $activeClass = null;
                    $active = ($configData['layout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active'; // Parent class if child is active
                    $currentRouteName = Route::currentRouteName();

                    // Exact match for slug or routeName specified in JSON [cite: 4]
                    if ($currentRouteName === ($submenu->routeName ?? ($submenu->slug ?? null))) {
                        $activeClass = 'active';
                    }
                    // If this submenu item itself has children, check if current route name starts with its slug prefixes or routeNamePrefix
                    elseif (isset($submenu->submenu)) {
                        $matchAgainst = $submenu->routeNamePrefix ?? ($submenu->slug ?? null);
                        if ($matchAgainst) {
                            if (is_array($matchAgainst)) {
                                foreach ($matchAgainst as $slug_or_prefix) {
                                    if (str_starts_with($currentRouteName, $slug_or_prefix)) {
                                        $activeClass = $active; // 'active open' for vertical
                                        break;
                                    }
                                }
                            } else {
                                if (str_starts_with($currentRouteName, $matchAgainst)) {
                                    $activeClass = $active; // 'active open' for vertical
                                }
                            }
                        }
                    }
                @endphp

                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                        class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($submenu->target) and !empty($submenu->target)) target="{{ $submenu->target }}" @endif>
                        @if (isset($submenu->icon)) {{-- Icons are optional for submenus --}}
                            <i class="{{ $submenu->icon }}"></i>
                        @endif
                        <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                        @isset($submenu->badge)
                            <div class="badge bg-label-{{ $submenu->badge[0] }} rounded-pill ms-auto">
                                {{ $submenu->badge[1] }}</div>
                        @endisset
                    </a>

                    {{-- Recursively include submenu if it exists --}}
                    @if (isset($submenu->submenu))
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $submenu->submenu,
                            'role' => $role,
                            'configData' => $configData,
                        ])
                    @endisset
            </li>
        @endif
    @endforeach
@endif
</ul>
