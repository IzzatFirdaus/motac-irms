{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
{{--
  Vertical Sidebar Menu for MOTAC Integrated Resource Management System.
  Design Language References:
  - 1.1 Professionalism: Clean, MOTAC branding.
  - 1.2 User-Centricity: Bahasa Melayu First, clear navigation.
  - 2.1 Color Palette: Uses Surface (var(--motac-surface)) for background, Primary (var(--motac-primary)) for active states.
  - 2.2 Typography: Noto Sans, defined scales (via theme CSS).
  - 2.4 Iconography: Bootstrap Icons (bi-*) specified in Design Doc.
  - 3.1 Navigation: Vertical Side Navigation with MOTAC branding.
  - 6.1 Accessibility: aria-label, keyboard navigable (theme dependent).
--}}
<div>
    @php
        $currentRouteName = Route::currentRouteName();
        $layoutType = $configData['layout'] ?? 'vertical';
        $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';
        $currentUserRole = $role; // Passed from Livewire component

        if (!function_exists('isMotacMenuItemActiveRecursiveCheck')) {
            function isMotacMenuItemActiveRecursiveCheck(
                $item,
                $currentRouteName,
                &$isAnyChildActiveGlobalScope,
                $userRole,
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
                    (isset($item->slug) && $item->slug === $currentRouteName);

                $isAnySubmenuChildActive = false;
                if (!empty($item->submenu)) {
                    foreach ($item->submenu as $subItem) {
                        if (
                            isMotacMenuItemActiveRecursiveCheck(
                                $subItem,
                                $currentRouteName,
                                $isAnySubmenuChildActive,
                                $userRole,
                            )
                        ) {
                            $isAnySubmenuChildActive = true;
                            break;
                        }
                    }
                }

                if ($isItemDirectlyActive || $isAnySubmenuChildActive) {
                    $isAnyChildActiveGlobalScope = true;
                    return true;
                }
                return false;
            }
        }
    @endphp

    {{-- Ensure .bg-menu-theme is styled with var(--motac-surface) or appropriate MOTAC theme background --}}
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
        aria-label="{{ __('Navigasi Menu Utama Sistem') }}">
        @if (!isset($navbarFull))
            <div class="app-brand demo"> {{-- Ensure '.app-brand.demo' styling aligns with MOTAC Design Language --}}
                <a href="{{ url('/') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        {{-- Design Doc 3.1 & 7.1: Official MOTAC SVG logo, height ~32-40px --}}
                        <img src="{{ asset($configData['templateLogoSvg'] ?? 'assets/img/logo/motac-logo.svg') }}"
                            alt="{{ __('Logo Sistem Pengurusan Sumber Bersepadu MOTAC') }}" height="32">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2">
                        {{ __(config('app.name', 'Sistem MOTAC')) }}
                    </span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto"
                    aria-label="{{ __('Tutup/Buka Menu') }}">
                    {{-- Iconography: Design Language 2.4. Replacing ti-* with bi-* --}}
                    <i class="bi bi-x fs-3 d-none d-xl-block align-middle"></i> {{-- Icon for desktop toggle (e.g., close/pin) --}}
                    <i class="bi bi-x fs-3 d-block d-xl-none align-middle"></i> {{-- Icon for mobile close --}}
                </a>
            </div>
        @endif

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" role="menubar">
            @if (isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
                @forelse ($menuData->menu as $menu)
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
                                $isCurrentBranchActive = false;
                                isMotacMenuItemActiveRecursiveCheck(
                                    $menu,
                                    $currentRouteName,
                                    $isCurrentBranchActive,
                                    $currentUserRole,
                                );

                                $menuItemActiveClass = $isCurrentBranchActive
                                    ? (isset($menu->submenu) && !empty($menu->submenu)
                                        ? $activeOpenClass
                                        : 'active')
                                    : '';

                                $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
                                $menuLinkClasses = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
                                // Ensure generated href is valid or 'javascript:void(0);'
                                $menuTargetHref = 'javascript:void(0);';
                                if (isset($menu->url)) {
                                    $menuTargetHref = url($menu->url);
                                } elseif (isset($menu->routeName) && Route::has($menu->routeName)) {
                                    $menuTargetHref = route($menu->routeName);
                                } elseif (isset($menu->slug) && Route::has($menu->slug)) {
                                    // Assuming slug can be a route name
                                    $menuTargetHref = route($menu->slug);
                                }
                            @endphp

                            <li class="menu-item {{ $menuItemActiveClass }}" role="none">
                                <a href="{{ $menuTargetHref }}" class="{{ $menuLinkClasses }}"
                                    @if (!empty($menu->target)) target="{{ $menu->target }}" rel="noopener noreferrer" @endif
                                    role="menuitem"
                                    @if ($hasSubmenu) aria-haspopup="true" aria-expanded="{{ $isCurrentBranchActive ? 'true' : 'false' }}" @endif>
                                    @isset($menu->icon)
                                        {{-- Iconography: Design Language 2.4. Menu icons should be bi-* --}}
                                        {{-- The $menu->icon should provide the full Bootstrap Icon class e.g., "bi bi-house-door" --}}
                                        <i class="menu-icon {{ $menu->icon }}"></i>
                                    @endisset
                                    <div class="menu-item-label">{{ __($menu->name ?? 'Item Menu') }}</div>
                                    @isset($menu->badge)
                                        {{-- Ensure .bg-label-* classes are MOTAC themed (Design Language 2.1) --}}
                                        <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                            {{ __($menu->badge[1]) }}
                                        </div>
                                    @endisset
                                </a>

                                @if ($hasSubmenu)
                                    {{-- Passes necessary context to submenu partial --}}
                                    {{-- Ensure this 'layouts.sections.menu.submenu' points to the correct recursive partial
                       which should be 'livewire.sections.menu.recursive-submenu' based on your files. --}}
                                    @include('livewire.sections.menu.recursive-submenu', [
                                        // Corrected include path
                                        'submenuItems' => $menu->submenu,
                                        'currentRole' => $currentUserRole,
                                        'configData' => $configData,
                                        'currentRouteName' => $currentRouteName, // Pass current route for active state in submenu
                                        'activeOpenClass' => $activeOpenClass,
                                    ])
                                @endif
                            </li>
                        @endif
                    @endif
                @empty
                    <li class="menu-item" role="none">
                        <a href="javascript:void(0);" class="menu-link" role="menuitem">
                            {{-- Iconography: Design Language 2.4 --}}
                            <i class="menu-icon bi bi-exclamation-circle-fill"></i>
                            <div class="menu-item-label">{{ __('Tiada item menu untuk dipaparkan.') }}</div>
                        </a>
                    </li>
                @endforelse
            @else
                <li class="menu-item" role="none">
                    <a href="javascript:void(0);" class="menu-link" role="menuitem">
                        {{-- Iconography: Design Language 2.4 --}}
                        <i class="menu-icon bi bi-x-octagon-fill"></i>
                        <div class="menu-item-label">{{ __('Struktur data menu tidak sah atau tiada.') }}</div>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
