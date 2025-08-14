{{--
    MOTAC IRMS - Navbar Section
    MYDS-compliant, responsive, accessible navigation bar for MOTAC IRMS dashboard.
    Adjusted to avoid PHP static analysis errors for Laravel helpers (route, url, asset, app, Auth, Str).
    Fallback logic is used for environments without Blade helper functions.
--}}

@php
    // Fallbacks for Blade/Laravel helper functions in static analysis or non-Blade PHP environments

    // Home URL (usually '/')
    $homeUrl = function_exists('url') ? url('/') : '/';

    // Logo asset URL (update path as needed)
    $logoUrl = function_exists('asset') ? asset('assets/img/logo-motac.png') : '/assets/img/logo-motac.png';

    // Route helpers for navigation links
    $dashboardUrl = function_exists('route') ? route('dashboard') : '/dashboard';
    $profileUrl = function_exists('route') ? route('profile.show') : '/profile/show';
    $settingsUrl = function_exists('route') ? route('settings') : '/settings';

    // Language switching (assuming 'app' helper for locale)
    $langMs = function_exists('app') ? app()->getLocale() === 'ms' : false;
    $langEn = function_exists('app') ? app()->getLocale() === 'en' : false;

    // User authentication and info
    $user = class_exists('Auth') && Auth::check() ? Auth::user() : null;

    // User roles display (uses getRoleNames if method exists, else fallback to empty array)
    $roleNames = ($user && method_exists($user, 'getRoleNames')) ? $user->getRoleNames() : [];

    // Str helper fallback for role display
    if (!class_exists('Illuminate\\Support\\Str')) {
        function str_title($value) { return ucwords(str_replace('_', ' ', $value)); }
    }

    // Other routes (update as needed)
    $logoutRoute = function_exists('route') ? route('logout') : '/logout';
    $helpRoute = function_exists('route') ? route('help') : '/help';
    $adminRoute = function_exists('route') ? route('admin.dashboard') : '/admin/dashboard';

    // Example for asset
    $avatarUrl = ($user && isset($user->profile_photo_url) && $user->profile_photo_url)
        ? $user->profile_photo_url
        : (function_exists('asset') ? asset('assets/img/avatars/default-avatar.png') : '/assets/img/avatars/default-avatar.png');
@endphp

<nav class="myds-navbar navbar navbar-expand-lg navbar-light bg-white shadow-card px-3 py-2" role="navigation" aria-label="Main Navigation">
    <div class="container-fluid">
        {{-- Logo and Home link --}}
        <a href="{{ $homeUrl }}" class="navbar-brand d-flex align-items-center">
            <img src="{{ $logoUrl }}" alt="MOTAC Logo" style="height: 40px;" class="me-2" />
            <span class="fw-semibold" style="font-family: 'Poppins', Arial, sans-serif; font-size: 1.25rem; color: var(--myds-primary-700);">
                MOTAC IRMS
            </span>
        </a>
        {{-- Responsive navbar toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        {{-- Navbar links --}}
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="{{ $dashboardUrl }}" class="nav-link">{{ __('Dashboard') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ $settingsUrl }}" class="nav-link">{{ __('Settings') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ $helpRoute }}" class="nav-link">{{ __('Help') }}</a>
                </li>
                {{-- Example admin link, only show if user is admin --}}
                @if($user && in_array('admin', $roleNames))
                    <li class="nav-item">
                        <a href="{{ $adminRoute }}" class="nav-link">{{ __('Admin') }}</a>
                    </li>
                @endif
            </ul>
            {{-- Language Switcher --}}
            <div class="navbar-lang d-flex align-items-center me-4">
                <span class="me-2">{{ __('Language:') }}</span>
                <a href="?lang=ms" class="btn btn-link btn-sm px-2 py-1 {{ $langMs ? 'fw-bold text-primary' : 'text-muted' }}">BM</a>
                <span class="mx-1">|</span>
                <a href="?lang=en" class="btn btn-link btn-sm px-2 py-1 {{ $langEn ? 'fw-bold text-primary' : 'text-muted' }}">EN</a>
            </div>
            {{-- User Profile Dropdown --}}
            @if($user)
            <div class="dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }} avatar" class="rounded-circle me-2" style="width:32px; height:32px; object-fit:cover;" />
                    <span>{{ $user->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ $profileUrl }}">
                            <i class="bi bi-person me-2"></i>{{ __('Profile') }}
                        </a>
                    </li>
                    <li>
                        <span class="dropdown-item-text text-muted small">{{ __('Role:') }}
                            @if(!empty($roleNames))
                                @foreach($roleNames as $role)
                                    <span class="badge bg-primary ms-1">{{ class_exists('Illuminate\\Support\\Str') ? Illuminate\Support\Str::title($role) : str_title($role) }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary ms-1">{{ __('User') }}</span>
                            @endif
                        </span>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ $settingsUrl }}">
                            <i class="bi bi-gear me-2"></i>{{ __('Settings') }}
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <form method="POST" action="{{ $logoutRoute }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            {{-- Login link for guest --}}
            <a href="{{ function_exists('route') ? route('login') : '/login' }}" class="btn btn-primary">{{ __('Login') }}</a>
            @endif
        </div>
    </div>
</nav>

{{--
    Documentation notes:
    - All URLs and asset paths use fallback logic for environments where Laravel helpers may not be available.
    - Language switcher works with query params (?lang=ms / ?lang=en).
    - Role display uses Str::title if available, otherwise falls back to simple PHP title format.
    - User dropdown displays profile, roles, settings, and logout; only shows admin link if user has 'admin' role.
    - Responsive and accessible: ARIA labels, nav roles, focusable elements.
--}}
