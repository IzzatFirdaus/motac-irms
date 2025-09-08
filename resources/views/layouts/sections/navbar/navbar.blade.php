{{-- navbar.blade.php --}}
@php
    $containerNav = $containerNav ?? 'container-fluid'; // container-fluid for full width, container-xxl for boxed
    $navbarDetached = $navbarDetached ?? '';
    // It's better to pass availableLocales directly from the controller or Livewire component
    // For this example, assuming $availableLocales is passed or accessible via the Livewire component's public property
    // If using this blade directly with a standard controller, ensure $availableLocales is passed to the view.
    // If this is part of a Livewire component, the public $availableLocales property will be accessible.
@endphp

{{-- Design Language Documentation: Apply MOTAC primary color and ensure proper text contrast.
    Changed 'bg-navbar-theme' to 'motac-navbar navbar-dark'.
    'motac-navbar' class should be defined in your CSS to use var(--motac-primary).
--}}
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center motac-navbar navbar-dark"
        id="layout-navbar">
@endif

@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center motac-navbar navbar-dark" id="layout-navbar">
        <div class="{{ $containerNav }}">
@endif

@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                {{-- Design Language Documentation (Section 7.1): MOTAC logo lockup (40px height). SVG recommended. --}}
                <img src="{{ asset(config('variables.templateLogo')) }}" alt="{{ __('Logo MOTAC') }}" height="40">
                {{-- Design Language Documentation does not explicitly require Jata Negara in Top Action Bar, but it was in intranet example. Keeping as optional.
                <img src="{{ asset('assets/img/logo/jata_negara.png') }}" alt="{{ __('Jata Negara Malaysia') }}" height="32" class="ms-2">
                --}}
            </span>
            {{-- Design Language Documentation: System Title. Using existing config app.name or default. --}}
            <span
                class="app-brand-text demo menu-text fw-bold">{{ config('app.name', 'Sistem Pengurusan BPM MOTAC') }}</span>
        </a>
    </div>
@endif

@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-menu-2. --}}
            <i class="bi bi-list fs-4"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    {{-- Quick access links can be added here if needed, as per Design Document Section 1.3 (One-Action Access) --}}

    {{-- Style Switcher / Theme Toggle --}}
    {{-- Ensure $activeTheme is passed or accessible if this is not part of the Livewire component view directly --}}
    @if(isset($activeTheme))
    <div class="navbar-nav align-items-center">
        <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);"
           onclick="event.preventDefault(); window.toggleTheme();">
            <i class='bi fs-5 {{ $activeTheme === "dark" ? "bi-sun" : "bi-moon-stars-fill" }}'></i>
        </a>
    </div>
    @endif

    {{-- Language Switcher --}}
    {{-- Design Language Documentation (Section 3.1): Language toggle: BM/EN with flag icons. --}}
    {{-- Uses $availableLocales from the Livewire component or passed by controller --}}
    @if (!empty($availableLocales) && count($availableLocales) > 1)
        <ul class="navbar-nav flex-row align-items-center">
            <li class="nav-item dropdown">
                @php
                    $currentLocale = app()->getLocale();
                    $currentLocaleData = $availableLocales[$currentLocale] ?? null;
                    $currentFlagCode = $currentLocaleData && isset($currentLocaleData['flag_code']) ? $currentLocaleData['flag_code'] : (str_starts_with($currentLocale, 'en') ? 'us' : 'my');
                @endphp
                <a class="nav-link dropdown-toggle hide-arrow" href="#" id="languageDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fis fi-{{ $currentFlagCode }} rounded-circle me-1 fs-3"></i>
                    <span class="d-none d-md-inline">{{ $currentLocaleData['name'] ?? (str_starts_with($currentLocale, 'en') ? __('English') : __('Bahasa Melayu')) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    @foreach ($availableLocales as $localeKey => $localeData)
                        @if ($localeKey !== $currentLocale)
                            <li>
                                <a class="dropdown-item {{ $localeKey == $currentLocale ? 'active' : '' }}"
                                    href="{{ url('lang/' . $localeKey) }}">
                                    <i class="fis fi-{{ $localeData['flag_code'] ?? (str_starts_with($localeKey, 'en') ? 'us' : 'my') }} rounded-circle me-1 fs-3"></i> {{ $localeData['name'] ?? $localeKey }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        </ul>
    @endif


    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    {{-- Design Language Documentation (Section 3.1): User profile. Style w-px-40 from original theme. --}}
                    <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : ($defaultProfilePhotoUrl ?? asset('assets/img/avatars/1.png')) }}"
                        alt="{{ __('Avatar Pengguna') }}" class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="{{ Auth::check() && Route::has('profile.show') ? ($profileShowRoute ? url($profileShowRoute) : route('profile.show')) : 'javascript:void(0);' }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : ($defaultProfilePhotoUrl ?? asset('assets/img/avatars/1.png')) }}"
                                        alt="{{ __('Avatar Pengguna') }}" class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">
                                    @if (Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        {{ __('Pengguna Tetamu') }} {{-- Design Language: Formal tone --}}
                                    @endif
                                </span>
                                {{-- Design Language Documentation (Section 3.1): User profile with role badge. --}}
                                <small
                                    class="text-muted">{{ Auth::user() ? (Auth::user()->getRoleNames()->first() ?? __('Pengguna')) : __('Pengguna') }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ Auth::check() && Route::has('profile.show') ? ($profileShowRoute ? url($profileShowRoute) : route('profile.show')) : 'javascript:void(0);' }}">
                        {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-user-check. --}}
                        <i class="bi bi-person-check me-2 fs-6"></i>
                        {{-- Design Language Documentation (Section 1.2): Bahasa Melayu First. --}}
                        <span class="align-middle">{{ __('Profil Saya') }}</span>
                    </a>
                </li>
                @if (Auth::check() && $canViewAdminSettings && $adminSettingsRoute)
                <li>
                    <a class="dropdown-item" href="{{ url($adminSettingsRoute) }}">
                        <i class="bi bi-sliders me-2 fs-6"></i>
                        <span class="align-middle">{{ __('Tetapan Admin') }}</span>
                    </a>
                </li>
                @endif
                @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <li>
                        <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
                            {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-key. --}}
                            <i class='bi bi-key me-2 fs-6'></i>
                            <span class="align-middle">{{ __('Token API') }}</span>
                        </a>
                    </li>
                @endif
                {{-- Billing link commented out - likely not applicable for MOTAC internal system --}}

                @if (Auth::User() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        {{-- Design Language Documentation (Section 1.2): Bahasa Melayu First. --}}
                        <h6 class="dropdown-header">{{ __('Urus Pasukan') }}</h6>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
                            {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-settings. --}}
                            <i class='bi bi-gear me-2 fs-6'></i>
                            <span class="align-middle">{{ __('Tetapan Pasukan') }}</span>
                        </a>
                    </li>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <li>
                            <a class="dropdown-item" href="{{ route('teams.create') }}">
                                {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-user. --}}
                                <i class='bi bi-people me-2 fs-6'></i>
                                <span class="align-middle">{{ __('Cipta Pasukan Baharu') }}</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <lI>
                        {{-- Design Language Documentation (Section 1.2): Bahasa Melayu First. --}}
                        <h6 class="dropdown-header">{{ __('Tukar Pasukan') }}</h6>
                    </lI>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    @if (Auth::user())
                        @foreach (Auth::user()->allTeams() as $team)
                            {{-- Ensure x-switchable-team component uses Bootstrap Icons if it has icons --}}
                            <x-switchable-team :team="$team" />
                        @endforeach
                    @endif
                @endif
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                @if (Auth::check())
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-logout. --}}
                            <i class='bi bi-box-arrow-right me-2 fs-6'></i>
                            <span class="align-middle">{{ __('Log Keluar') }}</span>
                        </a>
                    </li>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                        @csrf
                    </form>
                @else
                    <li>
                        <a class="dropdown-item"
                            href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                            {{-- Design Language Documentation (Section 2.4): Use Bootstrap Icons. Changed from ti-login. --}}
                            <i class='bi bi-box-arrow-left me-2 fs-6'></i>
                            <span class="align-middle">{{ __('Log Masuk') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>

@if (!isset($navbarDetached))
    </div>
@endif
</nav>
