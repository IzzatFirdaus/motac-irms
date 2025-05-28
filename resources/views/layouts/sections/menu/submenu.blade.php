{{-- resources/views/layouts/sections/menu/submenu.blade.php --}}
{{-- This is a recursive partial for rendering submenu items. --}}
{{-- It expects $menu (array of submenu items), $role (current user's role), and $configData. --}}
{{-- Design Language: Intuitive Navigation, Bahasa Melayu. --}}

<ul class="menu-sub">
  @if (isset($menu) && is_array($menu)) {{-- Check if $menu is set and is an array --}}
    @foreach ($menu as $submenu)
      {{-- Role-based visibility for submenu items --}}
      {{-- $role is passed from the parent menu view (e.g., vertical-menu.blade.php or the Livewire equivalent) --}}
      @php
        $canViewSubmenu = false;
        $currentUserRole = $role ?? Auth::user()?->getRoleNames()->first(); // Ensure role is available

        if ($currentUserRole === 'Admin') { // Super Admin sees all
            $canViewSubmenu = true;
        } elseif (isset($submenu->role)) {
            if (is_array($submenu->role) && !empty($currentUserRole) && in_array($currentUserRole, $submenu->role)) {
                $canViewSubmenu = true;
            } elseif (is_string($submenu->role) && $currentUserRole === $submenu->role) {
                $canViewSubmenu = true;
            }
        } elseif (isset($submenu->permissions)) {
            // $canViewSubmenu = Auth::user() && Auth::user()->canany(is_array($submenu->permissions) ? $submenu->permissions : [$submenu->permissions]);
        }
         else { // If no specific role/permission, assume visible if parent was visible
            $canViewSubmenu = true;
        }
      @endphp

      @if ($canViewSubmenu)
        @php
          $activeClass = null;
          // $configData should be passed down from the parent include.
          $layoutType = $configData['layout'] ?? 'vertical'; // Default to vertical
          $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active'; // 'open' for vertical submenus
          $currentRouteName = Route::currentRouteName();

          if (isset($submenu->slug) && $currentRouteName === $submenu->slug) {
              $activeClass = 'active'; // Exact match always 'active'
          } elseif (isset($submenu->submenu) && isset($submenu->slug)) { // If it has a submenu, it can be 'active open'
            $slugsToCheck = is_array($submenu->slug) ? $submenu->slug : [(string)$submenu->slug];
            foreach($slugsToCheck as $slug_item) {
              if (!empty($slug_item) && str_starts_with((string)$currentRouteName, (string)$slug_item)) {
                  $activeClass = $activeOpenClass;
                  break;
              }
            }
          } elseif (isset($submenu->routeName) && $currentRouteName === $submenu->routeName) {
             $activeClass = 'active';
          }
        @endphp

        <li class="menu-item {{ $activeClass }}">
          <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0);' }}"
             class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (isset($submenu->target) && !empty($submenu->target)) target="{{ $submenu->target }}" @endif>
            @if (isset($submenu->icon))
              <i class="{{ $submenu->icon }}"></i>
            @endif
            <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
             @isset($submenu->badge)
                <div class="badge bg-label-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
             @endisset
          </a>

          {{-- Recursive include for nested submenus --}}
          @if (isset($submenu->submenu))
            @include('layouts.sections.menu.submenu', [
                'menu' => $submenu->submenu,
                'configData' => $configData, // Pass $configData down
                'role' => $currentUserRole   // Pass current user's role down
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
