{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
{{--
  Vertical Sidebar Menu for MOTAC Integrated Resource Management System.
  Refactored to use Bootstrap Collapse for vertical submenus.
--}}
<div>
    @php
        $currentRouteName = Route::currentRouteName();
        // $layoutType is used for $activeOpenClass, keep as is if other parts of your theme rely on it.
        // For Bootstrap collapse, 'open' part of $activeOpenClass on the <li> might be redundant
        // if Bootstrap's 'show' on the <ul> handles the visual state.
        // However, themes often use 'open' on the <li> for styling the parent itself when expanded.
        $layoutType = $configData['layout'] ?? 'vertical';
        $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';
        $currentUserRole = $role; // Passed from Livewire component (Source 10)

        // Helper function for active states (already defined in your provided Source 12)
        if (!function_exists('isMotacMenuItemActiveRecursiveCheck')) {
            function isMotacMenuItemActiveRecursiveCheck(
                $item,
                $currentRouteName,
                &$isAnyChildActiveGlobalScope, // This will be set to true if this item or any child is active
                $userRole
            ) {
                $canViewItem =
                    $userRole === 'Admin' ||
                    !isset($item->role) ||
                    empty((array) $item->role) ||
                    in_array($userRole, (array) $item->role);
                if (!$canViewItem) {
                    return false;
                }

                $isItemDirectlyActive =
                    (isset($item->routeName) && $item->routeName === $currentRouteName) ||
                    (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix)) ||
                    (isset($item->slug) && !isset($item->routeName) && !isset($item->routeNamePrefix) && $item->slug === $currentRouteName);


                $isAnySubmenuChildActive = false;
                if (!empty($item->submenu)) {
                    foreach ($item->submenu as $subItem) {
                        if (
                            isMotacMenuItemActiveRecursiveCheck(
                                $subItem,
                                $currentRouteName,
                                $isAnySubmenuChildActive, // Pass by reference to see if any child makes this true
                                $userRole
                            )
                        ) {
                            // If a sub-item (or its descendants) is active, this branch is active
                            $isAnySubmenuChildActive = true;
                            break;
                        }
                    }
                }

                if ($isItemDirectlyActive || $isAnySubmenuChildActive) {
                    $isAnyChildActiveGlobalScope = true; // Set the output parameter
                    return true; // This item or a descendant is active
                }
                return false;
            }
        }
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
        aria-label="{{ __('Navigasi Menu Utama Sistem') }}">
        @if (!isset($navbarFull)) {{-- Assuming $navbarFull is a config from $configData or passed --}}
            <div class="app-brand demo">
                <a href="{{ url('/') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset($configData['templateLogoSvg'] ?? 'assets/img/logo/motac-logo.svg') }}"
                            alt="{{ __('Logo Sistem Pengurusan Sumber Bersepadu MOTAC') }}" height="32">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2">
                        {{ __(config('app.name', 'Sistem MOTAC')) }}
                    </span>
                </a>
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto"
                    aria-label="{{ __('Tutup/Buka Menu') }}">
                    <i class="bi bi-x fs-3 d-none d-xl-block align-middle"></i>
                    <i class="bi bi-x fs-3 d-block d-xl-none align-middle"></i>
                </a>
            </div>
        @endif

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" role="menubar">
            @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
                @forelse ($menuData->menu as $key => $menu) {{-- Added $key for unique ID generation --}}
                    @php
                        $canViewTopLevelMenu =
                            $currentUserRole === 'Admin' ||
                            !isset($menu->role) ||
                            empty((array) $menu->role) ||
                            in_array($currentUserRole, (array) $menu->role);
                    @endphp

                    @if ($canViewTopLevelMenu)
                        @if (isset($menu->menuHeader))
                            <li class="menu-header small text-uppercase" role="none">
                                <span
                                    class="menu-header-text text-muted fw-semibold">{{ __($menu->menuHeader ?? ($menu->name ?? 'Sub Sistem')) }}</span>
                            </li>
                        @else
                            @php
                                $isCurrentBranchActive = false; // This will be true if this item or any child is active
                                isMotacMenuItemActiveRecursiveCheck(
                                    $menu,
                                    $currentRouteName,
                                    $isCurrentBranchActive,
                                    $currentUserRole
                                );

                                // Class for the <li> item (theme's way of styling active parent)
                                $menuItemActiveClass = $isCurrentBranchActive
                                    ? (isset($menu->submenu) && !empty($menu->submenu)
                                        ? $activeOpenClass // e.g., 'active open'
                                        : 'active')
                                    : '';

                                $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
                                $menuLinkClasses = 'menu-link'; // Base theme class
                                if ($hasSubmenu) {
                                    // Remove 'menu-toggle' if Bootstrap handles the toggle and arrow indicator
                                    // Or keep 'menu-toggle' if it's only for styling the arrow and doesn't conflict with Bootstrap JS
                                    // For Bootstrap collapse, an arrow indicator is often added via CSS pseudo-elements on the [data-bs-toggle="collapse"]
                                    // $menuLinkClasses .= ' menu-toggle'; // Keep if needed for theme's arrow styling
                                }

                                // Generate a unique ID for the submenu if it exists
                                $submenuId = $hasSubmenu ? ('submenu-' . Illuminate\Support\Str::slug($menu->name ?? ('menu-item-' . $key))) : null;

                                // Determine href for the link
                                $menuTargetHref = 'javascript:void(0);'; // Default for items with no direct action or if submenu target
                                if (isset($menu->url) && $menu->url && $menu->url !== 'javascript:void(0);' && !$hasSubmenu) {
                                    $menuTargetHref = url($menu->url);
                                } elseif (isset($menu->routeName) && Route::has($menu->routeName) && !$hasSubmenu) {
                                    $menuTargetHref = route($menu->routeName);
                                } elseif (isset($menu->slug) && Route::has($menu->slug) && !$hasSubmenu && !isset($menu->routeName)) {
                                    $menuTargetHref = route($menu->slug);
                                } elseif ($hasSubmenu) {
                                    $menuTargetHref = '#' . $submenuId; // For Bootstrap collapse
                                }
                            @endphp

                            <li class="menu-item {{ $menuItemActiveClass }}" role="none">
                                <a href="{{ $menuTargetHref }}"
                                   class="{{ $menuLinkClasses }}"
                                   @if ($hasSubmenu)
                                       data-bs-toggle="collapse"
                                       aria-expanded="{{ $isCurrentBranchActive ? 'true' : 'false' }}"
                                       aria-controls="{{ $submenuId }}"
                                       role="button" {{-- More semantic for a collapse trigger --}}
                                   @else
                                       role="menuitem"
                                   @endif
                                   @if (isset($menu->target) && !$hasSubmenu) target="{{ $menu->target }}" rel="noopener noreferrer" @endif
                                   >
                                    @isset($menu->icon)
                                        <i class="menu-icon {{ $menu->icon }}"></i> {{-- Use theme's icon classes --}}
                                    @endisset
                                    <div class="menu-item-label">{{ __($menu->name ?? 'Item Menu') }}</div>
                                    @isset($menu->badge)
                                        <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                            {{ __($menu->badge[1]) }}
                                        </div>
                                    @endisset
                                </a>

                                @if ($hasSubmenu)
                                    {{-- Pass $submenuId to the recursive partial --}}
                                    @include('livewire.sections.menu.recursive-submenu', [
                                        'submenuItems' => $menu->submenu,
                                        'parentSubmenuId' => $submenuId, // Pass the ID for the ul.collapse
                                        'isParentBranchActive' => $isCurrentBranchActive, // To set 'show' class
                                        'currentRole' => $currentUserRole,
                                        'configData' => $configData,
                                        'currentRouteName' => $currentRouteName,
                                        'activeOpenClass' => $activeOpenClass,
                                        'level' => 1 // For generating unique IDs in nested submenus
                                    ])
                                @endif
                            </li>
                        @endif
                    @endif
                @empty
                    <li class="menu-item" role="none">
                        <a href="javascript:void(0);" class="menu-link" role="menuitem">
                            <i class="menu-icon bi bi-exclamation-circle-fill"></i>
                            <div class="menu-item-label">{{ __('Tiada item menu untuk dipaparkan.') }}</div>
                        </a>
                    </li>
                @endforelse
            @else
                <li class="menu-item" role="none">
                    <a href="javascript:void(0);" class="menu-link" role="menuitem">
                        <i class="menu-icon bi bi-x-octagon-fill"></i>
                        <div class="menu-item-label">{{ __('Struktur data menu tidak sah atau tiada.') }}</div>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
