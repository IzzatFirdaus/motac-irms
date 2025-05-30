{{-- submenu.blade.php --}}
<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    {{-- Design Document: Role-specific interfaces for submenu items --}}
    @php
        // $userRole is passed from verticalMenu.blade.php
        $canShowSubmenu = true; // Default to true if no role is specified for the submenu item
        if (isset($submenu->role)) {
            // Ensure $userRole is not null before using it in conditions
            if ($userRole) { // Added check for $userRole
                if (is_array($submenu->role)) {
                    $canShowSubmenu = in_array($userRole, $submenu->role);
                } else {
                    $canShowSubmenu = ($userRole === $submenu->role);
                }
            } else {
                // If $userRole is null (e.g., guest) and submenu requires a role, hide it
                $canShowSubmenu = false;
            }
        }
    @endphp

    @if ($canShowSubmenu)
      {{-- active menu method --}}
      @php
        $activeClass = null;
        $active = 'active open'; // Simplified, adjust if $configData logic is needed and can be passed
        $currentRouteName =  Route::currentRouteName();

        if ($currentRouteName === $submenu->slug) {
            $activeClass = 'active';
        }
        elseif (isset($submenu->submenu)) {
          if (gettype($submenu->slug) === 'array') {
            foreach($submenu->slug as $slug){
              if (str_contains($currentRouteName,$slug) && strpos($currentRouteName,$slug) === 0) { // Fixed str_contains parameters
                  $activeClass = $active;
              }
            }
          }
          else{
            if (str_contains($currentRouteName,$submenu->slug) && strpos($currentRouteName,$submenu->slug) === 0) { // Fixed str_contains parameters
              $activeClass = $active;
            }
          }
        }
      @endphp

        <li class="menu-item {{$activeClass}}">
          <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>
            @if (isset($submenu->icon))
            <i class="{{ $submenu->icon }}"></i>
            @endif
            <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          </a>

          {{-- submenu --}}
          @if (isset($submenu->submenu))
            {{-- Recursively include submenu, passing userRole --}}
            @include('layouts.sections.menu.submenu',['menu' => $submenu->submenu, 'userRole' => $userRole]) {{-- This is correct --}}
          @endif
        </li>
      @endif
    @endforeach
  @endif
</ul>


{{-- resources/views/layouts/sections/menu/submenu.blade.php --}}
{{-- Expected variables: $menu (array of submenu items), $configData, $currentUserRole --}}
{{--}}
@php
    $currentRouteName = Route::currentRouteName();
    $layoutType = $configData['layout'] ?? 'vertical';
    $activeOpenClass = $layoutType === 'vertical' ? 'active open' : 'active';

    // Using the same helper function defined in the parent.
    // For safety, ensure it's available or redefine with a different name if necessary,
// or better, move to a global helper/Blade component.
// For this example, we assume isMotacMenuItemActive is accessible or defined once.
// If not, you'd need to redefine it here with if(!function_exists(...)) check or pass active states.

@endphp

<ul class="menu-sub">
    @if (isset($menu) && is_array($menu))
        @foreach ($menu as $submenuItem)
            @php
                $canViewSubmenu = false;
                if ($currentUserRole === 'Admin') {
                    $canViewSubmenu = true;
                } elseif (isset($submenuItem->role)) {
                    $rolesArray = is_array($submenuItem->role) ? $submenuItem->role : [$submenuItem->role];
                    $canViewSubmenu = in_array($currentUserRole, $rolesArray);
                } else {
                    $canViewSubmenu = true;
                }

                $isCurrentSubItemBranchActive = false;
                // Call the main helper function, ensuring it's available or passed appropriately
if (function_exists('isMotacMenuItemActive')) {
    isMotacMenuItemActive(
        $submenuItem,
        $currentRouteName,
        $isCurrentSubItemBranchActive,
        $currentUserRole,
    );
}

$activeClass = '';
if ($isCurrentSubItemBranchActive) {
    $isDirectlyActive =
        (isset($submenuItem->routeName) && $submenuItem->routeName === $currentRouteName) ||
        (isset($submenuItem->routeNamePrefix) &&
            str_starts_with($currentRouteName, $submenuItem->routeNamePrefix)) ||
        (isset($submenuItem->slug) &&
            $submenuItem->slug === $currentRouteName &&
            !isset($submenuItem->routeName) &&
            !isset($submenuItem->routeNamePrefix));

    if (isset($submenuItem->submenu) && !empty($submenuItem->submenu)) {
        $activeClass = $activeOpenClass;
    } elseif ($isDirectlyActive || $isCurrentSubItemBranchActive) {
        $activeClass = 'active';
    }
}

$hasFurtherSubmenu = isset($submenuItem->submenu) && !empty($submenuItem->submenu);
$submenuLinkClass = $hasFurtherSubmenu ? 'menu-link menu-toggle' : 'menu-link';
$submenuHref =
    $submenuItem->url ??
    (isset($submenuItem->routeName) && Route::has($submenuItem->routeName)
        ? route($submenuItem->routeName)
        : 'javascript:void(0);');
            @endphp

            @if ($canViewSubmenu)
                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ $submenuHref }}" class="{{ $submenuLinkClass }}"
                        @if (!empty($submenuItem->target)) target="{{ $submenuItem->target }}" @endif>
                        @isset($submenuItem->icon)
                            <i class="menu-icon tf-icons {{ $submenuItem->icon }}"></i>
                        @endisset
                        <div class="menu-item-label">{{ __($submenuItem->name ?? '') }}</div>
                        @isset($submenuItem->badge)
                            <div class="badge bg-label-{{ $submenuItem->badge[0] }} rounded-pill ms-auto">
                                {{ __($submenuItem->badge[1]) }}</div>
                        @endisset
                    </a>

                    @if ($hasFurtherSubmenu)
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $submenuItem->submenu,
                            'configData' => $configData,
                            'currentUserRole' => $currentUserRole,
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    @endif
</ul>--}}
