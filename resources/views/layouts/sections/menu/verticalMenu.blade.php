{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}
{{-- MOTAC ICT LOAN SYSTEM | Vertical Sidebar Navigation --}}
{{-- Expected: $configData (from Helpers::appClasses()), $menuData (from config or service provider) --}}

@php
  $currentUserRole = Auth::check() ? Auth::user()->getRoleNames()->first() : null;
  $currentRouteName = Route::currentRouteName();
  $layoutType = $configData['layout'] ?? 'vertical';
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">
  @if (!($navbarFull ?? false))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">
          <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo Aplikasi') }}" height="32">
        </span>
        <span class="app-brand-text demo menu-text fw-bold ms-2">
          {{ __($configData['templateName'] ?? config('variables.templateName', 'Sistem MOTAC')) }}
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
    @if(isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu) && count($menuData->menu) > 0)
      @foreach ($menuData->menu as $menu)
        @php
          // Determine access rights
          $canViewMenu = false;

          if ($currentUserRole === 'Admin') {
              $canViewMenu = true;
          } elseif (isset($menu->role)) {
              $menuRoles = is_array($menu->role) ? $menu->role : [$menu->role];
              $canViewMenu = in_array($currentUserRole, $menuRoles);
          } elseif (isset($menu->permissions)) {
              // Optional: implement canAny() logic if needed
          } else {
              $canViewMenu = true;
          }
        @endphp

        @if ($canViewMenu)
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ __($menu->menuHeader ?? $menu->name) }}</span>
            </li>
          @else
            @php
              // Determine active state
              $activeClass = '';
              $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';

              if (isset($menu->routeNamePrefix) && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
                  $activeClass = $activeOpenClass;
              } elseif (isset($menu->slug) && $currentRouteName === $menu->slug) {
                  $activeClass = 'active';
              } elseif (isset($menu->routeName) && $currentRouteName === $menu->routeName && empty($menu->submenu)) {
                  $activeClass = 'active';
              } elseif (isset($menu->submenu) && isset($menu->slug)) {
                  $slugsToCheck = is_array($menu->slug) ? $menu->slug : [$menu->slug];
                  foreach ($slugsToCheck as $slugItem) {
                      if (str_starts_with($currentRouteName, $slugItem)) {
                          $activeClass = $activeOpenClass;
                          break;
                      }
                  }
              }

              $hasSubmenu = isset($menu->submenu);
              $menuLinkClass = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
              $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
            @endphp

            <li class="menu-item {{ $activeClass }}">
              <a href="{{ $menuHref }}"
                 class="{{ $menuLinkClass }}"
                 @if (!empty($menu->target)) target="{{ $menu->target }}" @endif>
                @isset($menu->icon)
                  <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                @endisset
                <div>{{ __($menu->name ?? '') }}</div>
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
                    'role' => $currentUserRole
                ])
              @endif
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link">
          <i class="menu-icon tf-icons ti ti-error-404"></i>
          <div>{{ __('Menu data tidak tersedia.') }}</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
