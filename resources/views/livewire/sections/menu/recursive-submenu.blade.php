{{-- resources/views/livewire/sections/menu/recursive-submenu.blade.php --}}
@php
    if (!function_exists('isMotacMenuItemActiveRecursiveCheckView')) {
        function isMotacMenuItemActiveRecursiveCheckView(
            $itemToCheck,
            $currentRouteNameToCheck,
            &$isAnyChildActiveGlobalScopeViewLocal,
            $currentRoleViewLocal,
        ) {
            $canViewThisItemLocal = true;
            if (isset($itemToCheck->role)) {
                $canViewThisItemLocal =
                    $currentRoleViewLocal === 'Admin' || in_array($currentRoleViewLocal, (array) $itemToCheck->role);
            }
            if (!$canViewThisItemLocal) {
                $isAnyChildActiveGlobalScopeViewLocal = false;
                return false;
            }

            $isDirectlyActiveCurrentLocal =
                (isset($currentRouteNameToCheck) &&
                    $currentRouteNameToCheck === ($itemToCheck->routeName ?? ($itemToCheck->slug ?? null))) ||
                (isset($itemToCheck->routeNamePrefix) &&
                    isset($currentRouteNameToCheck) &&
                    str_starts_with($currentRouteNameToCheck, $itemToCheck->routeNamePrefix));

            if ($isDirectlyActiveCurrentLocal) {
                $isAnyChildActiveGlobalScopeViewLocal = true;
                return true;
            }
            if (isset($itemToCheck->submenu) && !empty($itemToCheck->submenu)) {
                foreach ($itemToCheck->submenu as $subItmLocal) {
                    if (
                        isMotacMenuItemActiveRecursiveCheckView(
                            $subItmLocal,
                            $currentRouteNameToCheck,
                            $isAnyChildActiveGlobalScopeViewLocal,
                            $currentRoleViewLocal,
                        )
                    ) {
                        return true;
                    }
                }
            }
            return false;
        }
    }
@endphp

<ul class="menu-sub collapse {{ $isParentBranchActive ? 'show' : '' }}" id="{{ $parentSubmenuId }}" role="menu">
    @if (!empty($submenuItems) && is_array($submenuItems))
        @foreach ($submenuItems as $subKey => $item)
            @php
                $isAnyChildActiveLocalRecursive = false;
                $canViewItemLocalRecursive = isMotacMenuItemActiveRecursiveCheckView(
                    $item,
                    $currentRouteName,
                    $isAnyChildActiveLocalRecursive,
                    $currentRole,
                );
                $hasNestedSubmenuViewRecursive = isset($item->submenu) && !empty($item->submenu);
                $isThisSubItemBranchActiveViewRecursive = $isAnyChildActiveLocalRecursive;
                // Ensure $activeOpenClass is passed or has a default
                $activeOpenClassToUse = $activeOpenClass ?? 'active open';
                $menuItemClassViewRecursive = $isThisSubItemBranchActiveViewRecursive ? $activeOpenClassToUse : '';
                $currentLevelRecursive = $level ?? 0;
                $nestedSubmenuIdViewRecursive = $hasNestedSubmenuViewRecursive
                    ? $parentSubmenuId . '-' . $currentLevelRecursive . '-' . $loop->index
                    : null;
            @endphp

            @if ($canViewItemLocalRecursive)
                <li class="menu-item {{ $menuItemClassViewRecursive }}" role="none">
                    <a href="{{ $hasNestedSubmenuViewRecursive ? 'javascript:void(0);' : (isset($item->routeName) && Route::has($item->routeName) ? route($item->routeName) : $item->url ?? 'javascript:void(0);') }}"
                        class="menu-link {{ $hasNestedSubmenuViewRecursive ? 'menu-toggle' : '' }}"
                        @if ($hasNestedSubmenuViewRecursive) data-bs-toggle="collapse"
                            aria-expanded="{{ $isThisSubItemBranchActiveViewRecursive ? 'true' : 'false' }}"
                            data-bs-target="#{{ $nestedSubmenuIdViewRecursive }}"
                            aria-controls="{{ $nestedSubmenuIdViewRecursive }}"
                            role="button"
                        @else
                            role="menuitem" @endif
                        @if (!empty($item->target) && !$hasNestedSubmenuViewRecursive) target="{{ $item->target }}" rel="noopener noreferrer" @endif>
                        @isset($item->icon)
                            <i class="menu-icon bi bi-{{ $item->icon }}"></i>
                        @endisset
                        <div class="menu-item-label">{{ __($item->name ?? '') }}</div>
                        @isset($item->badge)
                            <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">
                                {{ __($item->badge[1]) }}</div>
                        @endisset
                    </a>

                    @if ($hasNestedSubmenuViewRecursive)
                        @include('livewire.sections.menu.recursive-submenu', [
                            'submenuItems' => $item->submenu,
                            'parentSubmenuId' => $nestedSubmenuIdViewRecursive,
                            'isParentBranchActive' => $isThisSubItemBranchActiveViewRecursive,
                            'currentRole' => $currentRole,
                            'configData' => $configData,
                            'currentRouteName' => $currentRouteName,
                            'activeOpenClass' => $activeOpenClassToUse, // Pass it down
                            'level' => $currentLevelRecursive + 1,
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
