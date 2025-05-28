{{-- resources/views/livewire/sections/menu/recursive-submenu.blade.php --}}
<ul class="menu-sub"> @if (isset($submenuItems) && is_array($submenuItems))
    @foreach ($submenuItems as $item)
      @php
        $canViewSubmenuItem = false;
        if (isset($currentRole) && $currentRole === 'Admin') { // [cite: 6]
            $canViewSubmenuItem = true;
        } elseif (isset($item->role)) { // [cite: 6]
            if (is_array($item->role) && isset($currentRole) && in_array($currentRole, $item->role)) { // [cite: 6]
                $canViewSubmenuItem = true;
            } elseif (is_string($item->role) && isset($currentRole) && $currentRole === $item->role) { // [cite: 6]
                $canViewSubmenuItem = true;
            }
        } elseif (isset($item->permissions)) { // [cite: 6]
            // Example: $canViewSubmenuItem = Auth::user() && Auth::user()->canany(is_array($item->permissions) ? $item->permissions : [$item->permissions]);
        } else {
            $canViewSubmenuItem = isset($currentRole); // Show if user has a role, or true for public items [cite: 6]
        }
      @endphp

      @if ($canViewSubmenuItem)
        @php
          $subActiveClass = null;
          $currentRouteName = Route::currentRouteName(); // [cite: 6]
          $isNestedSubmenuActive = false; // [cite: 6]

          if (isset($item->submenu) && is_array($item->submenu)) { // [cite: 6]
              foreach ($item->submenu as $nestedSub) { // [cite: 6]
                  if (isset($nestedSub->routeName) && $currentRouteName === $nestedSub->routeName) { // [cite: 6]
                      $isNestedSubmenuActive = true; break;
                  }
                  if (isset($nestedSub->submenu) && is_array($nestedSub->submenu)) { // Check one level deeper
                      foreach ($nestedSub->submenu as $deepNestedSub) { // [cite: 6]
                          if (isset($deepNestedSub->routeName) && $currentRouteName === $deepNestedSub->routeName) { // [cite: 6]
                              $isNestedSubmenuActive = true; break;
                          }
                      }
                  }
                  if ($isNestedSubmenuActive) break;
              }
          }

          if (isset($item->routeName) && $currentRouteName === $item->routeName) { // [cite: 6]
            $subActiveClass = 'active'; // [cite: 6]
            if (isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0) $subActiveClass .= ' open'; // [cite: 6]
          } elseif ($isNestedSubmenuActive && isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0) { // [cite: 6]
            $subActiveClass = 'active open'; // [cite: 6]
          }
          // Fallback for slug/routeNamePrefix prefix based active state
          elseif (isset($item->routeNamePrefix) && str_starts_with((string)$currentRouteName, $item->routeNamePrefix) && isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0) { // [cite: 6]
            $subActiveClass = 'active open'; // [cite: 6]
          } elseif (isset($item->slug) && str_starts_with((string)$currentRouteName, $item->slug) && isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0) { // [cite: 6]
            $subActiveClass = 'active open'; // [cite: 6]
          }

        @endphp

        <li class="menu-item {{ $subActiveClass }}"> <a href="{{ isset($item->routeName) && Route::has($item->routeName) ? route($item->routeName) : (isset($item->url) ? url($item->url) : 'javascript:void(0);') }}"
             class="{{ (isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (isset($item->target) && !empty($item->target)) target="{{ $item->target }}" @endif> @if (isset($item->icon))
              <i class="{{ $item->icon }}"></i> @endif
            <div>{{ isset($item->name) ? __($item->name) : '' }}</div> @isset($item->badge)
              <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">{{ $item->badge[1] }}</div> @endisset
          </a>

          @if(isset($item->submenu) && is_array($item->submenu) && count($item->submenu) > 0)
            @include('livewire.sections.menu.recursive-submenu', [
                'submenuItems' => $item->submenu,
                'currentRole' => $currentRole,
                'configData' => $configData,
                'parentRouteNamePrefix' => $item->routeNamePrefix ?? ($item->slug ?? '') // Pass prefix for child active state checking
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
