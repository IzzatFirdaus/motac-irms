<div>
  @php
    // Load config if not injected by parent layout
    $configData = $configData ?? \App\Helpers\Helpers::appClasses();
    $currentRouteName = Route::currentRouteName();
  @endphp

  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Menu Navigasi">
    @if (!isset($navbarFull))
      {{-- Brand Logo & System Name --}}
      <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="{{ asset(config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg')) }}" alt="{{ __('Logo Sistem MOTAC') }}" height="32">
          </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
        </a>
        {{-- Toggle Menu Button --}}
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto {{ ($configData['layout'] ?? 'vertical') === 'horizontal' ? 'd-none' : '' }}">
          <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
          <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
      </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      @forelse($menuData->menu ?? [] as $menu)
        @php
          $canViewMenu = false;
          $role = $this->role ?? null;

          // Determine if user can view this menu
          if ($role === 'Admin') {
            $canViewMenu = true;
          } elseif (isset($menu->role)) {
            $menuRoles = is_array($menu->role) ? $menu->role : [$menu->role];
            $canViewMenu = in_array($role, $menuRoles);
          } elseif (isset($menu->permissions) && Auth::check()) {
            $permissions = is_array($menu->permissions) ? $menu->permissions : [$menu->permissions];
            // Example: Uncomment if using Gate
            // $canViewMenu = Auth::user()->canAny($permissions);
          } else {
            $canViewMenu = true;
          }
        @endphp

        @if ($canViewMenu)
          {{-- Menu Header --}}
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
          @else
            @php
              $activeClass = '';
              $isSubmenuActive = false;

              if (isset($menu->submenu)) {
                foreach ($menu->submenu as $sub) {
                  if (isset($sub->routeName) && $currentRouteName === $sub->routeName) {
                    $isSubmenuActive = true;
                    break;
                  }

                  if (isset($sub->submenu)) {
                    foreach ($sub->submenu as $deepSub) {
                      if (isset($deepSub->routeName) && $currentRouteName === $deepSub->routeName) {
                        $isSubmenuActive = true;
                        break 2;
                      }
                    }
                  }
                }
              }

              if (isset($menu->routeName) && $currentRouteName === $menu->routeName) {
                $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
              } elseif ($isSubmenuActive) {
                $activeClass = 'active open';
              } elseif (isset($menu->routeNamePrefix) && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
                $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
              } elseif (isset($menu->slug) && str_starts_with($currentRouteName, $menu->slug)) {
                $activeClass = 'active' . (isset($menu->submenu) ? ' open' : '');
              }

              $menuLinkClass = isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link';
              $routeExists = isset($menu->routeName) && Route::has($menu->routeName);
              $menuHref = $routeExists ? route($menu->routeName) : (isset($menu->url) ? url($menu->url) : 'javascript:void(0);');
            @endphp

            <li class="menu-item {{ $activeClass }}">
              <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}" @if (!empty($menu->target)) target="{{ $menu->target }}" @endif>
                @isset($menu->icon)
                  <i class="{{ $menu->icon }}"></i>
                @endisset
                <div class="menu-item-label">{{ __($menu->name ?? '') }}</div>
                @isset($menu->badge)
                  <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                @endisset
              </a>

              {{-- Recursive submenu inclusion --}}
              @if (!empty($menu->submenu))
                @include('livewire.sections.menu.recursive-submenu', [
                    'submenuItems' => $menu->submenu,
                    'currentRole' => $role,
                    'configData' => $configData
                ])
              @endif
            </li>
          @endif
        @endif
      @empty
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <i class="menu-icon tf-icons ti ti-alert-circle"></i>
            <div class="menu-item-label">{{ __('Menu tidak dapat dimuatkan atau tiada item menu yang tersedia untuk peranan anda.') }}</div>
          </a>
        </li>
      @endforelse
    </ul>
  </aside>
</div>
