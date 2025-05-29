{{-- This file is expected to be included by a parent menu Blade view --}}
{{-- Expected variables: $menu (array of submenu items), $configData, $currentUserRole --}}

@php
  $currentRouteName = Route::currentRouteName();
  $layoutType = $configData['layout'] ?? 'vertical';
  $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';

  // Helper function (if not already defined globally or in a shared include)
  // to recursively check if a menu item or any of its children are active
  if (!function_exists('isSubMenuItemActive')) { // Use a different name to avoid collision if included multiple times with different contexts
      function isSubMenuItemActive($item, $currentRouteName, &$isAnyChildActive = false) {
          $isActive = false;
          if (isset($item->routeName) && $item->routeName === $currentRouteName) {
              $isActive = true;
              $isAnyChildActive = true;
          } elseif (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix)) {
              $isActive = true;
              // $isAnyChildActive = true; // Optional: if prefix match implies parent is open
          }

          if (!empty($item->submenu)) {
              $hasActiveGrandChild = false;
              foreach ($item->submenu as $subItem) {
                  if (isSubMenuItemActive($subItem, $currentRouteName, $hasActiveGrandChild)) {
                      $isActive = true;
                      $isAnyChildActive = true;
                      break;
                  }
              }
          }
          return $isActive;
      }
  }
@endphp

<ul class="menu-sub">
  @if (isset($menu) && is_array($menu))
    @foreach ($menu as $submenuItem) {{-- Renamed $submenu to $submenuItem to avoid confusion with outer scope $menu --}}
      @php
        $canViewSubmenu = false;
        // $currentUserRole is expected to be passed from the parent include
        if ($currentUserRole === 'Admin') {
            $canViewSubmenu = true;
        } elseif (isset($submenuItem->role)) {
            $roles = is_array($submenuItem->role) ? $submenuItem->role : [$submenuItem->role];
            $canViewSubmenu = in_array($currentUserRole, $roles);
        } elseif (isset($submenuItem->permissions) && Auth::check()) {
            // Uncomment if using permission system, e.g., Auth::user()->canAny((array)$submenuItem->permissions);
        } else {
            $canViewSubmenu = true; // Default for items without specific role/permission
        }

        $menuItemIsActive = false;
        $anyChildIsActive = false;

        isSubMenuItemActive($submenuItem, $currentRouteName, $anyChildIsActive);

        if (isset($submenuItem->routeName) && $submenuItem->routeName === $currentRouteName) {
            $menuItemIsActive = true;
        }
        if (!$menuItemIsActive && !$anyChildIsActive && isset($submenuItem->routeNamePrefix) && str_starts_with($currentRouteName, $submenuItem->routeNamePrefix)) {
            $menuItemIsActive = true;
        }

        $activeClass = '';
        if ($menuItemIsActive || $anyChildIsActive) {
            if (isset($submenuItem->submenu) && !empty($submenuItem->submenu)) {
                $activeClass = $activeOpenClass;
            } else {
                $activeClass = 'active';
            }
        }

        $hasFurtherSubmenu = isset($submenuItem->submenu) && !empty($submenuItem->submenu);
        $submenuLinkClass = $hasFurtherSubmenu ? 'menu-link menu-toggle' : 'menu-link';
        $submenuHref = $submenuItem->url ?? (isset($submenuItem->routeName) && Route::has($submenuItem->routeName) ? route($submenuItem->routeName) : 'javascript:void(0);');
      @endphp

      @if ($canViewSubmenu)
        <li class="menu-item {{ $activeClass }}">
          <a href="{{ $submenuHref }}"
             class="{{ $submenuLinkClass }}"
             @if (!empty($submenuItem->target)) target="{{ $submenuItem->target }}" @endif>
            @isset($submenuItem->icon) {{-- Submenu items might not always have icons, but supported if they do --}}
              <i class="menu-icon tf-icons {{ $submenuItem->icon }}"></i>
            @endisset
            <div>{{ __($submenuItem->name ?? '') }}</div>
            @isset($submenuItem->badge)
              <div class="badge bg-label-{{ $submenuItem->badge[0] }} rounded-pill ms-auto">{{ __($submenuItem->badge[1]) }}</div>
            @endisset
          </a>

          {{-- Recursive call for the next level of submenu --}}
          @if ($hasFurtherSubmenu)
            @include('layouts.sections.menu.submenu', [
              'menu' => $submenuItem->submenu,
              'configData' => $configData,
              'currentUserRole' => $currentUserRole // Continue passing the role
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
