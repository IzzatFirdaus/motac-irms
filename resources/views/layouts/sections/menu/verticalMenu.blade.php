{{-- verticalMenu.blade.php --}}
@php
  $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  @if(!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        {{-- Design Document: MOTAC Logo and Jata Negara
             Update: public/assets/img/logo/motac_logo_sidebar.png (can be a different version for sidebar)
                     and public/assets/img/logo/jata_negara_sidebar.png
        --}}
        <img src="{{ asset('assets/img/logo/motac_logo_sidebar.png') }}" alt="MOTAC Logo" height="20">
        {{-- Optionally, include Jata Negara
        <img src="{{ asset('assets/img/logo/jata_negara_sidebar.png') }}" alt="Jata Negara" height="20" class="ms-2">
        --}}
      </span>
      {{-- Design Document: System Title "Sistem Pengurusan BPM MOTAC" --}}
      <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name', 'Sistem Pengurusan BPM MOTAC') }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  {{-- Design Document: Logically structured menus, primarily in Bahasa Melayu.
       Menu items ($menuData->menu) should come from a source (e.g., verticalMenu.json)
       where 'name' and 'menuHeader' are translation keys. Example: "dashboard.title"
  --}}
  <ul class="menu-inner py-1">
    @foreach ($menuData->menu as $menu) {{-- $menuData is usually passed from a Service Provider or the Livewire component --}}

    {{-- adding active and open class if child is active --}}

    {{-- menu headers --}}
    @if (isset($menu->menuHeader))
      <li class="menu-header small text-uppercase">
        {{-- Ensure $menu->menuHeader is a translation key if it needs to be dynamic --}}
        <span class="menu-header-text">{{ __( $menu->menuHeader ) }}</span>
      </li>
    @else

      {{-- active menu method --}}
      @php
        $activeClass = null;
        $currentRouteName = Route::currentRouteName();

        if ($currentRouteName === $menu->slug) {
        $activeClass = 'active';
        }
        elseif (isset($menu->submenu)) {
          if (gettype($menu->slug) === 'array') {
          foreach($menu->slug as $slug){
            if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
            $activeClass = 'active open';
            }
            }
          }
          else{
            if (str_contains($currentRouteName,$menu->slug) and strpos($currentRouteName,$menu->slug) === 0) {
            $activeClass = 'active open';
            }
          }
        }
      @endphp

      {{-- main menu --}}
      {{-- Design Document: Role-specific interfaces.
           The following condition handles showing menu items based on roles defined in menu JSON.
           Example menu item in JSON: "role": ["Admin", "Pegawai Penyokong"]
      --}}
      @php
          $userRole = Auth::user() ? Auth::user()->getRoleNames()->first() : null;
          $canShowMenu = true; // Default to true if no role is specified for the menu item
          if (isset($menu->role)) {
              if (is_array($menu->role)) {
                  $canShowMenu = $userRole && in_array($userRole, $menu->role);
              } else {
                  $canShowMenu = $userRole && $userRole === $menu->role;
              }
          }
      @endphp

      @if ($canShowMenu)
        <li class="menu-item {{$activeClass}}">
          <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
            @isset($menu->icon)
              {{-- Design Document: Consistent set of simple, clear line icons --}}
              <i class="{{ $menu->icon }}"></i>
            @endisset
            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div> {{-- Already uses __() which is good --}}
            @isset($menu->badge)
              <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
            @endisset
          </a>

          {{-- submenu --}}
          @isset($menu->submenu)
            @include('layouts.sections.menu.submenu',['menu' => $menu->submenu, 'userRole' => $userRole]) {{-- Pass userRole to submenu --}}
          @endisset
        </li>
      @endif
    @endif
    @endforeach
  </ul>

</aside>

{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}
{{--<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" aria-label="Navigasi Sistem">
  @if (!($navbarFull ?? false))
    <div class="app-brand demo px-3 py-2 border-bottom">
      <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center gap-2">
        <span class="app-brand-logo demo">
          <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo Aplikasi') }}" height="32">
        </span>
        <span class="app-brand-text fw-semibold">{{ __($configData['templateName'] ?? 'Sistem MOTAC') }}</span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link ms-auto">
        <i class="ti ti-x ti-sm align-middle d-block d-xl-none"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @if(isset($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu))
      @foreach ($menuData->menu as $menu)
        @php
          $canViewMenu = false;
          if ($role === 'Admin') {
              $canViewMenu = true;
          } elseif (isset($menu->role)) {
              $canViewMenu = in_array($role, (array) $menu->role);
          } else {
              $canViewMenu = true;
          }

          $isActive = false;
          $currentRouteName = Route::currentRouteName();

          if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
              $isActive = true;
          } elseif (isset($menu->routeNamePrefix) && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
              $isActive = true;
          } elseif (!empty($menu->submenu)) {
              foreach ($menu->submenu as $subItem) {
                  if (isset($subItem->routeName) && $subItem->routeName === $currentRouteName) {
                      $isActive = true;
                      break;
                  }
              }
          }

          $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu);
          $menuItemClass = $isActive ? ($hasSubmenu ? 'active open' : 'active') : '';
          $menuLinkClass = $hasSubmenu ? 'menu-link menu-toggle' : 'menu-link';
          $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
        @endphp

        @if ($canViewMenu)
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase text-muted fw-bold">
              <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
          @else
            <li class="menu-item {{ $menuItemClass }}">
              <a href="{{ $menuHref }}" class="{{ $menuLinkClass }}" @if(isset($menu->target)) target="{{ $menu->target }}" @endif>
                @isset($menu->icon)
                  <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                @endisset
                <div class="menu-item-label">{{ __($menu->name ?? '-') }}</div>
                @isset($menu->badge)
                  <span class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">
                    {{ __($menu->badge[1]) }}
                  </span>
                @endisset
              </a>

              @if ($hasSubmenu)
                @include('layouts.sections.menu.submenu', [
                  'menu' => $menu->submenu,
                  'configData' => $configData,
                  'currentUserRole' => $role
                ])
              @endif
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link">
          <i class="menu-icon tf-icons ti ti-alert-circle"></i>
          <div class="menu-item-label">{{ __('Tiada data menu tersedia.') }}</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
