<ul class="menu-sub">
  @if (!empty($submenuItems) && is_array($submenuItems))
    @foreach ($submenuItems as $item)
      @php
        $canView = false;
        // Role check: User's role is passed as $currentRole from the parent include
        if ($currentRole === 'Admin') {
            $canView = true;
        } elseif (isset($item->role)) {
            $roles = is_array($item->role) ? $item->role : [$item->role];
            $canView = in_array($currentRole, $roles);
        } elseif (isset($item->permissions) && Auth::check()) {
            // Permission check (example, actual implementation might vary)
            // $permissions = is_array($item->permissions) ? $item->permissions : [$item->permissions];
            // $canView = Auth::user()->canAny($permissions);
        } else {
            // If no specific role/permission is defined for the item, assume it's viewable
            // (or apply a default deny policy if that's your system's behavior)
            $canView = true;
        }

        $subActiveClass = '';
        $currentRouteNameFromLaravel = Route::currentRouteName(); // Renamed to avoid conflict if $currentRouteName is passed in
        $isNestedSubmenuActive = false;

        // Check if any child submenu item (any level deep) is active
        if (!empty($item->submenu)) {
          $checkNestedActive = function ($nestedItems, $currentRoute) use (&$checkNestedActive, &$isNestedSubmenuActive) {
              foreach ($nestedItems as $nestedItem) {
                  if (isset($nestedItem->routeName) && $currentRoute === $nestedItem->routeName) {
                      $isNestedSubmenuActive = true;
                      return true;
                  }
                  if (isset($nestedItem->routeNamePrefix) && str_starts_with($currentRoute, $nestedItem->routeNamePrefix)) {
                      $isNestedSubmenuActive = true;
                      return true;
                  }
                  if (!empty($nestedItem->submenu)) {
                      if ($checkNestedActive($nestedItem->submenu, $currentRoute)) {
                          $isNestedSubmenuActive = true; // Ensure parent is marked active
                          return true;
                      }
                  }
              }
              return false;
          };
          $checkNestedActive($item->submenu, $currentRouteNameFromLaravel);
        }

        // Determine active class for the current submenu item
        if (isset($item->routeName) && $currentRouteNameFromLaravel === $item->routeName) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif ($isNestedSubmenuActive) { // A child submenu item is active
            $subActiveClass = 'active open';
        } elseif (isset($item->routeNamePrefix) && str_starts_with($currentRouteNameFromLaravel, $item->routeNamePrefix)) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif (isset($item->slug) && str_starts_with($currentRouteNameFromLaravel, $item->slug) && !isset($item->routeName) && !isset($item->routeNamePrefix)) {
            // Fallback to slug for active state if routeName/routeNamePrefix not set
            if ($isNestedSubmenuActive) $subActiveClass = 'active open';
            else $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        }
      @endphp

      @if ($canView)
        <li class="menu-item {{ $subActiveClass }}">
          <a href="{{ isset($item->routeName) && Route::has($item->routeName) ? route($item->routeName) : (isset($item->url) ? url($item->url) : 'javascript:void(0);') }}"
             class="{{ !empty($item->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (!empty($item->target)) target="{{ $item->target }}" @endif>
            @isset($item->icon)
              <i class="{{ $item->icon }}"></i>
            @endisset
            <div>{{ __($item->name ?? '') }}</div>
            @isset($item->badge)
              <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">{{ __($item->badge[1]) }}</div>
            @endisset
          </a>

          {{-- Recursive call for deeper submenus --}}
          @if (!empty($item->submenu))
            @include('livewire.sections.menu.recursive-submenu', [
              'submenuItems' => $item->submenu,
              'currentRole' => $currentRole, // Pass the role down
              'configData' => $configData    // Pass configData down
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
