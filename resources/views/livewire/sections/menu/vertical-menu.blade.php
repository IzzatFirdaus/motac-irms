{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
  @php
      $currentRouteName = Route::currentRouteName();
      $layoutType = $configData['layout'] ?? 'vertical';
      $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';
      $currentUserRole = $role;

      if (!function_exists('isMotacMenuItemActive')) {
          function isMotacMenuItemActive($item, $currentRouteName, &$isAnyChildActiveGlobal, $role) {
              $canView = $role === 'Admin' || !isset($item->role) || in_array($role, (array)$item->role);
              if (!$canView) return false;

              $isItemActive = (
                  (isset($item->routeName) && $item->routeName === $currentRouteName) ||
                  (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix)) ||
                  (isset($item->slug) && $item->slug === $currentRouteName)
              );

              $isChildActive = false;
              if (!empty($item->submenu)) {
                  foreach ($item->submenu as $sub) {
                      if (isMotacMenuItemActive($sub, $currentRouteName, $childActive, $role)) {
                          $isChildActive = true;
                          break;
                      }
                  }
              }

              if ($isItemActive || $isChildActive) {
                  $isAnyChildActiveGlobal = true;
                  return true;
              }

              return false;
          }
      }
  @endphp

  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Menu Utama">
    @if (!isset($navbarFull))
      <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
            <img src="{{ asset($configData['appLogo'] ?? $configData['templateLogoSvg'] ?? 'assets/img/logo/motac-logo.svg') }}"
                 alt="{{ __('Logo Sistem MOTAC') }}" height="32">
          </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2">
            {{ __($configData['templateName'] ?? 'Sistem MOTAC') }}
          </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
          <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
          <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
      </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      @if(isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
        @forelse ($menuData->menu as $menu)
          @php
              $canViewMenu = $currentUserRole === 'Admin' || !isset($menu->role) || in_array($currentUserRole, (array)$menu->role);
          @endphp

          @if ($canViewMenu)
            @if (isset($menu->menuHeader))
              <li class="menu-header small text-uppercase text-muted fw-semibold">
                <span class="menu-header-text">{{ __($menu->menuHeader ?? $menu->name ?? 'Tajuk Menu') }}</span>
              </li>
            @else
              @php
                  $isActiveBranch = false;
                  isMotacMenuItemActive($menu, $currentRouteName, $isActiveBranch, $currentUserRole);

                  $activeClass = $isActiveBranch
                      ? (isset($menu->submenu) ? $activeOpenClass : 'active')
                      : '';

                  $hasSubmenu = !empty($menu->submenu);
                  $menuLinkClass = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
                  $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
              @endphp

              <li class="menu-item {{ $activeClass }}">
                <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}" @if (!empty($menu->target)) target="{{ $menu->target }}" @endif>
                  @isset($menu->icon)
                    <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                  @endisset
                  <div class="menu-item-label">{{ __($menu->name ?? '') }}</div>
                  @isset($menu->badge)
                    <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                      {{ __($menu->badge[1]) }}
                    </div>
                  @endisset
                </a>

                @if ($hasSubmenu)
                  @include('layouts.sections.menu.submenu', [
                      'menu' => $menu->submenu,
                      'configData' => $configData,
                      'currentUserRole' => $currentUserRole
                  ])
                @endif
              </li>
            @endif
          @endif
        @empty
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link">
              <i class="menu-icon tf-icons ti ti-alert-circle"></i>
              <div class="menu-item-label">{{ __('Tiada item menu untuk dipaparkan.') }}</div>
            </a>
          </li>
        @endforelse
      @else
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <i class="menu-icon tf-icons ti ti-error-404"></i>
            <div class="menu-item-label">{{ __('Struktur data menu tidak sah atau kosong.') }}</div>
          </a>
        </li>
      @endif
    </ul>
  </aside>
</div>
