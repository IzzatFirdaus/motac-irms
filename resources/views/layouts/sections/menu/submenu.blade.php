{{-- resources/views/layouts/sections/menu/submenu.blade.php --}}
{{-- Expected variables: $menu (array of submenu items), $configData, $currentUserRole --}}

@php
    $currentRouteName = Route::currentRouteName();
    $layoutType = $configData['layout'] ?? 'vertical';
    $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';
@endphp

<ul class="menu-sub">
    @if (isset($menu) && is_array($menu))
        @foreach ($menu as $submenuItem)
            @php
                $canViewSubmenu = false;
                if ($currentUserRole === 'Admin') {
                    $canViewSubmenu = true;
                } elseif (isset($submenuItem->role)) {
                    $rolesArray = is_array($submenuItem->role) ? $submenuItem->role : [$submenuItem->role];
                    $canViewSubmenu = in_array($currentUserRole, $rolesArray);
                } else {
                    $canViewSubmenu = true;
                }

                $hasFurtherSubmenu = isset($submenuItem->submenu) && !empty($submenuItem->submenu);
                $activeClass = '';

                if ($canViewSubmenu) { // Only determine active state if user can view the menu item
                    $isDirectlyActive = \App\Helpers\Helpers::isMenuItemDirectlyActive($submenuItem, $currentRouteName);
                    $isBranchActive = \App\Helpers\Helpers::isMenuBranchActive($submenuItem, $currentRouteName, $currentUserRole);

                    if ($isBranchActive) { // Use isMenuBranchActive to determine if the branch (item or its children) is active
                        $activeClass = $hasFurtherSubmenu ? $activeOpenClass : 'active';
                    } elseif ($isDirectlyActive) { // Fallback for items without children but are directly active
                         $activeClass = 'active';
                    }
                }

                $submenuLinkClass = $hasFurtherSubmenu ? 'menu-link menu-toggle' : 'menu-link';
                $submenuHref =
                    $submenuItem->url ??
                    (isset($submenuItem->routeName) && Route::has($submenuItem->routeName)
                        ? route($submenuItem->routeName)
                        : 'javascript:void(0);');
            @endphp

            @if ($canViewSubmenu)
                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ $submenuHref }}" class="{{ $submenuLinkClass }}"
                        @if (!empty($submenuItem->target)) target="{{ $submenuItem->target }}" @endif>
                        @isset($submenuItem->icon)
                            <i class="menu-icon tf-icons {{ $submenuItem->icon }}"></i>
                        @endisset
                        <div class="menu-item-label">{{ __($submenuItem->name ?? '') }}</div>
                        @isset($submenuItem->badge)
                            <div class="badge bg-label-{{ $submenuItem->badge[0] }} rounded-pill ms-auto">
                                {{ __($submenuItem->badge[1]) }}</div>
                        @endisset
                    </a>

                    @if ($hasFurtherSubmenu)
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $submenuItem->submenu,
                            'configData' => $configData,
                            'currentUserRole' => $currentUserRole,
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
