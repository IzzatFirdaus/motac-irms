<ul class="menu-sub">
  @if (isset($menu) && is_array($menu))
    @foreach ($menu as $submenu)
      @php
        $canViewSubmenu = false;
        $currentUserRole = $role ?? (Auth::check() ? Auth::user()->getRoleNames()->first() : null);

        if ($currentUserRole === 'Admin') {
            $canViewSubmenu = true;
        } elseif (isset($submenu->role)) {
            $roles = is_array($submenu->role) ? $submenu->role : [$submenu->role];
            $canViewSubmenu = in_array($currentUserRole, $roles);
        } elseif (isset($submenu->permissions) && Auth::check()) {
            // Uncomment if using permission system
            // $canViewSubmenu = Auth::user()->canAny((array) $submenu->permissions);
        } else {
            $canViewSubmenu = true;
        }

        $activeClass = '';
        $currentRouteName = Route::currentRouteName();
        $layoutType = $configData['layout'] ?? 'vertical';
        $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';

        if (isset($submenu->routeName) && $currentRouteName === $submenu->routeName) {
            $activeClass = 'active';
        } elseif (isset($submenu->submenu)) {
            if (isset($submenu->routeNamePrefix) && str_starts_with($currentRouteName, $submenu->routeNamePrefix)) {
                $activeClass = $activeOpenClass;
            } elseif (isset($submenu->slug)) {
                foreach ((array)$submenu->slug as $slug) {
                    if (str_starts_with($currentRouteName, $slug)) {
                        $activeClass = $activeOpenClass;
                        break;
                    }
                }
            }
        }
      @endphp

      @if ($canViewSubmenu)
        <li class="menu-item {{ $activeClass }}">
          <a href="{{ isset($submenu->routeName) && Route::has($submenu->routeName) ? route($submenu->routeName) : (isset($submenu->url) ? url($submenu->url) : 'javascript:void(0);') }}"
             class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (!empty($submenu->target)) target="{{ $submenu->target }}" @endif>
            @isset($submenu->icon)
              <i class="menu-icon tf-icons {{ $submenu->icon }}"></i>
            @endisset
            <div>{{ __($submenu->name ?? '') }}</div>
            @isset($submenu->badge)
              <div class="badge bg-label-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ __($submenu->badge[1]) }}</div>
            @endisset
          </a>

          @if (isset($submenu->submenu))
            @include('layouts.sections.menu.submenu', [
              'menu' => $submenu->submenu,
              'configData' => $configData,
              'role' => $currentUserRole
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
