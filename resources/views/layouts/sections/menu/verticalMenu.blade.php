{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}
{{-- MOTAC ICT LOAN SYSTEM | Vertical Sidebar Navigation --}}
{{-- Expected: $configData (from Helpers::appClasses()), $menuData (from config or service provider), $currentUserRole (from controller/component or derived) --}}

@php
  // Ensure $currentUserRole is available; if not passed, try to get it.
  // It's better if the Livewire component (VerticalMenu.php) consistently provides this.
  $currentUserRole = $currentUserRole ?? (Auth::check() ? Auth::user()->getRoleNames()->first() : null);
  $currentRouteName = Route::currentRouteName();
  $layoutType = $configData['layout'] ?? 'vertical';
  $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active'; // Class for active parent

  // Helper function to recursively check if a menu item or any of its children are active
  // This helps in deciding if a parent menu item should be 'open'
  if (!function_exists('isMenuItemActive')) {
      function isMenuItemActive($item, $currentRouteName, &$isAnyChildActive = false) {
          $isActive = false;
          if (isset($item->routeName) && $item->routeName === $currentRouteName) {
              $isActive = true;
              $isAnyChildActive = true; // Mark that an active child path is found
          } elseif (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix)) {
              $isActive = true;
              // $isAnyChildActive = true; // Optionally mark parent active if prefix matches a child's active state
          } elseif (isset($item->slug) && !isset($item->routeName) && !isset($item->routeNamePrefix)) {
             // Fallback to slug for items that might not have direct routes but group children
             // This condition might need to be more specific if slugs are broadly used for active state
          }

          if (!empty($item->submenu)) {
              $hasActiveGrandChild = false;
              foreach ($item->submenu as $subItem) {
                  if (isMenuItemActive($subItem, $currentRouteName, $hasActiveGrandChild)) {
                      $isActive = true; // Parent is active if any child is active
                      $isAnyChildActive = true; // Propagate upwards
                      break;
                  }
              }
          }
          return $isActive;
      }
  }
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
          } elseif (isset($menu->permissions) && Auth::check()) {
              // Optional: implement canAny() logic if needed, e.g., Auth::user()->canAny((array)$menu->permissions);
          } else {
              $canViewMenu = true; // Default for items without specific role/permission
          }
        @endphp

        @if ($canViewMenu)
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ __($menu->menuHeader ?? $menu->name) }}</span>
            </li>
          @else
            @php
              $menuItemIsActive = false; // Is the menu item itself (or its direct route) active?
              $anyChildIsActive = false;  // Is any child/grandchild of this menu item active?

              // Check if this menu item itself or any of its children are active
              isMenuItemActive($menu, $currentRouteName, $anyChildIsActive);

              // The item's direct route is active
              if (isset($menu->routeName) && $menu->routeName === $currentRouteName) {
                  $menuItemIsActive = true;
              }
              // Active based on routeNamePrefix if not already active by specific route or child
              // This ensures the prefix logic applies primarily if no specific child makes it active.
              if (!$menuItemIsActive && !$anyChildIsActive && isset($menu->routeNamePrefix) && str_starts_with($currentRouteName, $menu->routeNamePrefix)) {
                  $menuItemIsActive = true;
              }

              $activeClass = '';
              if ($menuItemIsActive || $anyChildIsActive) {
                  if (isset($menu->submenu) && !empty($menu->submenu)) {
                      $activeClass = $activeOpenClass; // 'active open' for parents with active state
                  } else {
                      $activeClass = 'active'; // 'active' for items without submenu or when only itself is active
                  }
              }

              $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
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
                {{-- Pass $menu->submenu as $currentMenuLevelItems to avoid variable name collision --}}
                @include('layouts.sections.menu.submenu', [
                    'menu' => $menu->submenu,
                    'configData' => $configData,
                    'currentUserRole' => $currentUserRole // Pass the role for consistent checking in submenu
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
