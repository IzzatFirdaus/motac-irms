{{--
    Application Navbar (top navigation bar) - Refactored and documented.
    This version fixes the error by inlining the user profile dropdown directly here,
    removing the dependency on the now-missing 'layouts.partials.navbar.navbar-user-profile'.
    Expects:
      - $containerNav (Bootstrap container class)
      - $navbarDetachedClass (Extra class for navbar detachment)
      - $availableLocales (array of locales for language switcher)
      - $currentLocaleData (array for current locale info)
--}}

<!--
  IMPORTANT: Livewire components must have ONE root HTML element.
  This <nav> is the root for this component.
  MYDS & MyGOVEA Compliant Navigation Bar with improved accessibility
-->
<nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme {{ $containerNav }} {{ $navbarDetachedClass }}"
     id="layout-navbar"
     aria-label="{{ __('Navigasi Utama') }}"
     role="navigation">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <button class="nav-item nav-link px-0 me-xl-4 mygovea-accessible"
                type="button"
                aria-label="{{ __('Buka Menu Sisi') }}"
                title="{{ __('Buka Menu Sisi') }}"
                aria-expanded="false"
                aria-controls="layout-menu">
            <i class="ti ti-menu-2 ti-sm"></i>
        </button>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{-- Search --}}
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper">
                <label for="navbar-search" class="visually-hidden">{{ __('Cari') }}</label>
                <input type="search"
                       class="form-control search-input border-0 mygovea-accessible"
                       placeholder="{{ __('Cari...') }}"
                       aria-label="{{ __('Medan Carian') }}"
                       id="navbar-search"
                       name="search">
                <i class="ti ti-search ti-sm search-toggler cursor-pointer"
                   aria-hidden="true"></i>
            </div>
        </div>
        {{-- /Search --}}

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            {{-- Language Switcher --}}
            @if (count($availableLocales) > 1)
                <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                    <button class="nav-link dropdown-toggle hide-arrow mygovea-accessible"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-label="{{ __('Pilih Bahasa') }}"
                            title="{{ __('Pilih Bahasa') }}">
                        <i class='ti ti-language rounded-2 ti-md'></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu" aria-label="{{ __('Senarai Bahasa') }}">
                        @foreach ($availableLocales as $localeKey => $localeData)
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === $localeKey ? 'active' : '' }}"
                                   href="{{ route('language.swap', ['lang' => $localeKey]) }}"
                                   rel="nofollow"
                                   hreflang="{{ $localeKey }}"
                                   role="menuitem"
                                   @if(app()->getLocale() === $localeKey) aria-current="true" @endif>
                                    <span class="flag-icon flag-icon-{{ $localeData['flag_code'] }} me-2"></span>
                                    <span>{{ $localeKey === 'ms' ? 'Bahasa Melayu' : 'English' }}</span>
                                    @if(app()->getLocale() === $localeKey)
                                        <i class="ti ti-check ms-auto text-success" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif

            {{-- Theme Switcher --}}
            <li class="nav-item me-2 me-xl-0">
                <button type="button"
                        class="nav-link btn btn-text-secondary rounded-pill btn-icon mygovea-accessible"
                        id="theme-toggle"
                        aria-label="{{ __('Tukar Tema') }}"
                        title="{{ __('Tukar Tema') }}"
                        onclick="window.toggleAppTheme && window.toggleAppTheme()">
                    <i class='ti ti-sun-high ti-md' data-theme-icon="light"></i>
                    <i class='ti ti-moon ti-md d-none' data-theme-icon="dark"></i>
                </button>
            </li>

            {{-- Notifications --}}
            @auth
                @livewire('sections.navbar.notifications-dropdown')
            @endauth

            {{-- User Profile Dropdown --}}
            @auth
                @php
                    $currentUser = Auth::user();
                @endphp
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <button class="nav-link dropdown-toggle hide-arrow p-0 mygovea-accessible"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-label="{{ __('Menu Pengguna') }} - {{ $currentUser->name }}"
                            title="{{ __('Menu Pengguna') }}">
                        <div class="avatar avatar-online">
                            <img src="{{ $currentUser->profile_photo_url }}"
                                 alt="Avatar {{ $currentUser->name }}"
                                 class="h-auto rounded-circle"
                                 loading="lazy">
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu" aria-label="{{ __('Menu Pengguna') }}">
                        <li>
                            <div class="dropdown-item" role="presentation">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ $currentUser->profile_photo_url }}"
                                                 alt="Avatar {{ $currentUser->name }}"
                                                 class="h-auto rounded-circle"
                                                 loading="lazy">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $currentUser->name }}</h6>
                                        <small class="text-muted">
                                            {{ Str::title($currentUser->getRoleNames()->first() ?? __('Pengguna')) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider" role="separator"></div>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('profile.show') }}"
                               role="menuitem">
                                <i class='ti ti-user ti-md me-3'></i>
                                <span>{{ __('Profil Saya') }}</span>
                            </a>
                        </li>
                        @can('view-settings-admin')
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('settings.users.index') }}"
                                   role="menuitem">
                                    <i class='ti ti-settings ti-md me-3'></i>
                                    <span>{{ __('Tetapan Sistem') }}</span>
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a class="dropdown-item"
                               href="#"
                               role="menuitem">
                                <i class='ti ti-help ti-md me-3'></i>
                                <span>{{ __('Bantuan') }}</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider" role="separator"></div>
                        </li>
                        <li>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                @csrf
                            </form>
                            <a class="dropdown-item"
                               href="{{ route('logout') }}"
                               role="menuitem"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class='ti ti-power ti-md me-3'></i>
                                <span>{{ __('Log Keluar') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link mygovea-accessible"
                       href="{{ route('login') }}"
                       title="{{ __('Log Masuk') }}"
                       aria-label="{{ __('Log Masuk ke Sistem') }}">
                        <i class="ti ti-login ti-md me-2"></i>
                        <span class="d-none d-md-inline">{{ __('Log Masuk') }}</span>
                    </a>
                </li>
            @endauth
        </ul>
    </div>

    {{-- Search Overlay for Mobile --}}
    <div class="navbar-search-suggestion d-none">
        <div class="suggestion-list"></div>
    </div>
</nav>
