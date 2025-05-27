<div>
    {{--
        $configData is globally available from AppServiceProvider via Helpers::appClasses().
        $menuData is globally available from MenuServiceProvider (loaded from verticalMenu.json).
        $role is a public property from the VerticalMenu Livewire component.
    --}}
    @php
        // Ensure $configData and $menuData are available; provide safe fallbacks if not (though providers should ensure they are).
        $configData = $configData ?? App\Helpers\Helpers::appClasses(); // Fallback just in case
        $menuData = $menuData ?? json_decode('{"menu": []}'); // Fallback for menu data

        // Determine if navbarFull is set to hide the brand in the menu
        $navbarFull = $configData['navbarFull'] ?? false; // As per commonMaster and app layouts

        // Standardized role names from System Design
        $adminRoleName = 'Admin'; // Consistent admin role name
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        @if(!$navbarFull)
            <div class="app-brand demo">
                <a href="{{ url('/') }}" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        {{-- System Design 6.1: Unified MOTAC Branding --}}
                        @include('_partials.macros', ['height' => 20, 'width' => 20]) {{-- Assuming this partial renders MOTAC logo --}}
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2">{{ $configData['templateName'] ?? config('app.name', 'MOTAC RMS') }}</span>
                </a>

                {{-- Responsive Menu Toggle Button (usually hidden on larger screens by CSS/JS) --}}
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
                    @php
                        // Role-based visibility check
                        // $role is from the Livewire component (e.g., 'Admin', 'BPM Staff', 'Employee', 'Guest')
                        $isVisible = false;
                        if (isset($menu->role)) {
                            if ($role === $adminRoleName) { // Admin sees all items that have a role defined
                                $isVisible = true;
                            } elseif (is_array($menu->role) && in_array($role, $menu->role)) {
                                $isVisible = true;
                            } elseif (is_string($menu->role) && $role === $menu->role) {
                                $isVisible = true;
                            }
                        } else {
                             $isVisible = true; // No role defined means visible to all (including guests if $role is 'Guest')
                        }
                    @endphp

                    @if ($isVisible)
                        {{-- Menu Headers --}}
                        @if (isset($menu->menuHeader))
                            <li class="menu-header small text-uppercase mt-2">
                                <span class="menu-header-text">{{ isset($menu->name) ? __($menu->name) : (isset($menu->menuHeader) ? __($menu->menuHeader) : '') }}</span>
                            </li>
                        @else
                            {{-- Menu Items --}}
                            @php
                                $activeClass = '';
                                $currentRouteName = Route::currentRouteName();
                                $menuSlug = $menu->slug ?? null;

                                if ($menuSlug) {
                                    if (is_string($menuSlug) && $currentRouteName === $menuSlug) {
                                        $activeClass = 'active';
                                    } elseif (is_array($menuSlug)) { // If slug can be an array of route names for highlighting
                                        foreach($menuSlug as $slug_item) {
                                            if ($currentRouteName === $slug_item || str_starts_with((string)$currentRouteName, (string)$slug_item . '.')) {
                                                $activeClass = ($configData['layout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
                                                break;
                                            }
                                        }
                                    } elseif (is_string($menuSlug) && isset($menu->submenu) && str_starts_with((string)$currentRouteName, $menuSlug . '.')) {
                                        // For parent menu highlighting if submenu routes are prefixed by parent slug
                                        $activeClass = ($configData['layout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
                                    }
                                }
                            @endphp

                            {{-- Special handling for logout link --}}
                            @if (($menu->slug ?? '') === 'logout')
                                <li class="menu-item">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form-menu').submit();"
                                       class="menu-link">
                                        @isset($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endisset
                                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                                    </a>
                                    {{-- Keep the form outside the <a> tag if it causes issues, or ensure it's not nested in a way that breaks HTML --}}
                                </li>
                            @else
                                <li class="menu-item {{ $activeClass }}">
                                    <a href="{{ isset($menu->url) ? (Str::startsWith($menu->url, ['http://', 'https://']) ? $menu->url : url($menu->url)) : (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName, ($menu->routeParams ?? [])) : 'javascript:void(0);') }}"
                                       class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                                       @if (isset($menu->target) && !empty($menu->target)) target="{{ $menu->target }}" @endif>
                                        @isset($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endisset
                                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                                        @isset($menu->badge)
                                            <div class="badge bg-label-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                                        @endisset
                                    </a>

                                    {{-- Render submenu if it exists --}}
                                    @isset($menu->submenu)
                                        @include('layouts.sections.menu.submenu', [
                                            'menu' => $menu->submenu,
                                            'configData' => $configData, // Pass for submenu rendering consistency
                                            'role' => $role           // Pass role for submenu item filtering
                                        ])
                                    @endisset
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
            @else
                <li class="menu-item">
                    <div class="menu-link text-warning">{{ __('Menu data not available or malformed.') }}</div>
                </li>
            @endif
        </ul>
        {{-- Hidden form for logout, can be placed outside the main <ul> if that's cleaner --}}
        <form method="POST" id="logout-form-menu" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
    </aside>
</div>
