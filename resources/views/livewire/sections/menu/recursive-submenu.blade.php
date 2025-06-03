{{-- resources/views/livewire/sections/menu/recursive-submenu.blade.php --}}
{{--
  Refactored to use Bootstrap Collapse for nested submenus.
  Expects $submenuItems, $parentSubmenuId, $isParentBranchActive, $currentRole, $configData, $currentRouteName, $activeOpenClass, $level
--}}

{{-- The parent <a> tag has data-bs-target pointing to $parentSubmenuId --}}
<ul class="menu-sub collapse {{ $isParentBranchActive ? 'show' : '' }}" id="{{ $parentSubmenuId }}" role="menu">
    @if (!empty($submenuItems) && is_array($submenuItems))
        @foreach ($submenuItems as $subKey => $item)
            @php
                $isAnyChildActive = false; // Reset for each submenu item
                $canView = isMotacMenuItemActiveRecursiveCheck(
                    $item,
                    $currentRouteName,
                    $isAnyChildActive,
                    $currentRole,
                );
                $hasNestedSubmenu = isset($item->submenu) && !empty($item->submenu);

                $isThisSubItemBranchActive = $isAnyChildActive; // From recursive check
                $menuItemClass = $isThisSubItemBranchActive ? $activeOpenClass : '';

                // Generate unique ID for nested submenus if they exist
                $nestedSubmenuId = $hasNestedSubmenu ? $parentSubmenuId . '-' . $level . '-' . $loop->index : null;
            @endphp

            @if ($canView)
                <li class="menu-item {{ $menuItemClass }}" role="none">
                    <a href="{{ $hasNestedSubmenu ? 'javascript:void(0);' : (isset($item->routeName) && Route::has($item->routeName) ? route($item->routeName) : $item->url ?? 'javascript:void(0);') }}"
                        class="menu-link {{ $hasNestedSubmenu ? 'menu-toggle' : '' }}"
                        @if ($hasNestedSubmenu) data-bs-toggle="collapse"
                aria-expanded="{{ $isThisSubItemBranchActive ? 'true' : 'false' }}"
                data-bs-target="#{{ $nestedSubmenuId }}"
                aria-controls="{{ $nestedSubmenuId }}"
                role="button"
             @else
                role="menuitem" @endif
                        @if (!empty($item->target) && !$hasNestedSubmenu) target="{{ $item->target }}" rel="noopener noreferrer" @endif>
                        @isset($item->icon)
                            <i class="menu-icon {{ $item->icon }}"></i>
                        @endisset
                        <div class="menu-item-label">{{ __($item->name ?? '') }}</div>
                        @isset($item->badge)
                            <div class=\"badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto\">
                                {{ __($item->badge[1]) }}</div>
                        @endisset
                    </a>

                    @if ($hasNestedSubmenu)
                        {{-- Recursive call for further nested submenus --}}
                        @include('livewire.sections.menu.recursive-submenu', [
                            'submenuItems' => $item->submenu,
                            'parentSubmenuId' => $nestedSubmenuId, // Pass the new ID for this nested ul.collapse
                            'isParentBranchActive' => $isThisSubItemBranchActive, // To set 'show' class on nested ul
                            'currentRole' => $currentRole,
                            'configData' => $configData,
                            'currentRouteName' => $currentRouteName,
                            'activeOpenClass' => $activeOpenClass,
                            'level' => ($level ?? 0) + 1,
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>
