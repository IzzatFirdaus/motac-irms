{{--
    Canonical recursive partial for main menu and submenus.
    Handles menu headers, items, and submenus.
    All role/permission and guest logic is handled in the Livewire component before reaching this view.
    Only visible/allowed menu items are rendered here.

    NOTE: All classes and structure are scoped for .motac-vertical-menu for style encapsulation.
--}}

@foreach ($menuItems as $menu)
    @php
        $menu = (object) $menu;
        $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0;
        $isActive = false;

        // Mark item active if current route matches, or prefix matches
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

        // Determine the menu link (href)
        $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
        if ($hasSubmenu) {
            $menuHref = '#';
        }
    @endphp

    {{-- Menu Header (section label) --}}
    @if (isset($menu->menuHeader))
        <li class="menu-header small text-uppercase text-muted fw-bold">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
    @else
        {{-- Menu Item --}}
        <li class="menu-item{{ $isActive ? ' active' : '' }}{{ $hasSubmenu ? ' has-submenu' : '' }}">
            <a href="{{ $menuHref }}"
                class="menu-link{{ $hasSubmenu ? ' menu-toggle' : '' }}"
                @if ($hasSubmenu) aria-haspopup="true" aria-expanded="{{ $isActive ? 'true' : 'false' }}" tabindex="0" @endif>
                @isset($menu->icon)
                    <i class="menu-icon bi bi-{{ $menu->icon }}" aria-hidden="true"></i>
                @endisset
                <div>{{ __($menu->name ?? '-') }}</div>
                @if ($hasSubmenu)
                    <span class="menu-arrow bi bi-chevron-right" aria-hidden="true"></span>
                @endif
            </a>
            {{-- Recursive rendering for submenus --}}
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
@endforeach
