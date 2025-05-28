{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}
{{-- This is a traditional Blade include for the vertical menu. --}}
{{-- It assumes $configData and $menuData are globally available (e.g., shared by service providers). --}}
{{-- Design Language: MOTAC Branding, Bahasa Melayu, Intuitive Navigation. --}}

@php
  // $configData is expected to be globally available from AppServiceProvider or commonMaster
  // $menuData is expected to be globally available from MenuServiceProvider
  // For this non-Livewire version, we fetch the role directly if needed for filtering.
  $currentUserRole = Auth::user()?->getRoleNames()->first(); // Assuming Spatie roles
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  @if(!(isset($navbarFull) && $navbarFull === true)) {{-- Typically true for vertical menu unless specific full-navbar page --}}
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        {{-- Replace _partials.macros with direct img tag for MOTAC logo --}}
        <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo Sistem MOTAC') }}" height="32">
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __( ($configData['templateName'] ?? config('variables.templateName', 'Sistem MOTAC')) ) }}</span>
    </a>

    {{-- Menu toggle button --}}
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
        {{-- Role-based menu item visibility --}}
        @php
          $canViewMenu = false;
          if ($currentUserRole === 'Admin') {
              $canViewMenu = true;
          } elseif (isset($menu->role)) {
              if (is_array($menu->role) && !empty($currentUserRole) && in_array($currentUserRole, $menu->role)) {
                  $canViewMenu = true;
              } elseif (is_string($menu->role) && $currentUserRole === $menu->role) {
                  $canViewMenu = true;
              }
          } elseif (isset($menu->permissions)) {
              // $canViewMenu = Auth::user() && Auth::user()->canany(is_array($menu->permissions) ? $menu->permissions : [$menu->permissions]);
          }
           else { // If no specific role/permission, assume visible
              $canViewMenu = true;
          }
        @endphp

        @if ($canViewMenu)
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ __( ($menu->name ?? $menu->menuHeader) ) }}</span>
            </li>
          @else
            @php
              $activeClass = null;
              $currentRouteName = Route::currentRouteName();
              $layoutType = $configData['layout'] ?? 'vertical';
              $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';

              if (isset($menu->slug) && $currentRouteName === $menu->slug) {
                $activeClass = 'active';
              } elseif (isset($menu->routeName) && $currentRouteName === $menu->routeName) {
                $activeClass = 'active';
              } elseif (isset($menu->submenu) && (isset($menu->slug) || isset($menu->routeName))) {
                  $slugsToCheck = [];
                  if (isset($menu->slug)) {
                      $slugsToCheck = is_array($menu->slug) ? $menu->slug : [(string)$menu->slug];
                  }
                  // If routeName is primary for matching groups, use it
                  if (isset($menu->routeNamePrefix) || (isset($menu->routeName) && is_string($menu->routeName) && str_contains($menu->routeName, '*'))){
                     $prefixToMatch = $menu->routeNamePrefix ?? str_replace('*','', $menu->routeName);
                     if (str_starts_with((string)$currentRouteName, $prefixToMatch)) {
                         $activeClass = $activeOpenClass;
                     }
                  } else { // Fallback to slug-based matching for groups if no explicit routeNamePrefix
                      foreach($slugsToCheck as $slug_item) {
                        if (!empty($slug_item) && str_starts_with((string)$currentRouteName, (string)$slug_item)) {
                          $activeClass = $activeOpenClass;
                          break;
                        }
                      }
                  }
              }
            @endphp
            <li class="menu-item {{ $activeClass }}">
              <a href="{{ isset($menu->url) ? url($menu->url) : (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);') }}"
                 class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                 @if (isset($menu->target) && !empty($menu->target)) target="{{ $menu->target }}" @endif>
                @isset($menu->icon) <i class="{{ $menu->icon }}"></i> @endisset
                <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                @isset($menu->badge) <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div> @endisset
              </a>
              @isset($menu->submenu)
                @include('layouts.sections.menu.submenu', [
                    'menu' => $menu->submenu,
                    'configData' => $configData, // Pass configData
                    'role' => $currentUserRole    // Pass current user's role
                ])
              @endisset
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link">
            <i class="menu-icon tf-icons ti ti-error-404"></i>
            <div class="menu-item-label">{{__('Menu data tidak tersedia.')}}</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
