<ul class="menu-sub">
  @if (!empty($submenuItems) && is_array($submenuItems))
    @foreach ($submenuItems as $item)
      @php
        $canView = false;
        if ($currentRole === 'Admin') {
            $canView = true;
        } elseif (isset($item->role)) {
            $roles = is_array($item->role) ? $item->role : [$item->role];
            $canView = in_array($currentRole, $roles);
        } elseif (isset($item->permissions) && Auth::check()) {
            // $canView = Auth::user()->canAny((array) $item->permissions);
        } else {
            $canView = true;
        }

        $subActiveClass = '';
        $currentRouteName = Route::currentRouteName();
        $isNestedSubmenuActive = false;

        if (!empty($item->submenu)) {
          foreach ($item->submenu as $sub) {
              if (isset($sub->routeName) && $currentRouteName === $sub->routeName) {
                  $isNestedSubmenuActive = true; break;
              }
              if (!empty($sub->submenu)) {
                  foreach ($sub->submenu as $deep) {
                      if (isset($deep->routeName) && $currentRouteName === $deep->routeName) {
                          $isNestedSubmenuActive = true; break 2;
                      }
                  }
              }
          }
        }

        if (isset($item->routeName) && $currentRouteName === $item->routeName) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif ($isNestedSubmenuActive) {
            $subActiveClass = 'active open';
        } elseif (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix)) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif (isset($item->slug) && str_starts_with($currentRouteName, $item->slug)) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
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

          @if (!empty($item->submenu))
            @include('livewire.sections.menu.recursive-submenu', [
              'submenuItems' => $item->submenu,
              'currentRole' => $currentRole,
              'configData' => $configData
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
