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
        $canView = false;
        if ($currentRole === 'Admin') {
            $canView = true;
        } elseif (isset($item->role)) {
            $roles = is_array($item->role) ? $item->role : [$item->role];
            $canView = in_array($currentRole, $roles);
        } else {
            $canView = true; // Default to viewable if no specific role
        }

        $hasNestedSubmenu = isset($item->submenu) && !empty($item->submenu);

        // Determine if this specific item or its branch is active
        $isThisSubItemBranchActive = false;
        if (function_exists('isMotacMenuItemActiveRecursiveCheck')) { // From Source 12 context
             isMotacMenuItemActiveRecursiveCheck($item, $currentRouteName, $isThisSubItemBranchActive, $currentRole);
        }

        // Class for the <li> item (theme's way of styling active parent)
        $subItemLiActiveClass = $isThisSubItemBranchActive ? ($hasNestedSubmenu ? $activeOpenClass : 'active') : '';

        // Classes for the <a> link
        $submenuLinkClasses = 'menu-link'; // Base theme class
        // if ($hasNestedSubmenu) {
            // $submenuLinkClasses .= ' menu-toggle'; // Keep if theme styles arrow based on this
        // }

        // Generate a unique ID for the nested submenu if it exists
        $nestedSubmenuId = $hasNestedSubmenu ? ($parentSubmenuId . '-sub-' . Illuminate\Support\Str::slug($item->name ?? ('item-' . $subKey))) : null;

        // Determine href for the link
        $submenuTargetHref = 'javascript:void(0);'; // Default
        if (isset($item->url) && $item->url && $item->url !== 'javascript:void(0);' && !$hasNestedSubmenu) {
            $submenuTargetHref = url($item->url);
        } elseif (isset($item->routeName) && Route::has($item->routeName) && !$hasNestedSubmenu) {
            $submenuTargetHref = route($item->routeName);
        } elseif (isset($item->slug) && Route::has($item->slug) && !$hasNestedSubmenu && !isset($item->routeName)) {
            $submenuTargetHref = route($item->slug);
        } elseif ($hasNestedSubmenu) {
            $submenuTargetHref = '#' . $nestedSubmenuId; // For Bootstrap collapse
        }

      @endphp

      @if ($canView)
        <li class="menu-item {{ $subItemLiActiveClass }}" role="none">
          <a href="{{ $submenuTargetHref }}"
             class="{{ $submenuLinkClasses }}"
             @if ($hasNestedSubmenu)
                data-bs-toggle="collapse"
                aria-expanded="{{ $isThisSubItemBranchActive ? 'true' : 'false' }}"
                aria-controls="{{ $nestedSubmenuId }}"
                role="button"
             @else
                role="menuitem"
             @endif
             @if (!empty($item->target) && !$hasNestedSubmenu) target="{{ $item->target }}" rel="noopener noreferrer" @endif
             >
            @isset($item->icon)
              <i class="menu-icon {{ $item->icon }}"></i>
            @endisset
            <div>{{ __($item->name ?? '') }}</div>
            @isset($item->badge)
              <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">{{ __($item->badge[1]) }}</div>
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
              'level' => ($level ?? 0) + 1
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
