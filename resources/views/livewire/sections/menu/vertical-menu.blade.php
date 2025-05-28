{{-- resources/views/livewire/sections/menu/vertical-menu.blade.php --}}
<div>
  @php
    $configData = \App\Helpers\Helpers::appClasses(); // [cite: 4]
    // $menuData is assumed to be injected by a Service Provider (e.g., MenuServiceProvider)
    // and available to this Livewire component's view.
    // $this->role is the role fetched in the VerticalMenu.php Livewire component. [cite: 3]
  @endphp

  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"> @if(!isset($navbarFull)) {{-- Typically, brand is here for vertical menu --}} <div class="app-brand demo"> <a href="{{url('/')}}" class="app-brand-link"> <span class="app-brand-logo demo"> <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo Sistem MOTAC') }}" height="32"> </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span> </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto {{ ($configData['layout'] ?? 'vertical') === 'horizontal' ? 'd-none' : '' }}"> <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i> <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i> </a>
      </div>
    @endif

    <div class="menu-inner-shadow"></div> <ul class="menu-inner py-1"> @if(isset($menuData) && isset($menuData->menu) && count($menuData->menu) > 0)
        @foreach ($menuData->menu as $menu)
          @php
            $canViewMenu = false;
            if (isset($this->role) && $this->role === 'Admin') { // Super Admin sees all [cite: 4]
                $canViewMenu = true;
            } elseif (isset($menu->role)) { // [cite: 4]
                if (is_array($menu->role) && isset($this->role) && in_array($this->role, $menu->role)) { // [cite: 4]
                    $canViewMenu = true;
                } elseif (is_string($menu->role) && isset($this->role) && $this->role === $menu->role) { // [cite: 4]
                    $canViewMenu = true;
                }
            } elseif (isset($menu->permissions)) { // [cite: 4]
                // Example: $canViewMenu = Auth::user() && Auth::user()->canany(is_array($menu->permissions) ? $menu->permissions : [$menu->permissions]);
            } else { // If no specific role/permission is defined [cite: 4]
                $canViewMenu = isset($this->role); // Only show if user has a role, or true if public items allowed
            }
          @endphp

          @if ($canViewMenu)
            @if (isset($menu->menuHeader))
              <li class="menu-header small text-uppercase"> <span class="menu-header-text">{{ __($menu->menuHeader) }}</span> </li>
            @else
              @php
                $activeClass = null;
                $currentRouteName = Route::currentRouteName(); // [cite: 4]
                $isSubmenuActive = false; // [cite: 4]

                if (isset($menu->submenu) && is_array($menu->submenu)) { // [cite: 4]
                    foreach ($menu->submenu as $sub) { // [cite: 4]
                        if (isset($sub->routeName) && $currentRouteName === $sub->routeName) { // [cite: 4]
                            $isSubmenuActive = true; break;
                        }
                        if (isset($sub->submenu) && is_array($sub->submenu)) { // Check one level deeper
                            foreach ($sub->submenu as $deepSub) { // [cite: 4]
                                if (isset($deepSub->routeName) && $currentRouteName === $deepSub->routeName) { // [cite: 4]
                                    $isSubmenuActive = true; break;
                                }
                            }
                        }
                        if ($isSubmenuActive) break;
                    }
                }

                if (isset($menu->routeName) && $currentRouteName === $menu->routeName) { // [cite: 4]
                  $activeClass = 'active'; // [cite: 4]
                  if (isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0) $activeClass .= ' open'; // [cite: 4]
                } elseif ($isSubmenuActive && isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0) { // [cite: 4]
                  $activeClass = 'active open'; // [cite: 4]
                }
                // Fallback for slug/routeNamePrefix prefix based active state for parent highlighting
                elseif (isset($menu->routeNamePrefix) && str_starts_with((string)$currentRouteName, $menu->routeNamePrefix) && isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0) { // [cite: 4]
                    $activeClass = 'active open'; // [cite: 4]
                } elseif (isset($menu->slug) && str_starts_with((string)$currentRouteName, $menu->slug) && isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0) { // [cite: 4]
                    $activeClass = 'active open'; // [cite: 4]
                }
              @endphp

              <li class="menu-item {{ $activeClass }}"> <a href="{{ isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : (isset($menu->url) ? url($menu->url) : 'javascript:void(0);') }}"
                   class="{{ (isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0) ? 'menu-link menu-toggle' : 'menu-link' }}"
                   @if (isset($menu->target) && !empty($menu->target)) target="{{ $menu->target }}" @endif> @isset($menu->icon)
                    <i class="{{ $menu->icon }}"></i> @endisset
                  <div class="menu-item-label">{{ isset($menu->name) ? __($menu->name) : '' }}</div> @isset($menu->badge)
                    <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div> @endisset
                </a>

                @if(isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0)
                  {{-- Points to the new recursive submenu partial within the Livewire views directory --}}
                  @include('livewire.sections.menu.recursive-submenu', [
                      'submenuItems' => $menu->submenu,
                      'currentRole' => $this->role,
                      'configData' => $configData,
                      'parentRouteNamePrefix' => $menu->routeNamePrefix ?? ($menu->slug ?? '')
                  ])
                @endif
              </li>
            @endif
          @endif
        @endforeach
      @else
        <li class="menu-item"> <a href="javascript:void(0);" class="menu-link"> <i class="menu-icon tf-icons ti ti-alert-circle"></i> <div class="menu-item-label">{{ __('Menu tidak dimuatkan') }}</div> </a>
        </li>
      @endif
    </ul>
  </aside>
</div>
