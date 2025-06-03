{{-- resources/views/livewire/sections/menu/recursive-submenu.blade.php --}}
{{--
  This partial recursively renders submenu items.
  It expects $submenuItems, $currentRole, $configData, $currentRouteName, and $activeOpenClass to be passed.
  Design Language Alignment:
  - Typography (2.2): Noto Sans for submenu text.
  - Iconography (2.4): Uses Bootstrap Icons if provided in $item->icon.
  - Colors (2.1): Active/hover states styled by MOTAC theme.
--}}
<ul class="menu-sub" role="menu"> {{-- Added role="menu" for better semantics --}}
  @if (!empty($submenuItems) && is_array($submenuItems))
    @foreach ($submenuItems as $item)
      @php
        $canView = false;
        if ($currentRole === 'Admin') {
            $canView = true;
        } elseif (isset($item->role)) {
            $roles = is_array($item->role) ? $item->role : [$item->role];
            $canView = in_array($currentRole, $roles);
        } elseif (isset($item->permissions) && Auth::check()) {
            // This permission check logic depends on your Spatie setup or custom permission system.
            // $permissions = is_array($item->permissions) ? $item->permissions : [$item->permissions];
            // $canView = Auth::user()->canAny($permissions); // Example
            $canView = true; // Placeholder: Default to true if permissions logic is complex/external
        } else {
            $canView = true; // Default to viewable if no specific role/permission
        }

        $subActiveClass = '';
        $currentRouteNameFromLaravel = $currentRouteName; // Already passed, no need to call Route:: again
        $isNestedSubmenuActive = false;

        if (!empty($item->submenu)) {
          // Simplified check for active child using the existing helper function passed down or re-evaluated
          // For this partial, we assume parent has already determined if it needs 'open'
          // This logic primarily sets 'active' for the item itself.
          $tempIsActiveForParent = false; // Dummy var for the recursive check signature
          if(function_exists('isMotacMenuItemActiveRecursiveCheck')) {
             isMotacMenuItemActiveRecursiveCheck($item, $currentRouteNameFromLaravel, $tempIsActiveForParent, $currentRole);
             $isNestedSubmenuActive = $tempIsActiveForParent; // Check if any child made this branch active
          }
        }

        if (isset($item->routeName) && $currentRouteNameFromLaravel === $item->routeName) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif ($isNestedSubmenuActive && !empty($item->submenu)) { // If a child is active and this has submenu, it should be open
            $subActiveClass = $activeOpenClass; // Use 'active open'
        } elseif (isset($item->routeNamePrefix) && str_starts_with($currentRouteNameFromLaravel, $item->routeNamePrefix)) {
            $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        } elseif (isset($item->slug) && str_starts_with($currentRouteNameFromLaravel, $item->slug) && !isset($item->routeName) && !isset($item->routeNamePrefix)) {
             if ($isNestedSubmenuActive && !empty($item->submenu)) $subActiveClass = $activeOpenClass;
             else $subActiveClass = 'active' . (!empty($item->submenu) ? ' open' : '');
        }

        $hasSubmenuRecursive = isset($item->submenu) && !empty($item->submenu);
        $submenuLinkClasses = $hasSubmenuRecursive ? 'menu-link menu-toggle' : 'menu-link';
        // Ensure generated href is valid or 'javascript:void(0);'
        $submenuTargetHref = 'javascript:void(0);';
        if (isset($item->url)) {
            $submenuTargetHref = url($item->url);
        } elseif (isset($item->routeName) && Route::has($item->routeName)) {
            $submenuTargetHref = route($item->routeName);
        } elseif (isset($item->slug) && Route::has($item->slug)) {
            $submenuTargetHref = route($item->slug);
        }

      @endphp

      @if ($canView)
        <li class="menu-item {{ $subActiveClass }}" role="none">
          <a href="{{ $submenuTargetHref }}"
             class="{{ $submenuLinkClasses }}"
             @if (!empty($item->target)) target="{{ $item->target }}" rel="noopener noreferrer" @endif
             role="menuitem" @if($hasSubmenuRecursive) aria-haspopup="true" aria-expanded="{{ str_contains($subActiveClass, 'open') ? 'true' : 'false' }}" @endif>
            @isset($item->icon)
              {{-- Iconography: Design Language 2.4. Ensure $item->icon contains "bi bi-icon-name" --}}
              <i class="menu-icon {{ $item->icon }}"></i>
            @endisset
            <div>{{ __($item->name ?? '') }}</div>
            @isset($item->badge)
              {{-- Ensure .bg-label-* classes are MOTAC themed --}}
              <div class="badge bg-label-{{ $item->badge[0] }} rounded-pill ms-auto">{{ __($item->badge[1]) }}</div>
            @endisset
          </a>

          @if ($hasSubmenuRecursive)
            @include('livewire.sections.menu.recursive-submenu', [
              'submenuItems' => $item->submenu,
              'currentRole' => $currentRole,
              'configData' => $configData,
              'currentRouteName' => $currentRouteName, // Pass current route
              'activeOpenClass' => $activeOpenClass    // Pass class for active parent
            ])
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>
