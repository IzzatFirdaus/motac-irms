<div>
    @php
        // Load config if not injected by parent layout
        $configData = $configData ?? \App\Helpers\Helpers::appClasses();
        $currentRouteName = Route::currentRouteName();
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Menu Navigasi">
        @if (!isset($navbarFull))
            {{-- Brand Logo & System Name --}}
            <div class="app-brand demo">
                <a href="{{ url('/') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset(config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg')) }}"
                            alt="{{ __('Logo Sistem MOTAC') }}" height="32">
                    </span>
                    <span
                        class="app-brand-text demo menu-text fw-bold ms-2">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
                </a>
                {{-- Toggle Menu Button --}}
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large ms-auto {{ ($configData['layout'] ?? 'vertical') === 'horizontal' ? 'd-none' : '' }}">
                    <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                    <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                </a>
            </div>
        @endif

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
            @forelse($menuData->menu ?? [] as $menu)
                @php
                    $canViewMenu = false;
                    $role = $this->role ?? null;

                    // Determine if user can view this menu
                    if ($role === 'Admin') {
                        $canViewMenu = true;
                    } elseif (isset($menu->role)) {
                        $menuRoles = is_array($menu->role) ? $menu->role : [$menu->role];
                        $canViewMenu = in_array($role, $menuRoles);
                    } elseif (isset($menu->permissions) && Auth::check()) {
                        $permissions = is_array($menu->permissions) ? $menu->permissions : [$menu->permissions];
                        // Example: Uncomment if using Gate
                        // $canViewMenu = Auth::user()->canAny($permissions);
                    } else {
                        $canViewMenu = true; // Default to true if no specific role/permission check on the menu item itself
                    }
                @endphp

                @if ($canViewMenu)
                    {{-- Menu Header --}}
                    @if (isset($menu->menuHeader))
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                        </li>
                    @else
                        @php
                            $activeClass = '';
                            $isSubmenuActive = false;

                            // Check if any submenu item (any level deep) is active
                            if (isset($menu->submenu)) {
                                $checkSubmenuActive = function ($submenuItems, $currentRouteName) use (
                                    &$checkSubmenuActive,
                                    &$isSubmenuActive,
                                ) {
                                    foreach ($submenuItems as $subItem) {
                                        if (isset($subItem->routeName) && $currentRouteName === $subItem->routeName) {
                                            $isSubmenuActive = true;
                                            return true;
                                        }
                                        if (
                                            isset($subItem->routeNamePrefix) &&
                                            str_starts_with($currentRouteName, $subItem->routeNamePrefix)
                                        ) {
                                            $isSubmenuActive = true;
                                            return true;
                                        }
                                        if (!empty($subItem->submenu)) {
                                            if ($checkSubmenuActive($subItem->submenu, $currentRouteName)) {
                                                $isSubmenuActive = true; // Ensure parent is marked active if deep child is active
                                                return true;
                                            }
                                        }
                                    }
                                    return false;
                                };
                                $checkSubmenuActive($menu->submenu, $currentRouteName);
                            }

                            // Determine active class for the main menu item
                            if (isset($menu->routeName) && $currentRouteName === $menu->routeName) {
                                $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
                            } elseif ($isSubmenuActive) {
                                // A submenu item is active, so this parent should be 'active open'
                                $activeClass = 'active open';
                            } elseif (
                                isset($menu->routeNamePrefix) &&
                                str_starts_with($currentRouteName, $menu->routeNamePrefix)
                            ) {
                                $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
                            } elseif (
                                isset($menu->slug) &&
                                str_starts_with($currentRouteName, $menu->slug) &&
                                !isset($menu->routeName) &&
                                !isset($menu->routeNamePrefix)
                            ) {
                                // Fallback to slug if no routeName or routeNamePrefix, and a submenu item might be active under this slug
                                // This part might need refinement based on how slugs are used for active states when routeName isn't available
    if ($isSubmenuActive) {
        $activeClass = 'active open';
    } else {
        $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
    }
}

$menuLinkClass = isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link';
$routeExists = isset($menu->routeName) && Route::has($menu->routeName);
$menuHref = $routeExists
    ? route($menu->routeName)
    : (isset($menu->url)
        ? url($menu->url)
        : 'javascript:void(0);');
                        @endphp

                        <li class="menu-item {{ $activeClass }}">
                            <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}"
                                @if (!empty($menu->target)) target="{{ $menu->target }}" @endif>
                                @isset($menu->icon)
                                    <i class="{{ $menu->icon }}"></i>
                                @endisset
                                <div class="menu-item-label">{{ __($menu->name ?? '') }}</div>
                                @isset($menu->badge)
                                    <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                        {{ $menu->badge[1] }}</div>
                                @endisset
                            </a>

                            {{-- Recursive submenu inclusion --}}
                            @if (!empty($menu->submenu))
                                @include('livewire.sections.menu.recursive-submenu', [
                                    'submenuItems' => $menu->submenu,
                                    'currentRole' => $role, // Pass current user's role
                                    'configData' => $configData, // Pass config data
                                ])
                            @endif
                        </li>
                    @endif
                @endif
            @empty
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-alert-circle"></i>
                        <div class="menu-item-label">
                            {{ __('Menu tidak dapat dimuatkan atau tiada item menu yang tersedia untuk peranan anda.') }}
                        </div>
                    </a>
                </li>
            @endforelse
        </ul>
    </aside>
</div>
