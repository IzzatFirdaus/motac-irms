{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
{{-- Renders the main vertical navigation menu. --}}
{{-- System Design: Phase 3 (Vertical Menu), Design Language: Intuitive Navigation --}}
<div>
  @php
    // $configData is globally available from commonMaster or App\Helpers\Helpers::appClasses()
    $configData = \App\Helpers\Helpers::appClasses();

    // $menuData is assumed to be injected by a Service Provider (e.g., MenuServiceProvider)
    // It typically loads data from a JSON file like resources/menu/verticalMenu.json
    // The structure of $menuData->menu should contain items with 'name', 'slug', 'url', 'icon', 'role', 'submenu', etc.
  @endphp

  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- App Brand (Logo & Name) --}}
    {{-- Design Language: Prominent MOTAC Branding --}}
    @if(!isset($navbarFull)) {{-- Typically, brand is here for vertical menu --}}
      <div class="app-brand demo">
        <a href="{{url('/')}}" class="app-brand-link">
          <span class="app-brand-logo demo">
            {{-- Ensure asset path is correct for MOTAC logo --}}
            <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo Sistem MOTAC') }}" height="32"> {{-- Adjusted height for clarity --}}
          </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
        </a>

        {{-- Menu Toggle Button (for collapsing menu on larger screens) --}}
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto {{ $configData['layout'] === 'horizontal' ? 'd-none' : '' }}">
            {{-- Icons for toggle state --}}
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i> {{-- Chevron left/right based on LTR/RTL --}}
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i> {{-- Close icon for mobile --}}
        </a>
      </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      @if(isset($menuData) && isset($menuData->menu) && count($menuData->menu) > 0)
        @foreach ($menuData->menu as $menu)
          {{-- Role-based menu item visibility --}}
          {{-- System Design: RBAC for menu items --}}
          @php
            $canViewMenu = false;
            if ($role === 'Admin') { // Super Admin sees all
                $canViewMenu = true;
            } elseif (isset($menu->role)) {
                if (is_array($menu->role) && in_array($role, $menu->role)) {
                    $canViewMenu = true;
                } elseif (is_string($menu->role) && $role === $menu->role) {
                    $canViewMenu = true;
                }
            } elseif (isset($menu->permissions)) { // Prefer permission-based checks if available
                // Assumes user has a method like canany() or similar for checking multiple permissions
                // $canViewMenu = Auth::user() && Auth::user()->canany(is_array($menu->permissions) ? $menu->permissions : [$menu->permissions]);
            }
             else { // If no specific role/permission is defined, assume visible to all authenticated users
                $canViewMenu = true;
            }
          @endphp

          @if ($canViewMenu)
            {{-- Menu Headers --}}
            @if (isset($menu->menuHeader))
              <li class="menu-header small text-uppercase">
                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
              </li>
            @else
              {{-- Active Menu Logic --}}
              @php
                $activeClass = null;
                $currentRouteName = Route::currentRouteName();

                if (isset($menu->slug) && $currentRouteName === $menu->slug) {
                  $activeClass = 'active';
                } elseif (isset($menu->submenu) && isset($menu->slug)) {
                  // Check if current route starts with any of the defined slugs for parent menu
                  $slugsToCheck = is_array($menu->slug) ? $menu->slug : [$menu->slug];
                  foreach ($slugsToCheck as $slug) {
                    if (str_starts_with((string)$currentRouteName, (string)$slug)) {
                      $activeClass = 'active open';
                      break;
                    }
                  }
                }
              @endphp

              {{-- Main Menu Item --}}
              <li class="menu-item {{ $activeClass }}">
                <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                   class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                   @if (isset($menu->target) && !empty($menu->target)) target="{{ $menu->target }}" @endif>
                  @isset($menu->icon)
                    <i class="{{ $menu->icon }}"></i>
                  @endisset
                  {{-- Design Language: Bahasa Melayu as Primary Language --}}
                  <div class="menu-item-label">{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                  @isset($menu->badge)
                    <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                  @endisset
                </a>

                {{-- Submenu --}}
                @isset($menu->submenu)
                  {{-- Assumes submenu.blade.php handles recursion and role checks for sub-items --}}
                  @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu, 'role' => $role])
                @endisset
              </li>
            @endif
          @endif
        @endforeach
      @else
        {{-- Fallback if menu data is not available --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link">
                <i class="menu-icon tf-icons ti ti-alert-circle"></i>
                <div class="menu-item-label">{{ __('Menu tidak dimuatkan') }}</div>
            </a>
        </li>
      @endif
    </ul>
  </aside>
</div>
