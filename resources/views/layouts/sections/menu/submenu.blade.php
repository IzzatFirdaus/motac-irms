<ul class="menu-sub">
  @if (isset($menu) && is_array($menu)) {{-- Added is_array check for $menu --}}
    @foreach ($menu as $submenu)
      {{-- Ensure $role is available from the parent view (vertical-menu.blade.php) --}}
      @if (isset($submenu->role) && ( ($role === 'Admin') || (is_array($submenu->role) && isset($role) && in_array($role, $submenu->role)) ) )
        @php
          $activeClass = null;
          // $configData should be available from the parent scope (ultimately from global share)
          // Helpers::appClasses() should now reliably provide 'layout'.
          $layoutType = $configData['layout'] ?? 'vertical'; // Fallback remains good practice
          $active = $layoutType === 'vertical' ? 'active open' : 'active';
          $currentRouteName = Route::currentRouteName();

          if (isset($submenu->slug) && $currentRouteName === $submenu->slug) {
              $activeClass = 'active';
          } elseif (isset($submenu->submenu) && isset($submenu->slug)) {
            $slugsToCheck = is_array($submenu->slug) ? $submenu->slug : [(string)$submenu->slug];
            foreach($slugsToCheck as $slug_item) {
              if (str_starts_with((string)$currentRouteName, (string)$slug_item)) { // Use str_starts_with
                  $activeClass = $active; // Uses 'active open' or 'active' based on layoutType
                  break;
              }
            }
          }
        @endphp
        <li class="menu-item {{ $activeClass }}">
          <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
             class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (isset($submenu->target) && !empty($submenu->target)) target="{{ $submenu->target }}" @endif>
            @if (isset($submenu->icon))
              <i class="{{ $submenu->icon }}"></i>
            @endif
            <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          </a>

          {{-- Recursive include for nested submenus --}}
          @if (isset($submenu->submenu))
            @include('layouts.sections.menu.submenu',[
                'menu' => $submenu->submenu,
                'configData' => $configData ?? [], // Pass $configData down
                'role' => $role ?? null             // Pass $role down
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
