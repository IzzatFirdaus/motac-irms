{{-- resources/views/livewire/sections/menu/recursive-submenu.blade.php --}}
@php
    // Function to check active state and roles (as provided in your file)
    if (!function_exists('isMotacMenuItemActiveRecursiveCheckView')) {
        function isMotacMenuItemActiveRecursiveCheckView(
            $itemToCheck,
            $currentRouteNameToCheck,
            &$isAnyChildActiveGlobalScopeViewLocal,
            $currentRoleViewLocal,
        ) {
            $canViewThisItemLocal = true; // Assume viewable unless role says otherwise
            if (isset($itemToCheck->role)) {
                $roles = is_array($itemToCheck->role) ? $itemToCheck->role : [$itemToCheck->role];
                $canViewThisItemLocal =
                    $currentRoleViewLocal === 'Admin' ||
                    !empty(array_intersect($roles, (array) $currentRoleViewLocal)) ||
                    empty($roles);
                if (empty($roles) && $currentRoleViewLocal !== 'Admin' && !Auth::check()) {
                    // if roles array is empty, non-admin cannot see unless Auth check passes
                    $canViewThisItemLocal = false;
                }
                if (empty($roles) && $currentRoleViewLocal !== 'Admin' && Auth::check()) {
                    // if roles array is empty, non-admin can see if auth check passes
                    $canViewThisItemLocal = true;
                }
            }
            // If no 'role' property, assume it's viewable by authenticated users or if it's a public menu.
            // The original had no specific else, implying viewable if no role. Adding Auth::check() for non-Admin might be safer
            elseif ($currentRoleViewLocal !== 'Admin' && !Auth::check()) {
                // If no role specified, and not admin, and not authenticated, then cannot view
                // $canViewThisItemLocal = false; // This line can make unroled items invisible to guests. Adjust as needed.
            }

            if (!$canViewThisItemLocal) {
                $isAnyChildActiveGlobalScopeViewLocal = false; // if parent not viewable, children aren't effectively active in this path
            return false; // Cannot view, so not active from this item's perspective
            }

            $isDirectlyActiveCurrentLocal =
                (isset($currentRouteNameToCheck) &&
                    $currentRouteNameToCheck === ($itemToCheck->routeName ?? ($itemToCheck->slug ?? null))) ||
                (isset($itemToCheck->routeNamePrefix) &&
                    isset($currentRouteNameToCheck) &&
                    str_starts_with($currentRouteNameToCheck, $itemToCheck->routeNamePrefix));

            if ($isDirectlyActiveCurrentLocal) {
                $isAnyChildActiveGlobalScopeViewLocal = true; // Mark that an active child was found in this branch
                return true; // This item itself is active
            }

            // If not directly active, check its children
            if (isset($itemToCheck->submenu) && !empty($itemToCheck->submenu)) {
                foreach ($itemToCheck->submenu as $subItmLocal) {
                    $subItmLocal = (object) $subItmLocal; // Ensure object access
                    if (
                        isMotacMenuItemActiveRecursiveCheckView(
                            $subItmLocal,
                            $currentRouteNameToCheck,
                            $isAnyChildActiveGlobalScopeViewLocal,
                            $currentRoleViewLocal,
                        )
                    ) {
                        // If any child is active, this branch is considered active up the chain.
                        // $isAnyChildActiveGlobalScopeViewLocal will be set to true by the recursive call.
                        return true; // Found an active child, so this parent branch is active
                    }
                }
            }
            return false; // Neither this item nor its children are active
        }
    }
@endphp

<ul class="menu-sub collapse {{ $isParentBranchActive ? 'show' : '' }}" id="{{ $parentSubmenuId }}" role="menu">
    @if (!empty($submenuItems) && is_array($submenuItems))
        @foreach ($submenuItems as $subKey => $item)
            @php
                $item = (object) $item; // Ensure object access
                $isAnyChildActiveLocalRecursive = false; // Reset for each item branch

                // Determine if the current item or any of its children are active, AND if it's viewable by role
$canViewAndIsActiveBranch = isMotacMenuItemActiveRecursiveCheckView(
    $item,
    $currentRouteName,
    $isAnyChildActiveLocalRecursive, // This will be true if $item or its children are active AND viewable
    $currentRole,
);
// The function now returns true if viewable and active (itself or children)
// $isAnyChildActiveLocalRecursive is modified by reference to reflect if any child path is active

// An item should be considered for display if it's viewable by role.
                // The active check is separate. Let's refine canViewItemLocalRecursive.
$explicitCanViewItem = true;
if (isset($item->role)) {
    $roles = is_array($item->role) ? $item->role : [$item->role];
    $explicitCanViewItem =
        $currentRole === 'Admin' ||
        !empty(array_intersect($roles, (array) $currentRole)) ||
        empty($roles);
    if (empty($roles) && $currentRole !== 'Admin' && !Auth::check()) {
        $explicitCanViewItem = false;
    }
    if (empty($roles) && $currentRole !== 'Admin' && Auth::check()) {
        $explicitCanViewItem = true;
    }
} elseif ($currentRole !== 'Admin' && !Auth::check()) {
    // $explicitCanViewItem = false; // Adjust if items without roles should be hidden for guests
}

$hasNestedSubmenuViewRecursive =
    isset($item->submenu) &&
    !empty($item->submenu) &&
    is_array($item->submenu) &&
    count($item->submenu) > 0;
// $isThisSubItemBranchActiveViewRecursive should be true if this item itself is the active one OR if one of its children is active.
// $isAnyChildActiveLocalRecursive will be true if this item or any of its children are active (and viewable)
$isThisSubItemBranchActiveViewRecursive = $isAnyChildActiveLocalRecursive;

$activeOpenClassToUse = $activeOpenClass ?? 'active open'; // Default from parent or local
$menuItemClassViewRecursive = $isThisSubItemBranchActiveViewRecursive ? $activeOpenClassToUse : '';
// Ensure currentLevelRecursive is correctly incremented and passed
$currentLevelRecursive = $level ?? 0; // Default level if not passed
$nestedSubmenuIdViewRecursive = $hasNestedSubmenuViewRecursive
    ? $parentSubmenuId . '-' . $currentLevelRecursive . '-' . $loop->index
    : null;

// The href for the link
$itemHref =
    $hasNestedSubmenuViewRecursive && empty($item->url) && empty($item->routeName)
        ? 'javascript:void(0);'
        : $item->url ??
            (isset($item->routeName) && Route::has((string) $item->routeName)
                ? route((string) $item->routeName)
                : 'javascript:void(0);');
            @endphp

            @if ($explicitCanViewItem)
                {{-- Only render if the user has permission for this specific item --}}
                <li class="menu-item {{ $menuItemClassViewRecursive }}" role="none">
                    <a href="{{ $itemHref }}"
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
                        {{-- Consistent label rendering --}}
                        <div class="menu-item-label">{{ __($item->name ?? null ? $item->name : '-') }}</div>
                        @isset($item->badge)
                            <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">
                                {{ __($item->badge[1]) }}</div>
                        @endisset
                    </a>

                    @if ($hasNestedSubmenuViewRecursive)
                        @include('livewire.sections.menu.recursive-submenu', [
                            'submenuItems' => $item->submenu,
                            'parentSubmenuId' => $nestedSubmenuIdViewRecursive,
                            'isParentBranchActive' => $isThisSubItemBranchActiveViewRecursive, // If this branch is active, its direct ul should show
                            'currentRole' => $currentRole,
                            'configData' => $configData,
                            'currentRouteName' => $currentRouteName,
                            'activeOpenClass' => $activeOpenClassToUse,
                            'level' => $currentLevelRecursive + 1, // Increment level
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
