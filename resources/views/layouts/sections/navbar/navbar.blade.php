{{-- navbar.blade.php --}}
@php
    $containerNav = $containerNav ?? 'container-fluid'; // container-fluid for full width, container-xxl for boxed
    $navbarDetached = $navbarDetached ?? '';
@endphp

@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
        id="layout-navbar">
@endif

@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{ $containerNav }}">
@endif

@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                {{-- Design Document: MOTAC Logo and Jata Negara
            Replace with actual MOTAC logo. Jata Negara might be part of the logo image or a separate small image.
            Update: public/assets/img/logo/motac_logo.png and potentially public/assets/img/logo/jata_negara.png
        --}}
                <img src="{{ asset('assets/img/logo/motac_logo.png') }}" alt="MOTAC Logo" height="20">
                {{-- Optionally, include Jata Negara if separate and desired in navbar brand
        <img src="{{ asset('assets/img/logo/jata_negara.png') }}" alt="Jata Negara" height="20" class="ms-2">
        --}}
            </span>
            {{-- Design Document: System Title "Sistem Pengurusan BPM MOTAC" --}}
            <span
                class="app-brand-text demo menu-text fw-bold">{{ config('app.name', 'Sistem Pengurusan BPM MOTAC') }}</span>
        </a>
    </div>
@endif

@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    {{-- Design Document: Quick access links could be here or on dashboard
        If needed, this is a potential spot for a few critical icon-based quick links.
        Example:
        <ul class="navbar-nav flex-row align-items-center me-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('some.new_application_route') }}" title="{{__('New Loan Application')}}"><i class="ti ti-plus"></i></a>
            </li>
        </ul>
    --}}

    <div class="navbar-nav align-items-center">
        <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
            <i class='ti ti-sm'></i> {{-- Icon will be set by JS for sun/moon --}}
        </a>
    </div>
    {{-- Language Switcher would typically be here. Assuming it's part of the template's main.js or a dedicated livewire component if complex.
        The design document mentions: "Language switcher dropdown, now with Bahasa Melayu ('my')."
        If not handled by main.js, a simple dropdown:
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fis fi-{{ app()->getLocale() == 'en' ? 'us' : (app()->getLocale() == 'my' ? 'my' : 'sa') }} rounded-circle me-1 fs-3"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
            <li><a class="dropdown-item" href="{{ url('lang/my') }}"><i class="fis fi-my rounded-circle me-1 fs-3"></i> {{__('Bahasa Melayu')}}</a></li>
            <li><a class="dropdown-item" href="{{ url('lang/en') }}"><i class="fis fi-us rounded-circle me-1 fs-3"></i> {{__('English')}}</a></li>
             Add other languages like Arabic if needed
            <li><a class="dropdown-item" href="{{ url('lang/ar') }}"><i class="fis fi-sa rounded-circle me-1 fs-3"></i> {{__('Arabic')}}</a></li>
        </ul>
    </li>
    --}}


    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                        alt class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                                        alt class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">
                                    @if (Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        {{-- Design Document: Formal & Respectful Tone. Avoid generic placeholders like John Doe in a live system. --}}
                                        {{ __('Guest User') }}
                                    @endif
                                </span>
                                {{-- Design Document: Role-specific interfaces. Display dynamic role. --}}
                                <small
                                    class="text-muted">{{ Auth::user() ? Auth::user()->getRoleNames()->first() ?? __('User') : __('User') }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
                        <i class="ti ti-user-check me-2 ti-sm"></i>
                        {{-- Design Document: Bahasa Melayu as Primary Language --}}
                        <span class="align-middle">{{ __('Profil Saya') }}</span>
                    </a>
                </li>
                @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <li>
                        <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
                            <i class='ti ti-key me-2 ti-sm'></i>
                            <span class="align-middle">{{ __('Token API') }}</span>
                        </a>
                    </li>
                @endif
                {{-- Billing link might be irrelevant for an internal government system unless it's for specific service charges --}}
                {{-- Commenting out as per "Focused & Functional" principle if not applicable for MOTAC internal --}}
                {{--
          <li>
            <a class="dropdown-item" href="javascript:void(0);">
              <span class="d-flex align-items-center align-middle">
                <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
                <span class="flex-grow-1 align-middle">{{ __('Billing') }}</span>
                <span class="flex-shrink-0 badge badge-center rounded-pill bg-label-danger w-px-20 h-px-20">2</span>
              </span>
            </a>
          </li>
          --}}
                @if (Auth::User() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <h6 class="dropdown-header">{{ __('Urus Pasukan') }}</h6>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
                            <i class='ti ti-settings me-2'></i>
                            <span class="align-middle">{{ __('Tetapan Pasukan') }}</span>
                        </a>
                    </li>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <li>
                            <a class="dropdown-item" href="{{ route('teams.create') }}">
                                <i class='ti ti-user me-2'></i>
                                <span class="align-middle">{{ __('Cipta Pasukan Baharu') }}</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <lI>
                        <h6 class="dropdown-header">{{ __('Tukar Pasukan') }}</h6>
                    </lI>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    @if (Auth::user())
                        @foreach (Auth::user()->allTeams() as $team)
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
                            <i class='ti ti-logout me-2'></i>
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
                            <i class='ti ti-login me-2'></i>
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

{{-- resources/views/layouts/sections/navbar/navbar.blade.php --}}
{{-- <div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
        $currentLocale = app()->getLocale();
        $activeTheme = $this->activeTheme ?? ($configData['style'] ?? 'light');
        $containerNavClass = $this->containerNav ?? $configData['containerNav'] ?? 'container-fluid';
        $navbarDetachedEffectiveClass = $this->navbarDetachedClass ?? ($configData['navbarDetached'] ? 'navbar-detached' : '');
    @endphp

    @pushOnce('custom-css')
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .dropdown-notifications-list .list-group-item { cursor: pointer; }
        </style>
    @endPushOnce
{{-- }}
{{-- Begin Navbar --}}
{{-- }}    @if (!empty($navbarDetachedEffectiveClass))
        <nav class="layout-navbar {{ $containerNavClass }} navbar navbar-expand-xl {{ $navbarDetachedEffectiveClass }} align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Navigasi Utama">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Navigasi Utama">
            <div class="{{ $containerNavClass }}">
    @endif

        {{-- Logo + App Name --}}
{{-- }}        @if ($this->navbarFull ?? false)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">
                        {{ __($configData['templateName'] ?? config('variables.templateName', 'Sistem MOTAC')) }}
                    </span>
                </a>
                @unless ($this->navbarHideToggle ?? false)
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endunless
            </div>
        @endif --}}

{{-- Sidebar Toggle (Mobile) --}}
{{-- }}        @unless ($this->navbarHideToggle ?? false)
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                {{ ($this->menuHorizontal ?? false) ? ' d-xl-none ' : '' }}
                {{ ($this->contentNavbar ?? false) ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endunless --}}

{{-- Right Aligned Items --}}
{{-- }}        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            {{-- Theme Toggle --}}
{{-- }}            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);"
                   title="{{ $activeTheme === 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}"
                   onclick="@this.dispatch('toggleTheme')">
                    <i class="ti ti-sm {{ $activeTheme === 'dark' ? 'ti-sun' : 'ti-moon-stars' }}"></i>
                </a>
            </div>

            {{-- Offline Status --}}
{{-- }}            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <span class="animation-fade"><i class="ti ti-wifi-off fs-4"></i></span>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">

                {{-- Progress Bar --}}
{{-- }}                @if ($this->activeProgressBar && $this->percentage >= 0 && $this->percentage <= 100)
                    <li wire:poll.750ms="updateProgressBar" class="nav-item mx-3" style="width: 200px;">
                        <div class="progress" style="height: 12px;" title="{{ __('Proses Import Data') }} {{ $this->percentage }}%">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                                 role="progressbar"
                                 style="width: {{ $this->percentage }}%;"
                                 aria-valuenow="{{ $this->percentage }}"
                                 aria-valuemin="0" aria-valuemax="100">
                                <small>{{ $this->percentage > 0 ? $this->percentage.'%' : '' }}</small>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Language Switcher --}}
{{-- }}                <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="fi {{ $this->getLocaleFlagIcon($currentLocale) }} fis rounded-circle me-1 fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($this->availableLocales as $localeCode => $properties)
                            @if ($properties['display'] ?? false)
                                <li>
                                    <a class="dropdown-item {{ $currentLocale === $localeCode ? 'active' : '' }}"
                                       href="{{ url('lang/' . $localeCode) }}"
                                       data-language="{{ $localeCode }}">
                                        <i class="fi {{ $properties['flag_icon'] ?? ('fi-' . strtolower(substr($localeCode,0,2))) }} fis rounded-circle me-2 fs-4"></i>
                                        <span class="align-middle">{{ __($properties['name'] ?? strtoupper($localeCode)) }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                {{-- Notifications --}}
{{-- }}                @auth
                    @include('livewire.sections.navbar.partials.notifications')
                @endauth

                {{-- User Menu or Login --}}
{{-- }}                @auth
                    @include('livewire.sections.navbar.partials.user-dropdown')
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="ti ti-login me-2 ti-sm"></i>
                            <span class="align-middle">{{ __('Log Masuk') }}</span>
                        </a>
                    </li>
                @endauth

            </ul>
        </div>

    {{-- Close inner container if not detached --}}
{{-- }}    @if (empty($navbarDetachedEffectiveClass))
        </div>
    @endif
</nav>
</div> --}}
