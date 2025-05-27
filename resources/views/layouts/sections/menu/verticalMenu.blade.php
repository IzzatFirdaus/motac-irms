@php
  // $configData is expected to be globally available from AppServiceProvider (via Helpers::appClasses())
  // $menuData is expected to be globally available (e.g., from MenuServiceProvider)
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  @if(!(isset($navbarFull) && $navbarFull === true))
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @include('_partials.macros',["height"=>20])
      </span>
      <span class="app-brand-text demo menu-text fw-bold">{{ $configData['templateName'] ?? config('app.name') }}</span>
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
      @foreach ($menuData->menu as $menu)
        {{-- For this non-Livewire version, $role needs to be passed to this view/include if role-based filtering is required.
             Example: @php $role = Auth::user()?->getRoleNames()->first(); @endphp at the top, or passed in.
             Assuming $role is made available to this partial's scope if filtering is active.
        --}}
        @php $currentUserRole = Auth::user()?->getRoleNames()->first(); @endphp
        @if (isset($menu->role) && ( ($currentUserRole === 'Admin') || (is_array($menu->role) && isset($currentUserRole) && in_array($currentUserRole, $menu->role)) ) )
          @if (isset($menu->menuHeader))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ isset($menu->name) ? __($menu->name) : (isset($menu->menuHeader) ? __($menu->menuHeader) : '') }}</span>
            </li>
          @else
            @php
              $activeClass = null;
              $currentRouteName = Route::currentRouteName();
              if (isset($menu->slug) && $currentRouteName === $menu->slug) {
                $activeClass = 'active';
              } elseif (isset($menu->submenu) && isset($menu->slug)) {
                $slugsToCheck = is_array($menu->slug) ? $menu->slug : [(string)$menu->slug];
                foreach($slugsToCheck as $slug_item) {
                  if (str_starts_with((string)$currentRouteName, (string)$slug_item)) {
                    $activeClass = ($configData['layout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
                    break;
                  }
                }
              }
            @endphp
            <li class="menu-item {{$activeClass}}">
              <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                 class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                 @if (isset($menu->target) and !empty($menu->target)) target="{{ $menu->target }}" @endif>
                @isset($menu->icon) <i class="{{ $menu->icon }}"></i> @endisset
                <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                @isset($menu->badge) <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div> @endisset
              </a>
              @isset($menu->submenu)
                {{-- Pass $currentUserRole as $role to the submenu if this non-Livewire version requires it --}}
                @include('layouts.sections.menu.submenu',[
                    'menu' => $menu->submenu,
                    'configData' => $configData ?? [],
                    'role' => $currentUserRole ?? null
                ])
              @endisset
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item"><div class="menu-link">{{__('Menu data not available.')}}</div></li>
    @endif
  </ul>
</aside>
