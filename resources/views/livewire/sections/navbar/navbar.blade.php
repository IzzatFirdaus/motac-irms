{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
{{--
  Top Navigation Bar for MOTAC Integrated Resource Management System (Livewire Component View).
  Design Language References:
  - 1.1 Professionalism: MOTAC branding.
  - 1.2 User-Centricity: Bahasa Melayu First, clear user info & actions.
  - 2.1 Color Palette: Primary color (var(--motac-primary)) for background/accents.
  - 2.2 Typography: Noto Sans (via theme CSS).
  - 2.4 Iconography: Bootstrap Icons (bi-*).
  - 3.1 Navigation: Top Action Bar (Logo, Language Toggle, User Profile with Role Badge).
  - 5.0 Dark Mode: Theme switcher.
  - 6.2 Accessibility: aria-labels, keyboard navigable dropdowns.
--}}
<div> {{-- Root Livewire component element --}}
    @php
        // $currentLocaleData is passed from the render method of the Navbar component
        // These variables are directly from $this->currentLocaleData in the component
        $viewCurrentFlagCode = $currentLocaleData['flag_code'];
        $viewCurrentLocaleName = $currentLocaleData['name'];
    @endphp

    @pushOnce('custom-css')
        <style>
            .animation-fade {
                animation: fade 2s infinite;
            }

            .animation-rotate {
                animation: rotation 2s infinite linear;
            }

            @keyframes fade {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.3;
                }
            }

            @keyframes rotation {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .navbar-dropdown .dropdown-menu {
                min-width: 22rem;
            }

            .navbar-dropdown .dropdown-notifications-list {
                max-height: 350px;
                overflow-y: auto;
            }

            .object-fit-cover {
                object-fit: cover;
            }

            .motac-role-badge {
                font-size: 0.7rem;
                padding: 0.2em 0.5em;
                vertical-align: middle;
                margin-left: 0.5rem;
            }

            .navbar-nav .nav-link i.fi {
                line-height: 1;
                /* Better alignment for flag icons */
            }
        </style>
    @endPushOnce

    @if (!empty($this->navbarDetachedClass))
        {{-- Check if navbar is detached --}}
        <nav class="layout-navbar {{ $this->containerNav }} navbar navbar-expand-xl {{ $this->navbarDetachedClass }} align-items-center bg-navbar-theme"
            id="layout-navbar" aria-label="{{ __('Navigasi Atas Utama') }}">
        @else
            {{-- Navbar is not detached, opens an inner container --}}
            <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar"
                aria-label="{{ __('Navigasi Atas Utama') }}">
                <div class="{{ $this->containerNav }}"> {{-- This div is closed after navbar-nav-right if not detached --}}
    @endif

    {{-- Conditional Brand Logo --}}
    @if ($this->navbarFull)
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    {{-- Design Language Section 3.1: Top Action Bar - MOTAC logo lockup (e.g., 40px height). --}}
                    {{-- Uses appLogo from $configData (Helpers.php) with a specific fallback if needed. --}}
                    <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo-lockup.svg') }}"
                        alt="{{ __('Logo Kementerian Pelancongan, Seni dan Budaya Malaysia') }}" height="40">
                    {{-- Adjusted height to 40px --}}
                </span>
            </a>
            {{-- This specific toggler might be part of a theme feature to close menu from brand area on mobile --}}
            @unless ($this->navbarHideToggle)
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none"
                    aria-label="{{ __('Buka/Tutup Menu') }}">
                    <i class="bi bi-x fs-3 align-middle"></i> {{-- bi-x is usually for close, context dependent here --}}
                </a>
            @endunless
        </div>
    @endif

    {{-- Main Menu Toggler (Hamburger) --}}
    @unless ($this->navbarHideToggle)
        <div
            class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                        {{ ($configData['myLayout'] ?? 'vertical') === 'horizontal' ? 'd-xl-none' : '' }}
                        {{ $configData['contentNavbar'] ?? true ? 'd-xl-none' : '' }}">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)"
                aria-label="{{ __('Buka/Tutup Menu Sisi') }}">
                <i class="bi bi-list fs-3"></i>
            </a>
        </div>
    @endunless

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{-- Theme Switcher --}}
        <div class="navbar-nav align-items-center">
            <a wire:ignore id="motacNavbarThemeSwitcher" class="nav-link motac-manual-style-switcher hide-arrow"
                {{-- Changed class from style-switcher-toggle --}} href="javascript:void(0);" data-light-title="{{ __('Tukar ke Mod Gelap') }}"
                data-dark-title="{{ __('Tukar ke Mod Cerah') }}"
                title="{{ $this->activeTheme === 'dark' ? __('Tukar ke Mod Cerah') : __('Tukar ke Mod Gelap') }}"
                aria-label="{{ $this->activeTheme === 'dark' ? __('Tukar ke Mod Cerah') : __('Tukar ke Mod Gelap') }}"
                onclick="globalToggleAppTheme(); event.preventDefault();">
                <i class="bi {{ $this->activeTheme === 'dark' ? 'bi-sun-fill' : 'bi-moon-stars-fill' }} fs-5"></i>
            </a>
        </div>

        {{-- Offline Indicator --}}
        <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Sambungan Internet Terputus') }}">
            <span class="animation-fade"><i class="bi bi-wifi-off fs-4"></i></span>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto" role="menubar">
            {{-- Language Dropdown --}}
            <li class="nav-item dropdown-language dropdown me-2 me-xl-1" role="none">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    role="menuitem" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Pilihan Bahasa') }}">
                    <span class="flag-icon flag-icon-{{ $viewCurrentFlagCode }} rounded-circle me-1"
                        style="font-size: 1.1rem; vertical-align: middle;"></span>
                    <span class="d-none d-sm-inline-block align-middle">{{ $viewCurrentLocaleName }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" role="menu"
                    aria-labelledby="{{ __('Pilihan Bahasa') }}">
                    @foreach ($this->availableLocales as $localeKey => $localeData)
                        @if ($localeData['display'] ?? false)
                            {{-- Check if locale should be displayed in switcher --}}
                            <li role="none">
                                <a class="dropdown-item {{ $currentLocaleData['key'] === $localeKey ? 'active' : '' }}"
                                    href="{{ route('language.swap', ['locale' => $localeKey]) }}"
                                    data-language="{{ $localeKey }}" {{-- Ensure this attribute is present for main.js --}}
                                    aria-label="{{ __($localeData['name']) }}" hreflang="{{ $localeKey }}"
                                    lang="{{ $localeKey }}" role="menuitem">
                                    <span
                                        class="flag-icon flag-icon-{{ $localeData['flag_code'] ?: 'xx' }} rounded-circle me-2"
                                        style="font-size: 1.1rem; vertical-align: middle;"></span>
                                    <span class="align-middle">{{ __($localeData['name']) }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>

            @auth {{-- Show these items only if user is authenticated --}}
                {{-- Notifications Dropdown --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('Notifikasi') }}">
                        <i class="bi bi-bell-fill fs-5"></i>
                        @if ($this->unreadNotifications->count())
                            <span
                                class="badge bg-danger rounded-pill badge-notifications">{{ $this->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0" wire:ignore.self> {{-- wire:ignore.self for BS5 dropdown JS interop --}}
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($this->unreadNotifications->count())
                                    <button wire:click.prevent='markAllNotificationsAsRead'
                                        class="btn btn-sm btn-text-secondary p-0 border-0"
                                        title="{{ __('Tandakan semua sudah dibaca') }}"
                                        aria-label="{{ __('Tandakan semua sudah dibaca') }}">
                                        <i class="bi bi-envelope-open-fill fs-4"></i>
                                    </button>
                                @endif
                                <button wire:click.prevent='$dispatch("refreshNotifications")'
                                    class="btn btn-sm btn-text-secondary p-0 border-0 ms-2" title="{{ __('Muat Semula') }}"
                                    aria-label="{{ __('Muat Semula Notifikasi') }}">
                                    <span wire:loading.remove wire:target="$dispatch('refreshNotifications')">
                                        {{-- Adjusted wire:target --}}
                                        <i class="bi bi-arrow-clockwise fs-4"></i>
                                    </span>
                                    <span wire:loading wire:target="$dispatch('refreshNotifications')"
                                        class="animation-rotate"> {{-- Adjusted wire:target --}}
                                        <i class="bi bi-arrow-clockwise fs-4"></i>
                                    </span>
                                </button>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                @forelse ($this->unreadNotifications as $notification)
                                    @php
                                        $notificationData = $notification->data;
                                        // Securely get link, default to '#!'
                                        $notificationLink = Illuminate\Support\Arr::get(
                                            $notificationData,
                                            'link',
                                            '#!',
                                        );
                                        // Get level for styling, default to 'info'
                                        $level = Illuminate\Support\Arr::get($notificationData, 'level', 'info');
                                        // Get icon class, default to a generic info icon
                                        $iconClass = Illuminate\Support\Arr::get(
                                            $notificationData,
                                            'icon',
                                            'bi bi-info-circle-fill',
                                        );
                                    @endphp
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item"
                                        wire:click="handleNotificationClick('{{ $notification->id }}', '{{ $notificationLink }}')"
                                        style="cursor:pointer;" role="listitem">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-{{ $level }}">
                                                        <i class="{{ $iconClass }}"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small fw-semibold">
                                                    {{ __(Illuminate\Support\Arr::get($notificationData, 'title', __('Notifikasi Sistem'))) }}
                                                </h6>
                                                <p class="mb-0 small text-wrap">
                                                    {{ __(Illuminate\Support\Arr::get($notificationData, 'message', __('Tiada butiran.'))) }}
                                                </p>
                                                <small
                                                    class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                @if (!$notification->read_at)
                                                    <span class="badge badge-dot bg-primary"
                                                        title="{{ __('Belum Dibaca') }}"></span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted small py-3" role="listitem">
                                        {{ __('Tiada notifikasi baharu buat masa ini.') }}
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        @if (Auth::user()->notifications()->count() > 0)
                            {{-- Check if user has any notifications at all --}}
                            <li class="dropdown-menu-footer border-top">
                                <a href="{{ route('notifications.index') }}" {{-- Assuming 'notifications.index' is the route name --}}
                                    class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                                    {{ __('Lihat Semua Notifikasi') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- User Profile Dropdown --}}
                <li class="nav-item navbar-dropdown dropdown-user dropdown" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        aria-label="{{ __('Menu Pengguna') }}" role="menuitem" aria-haspopup="true"
                        aria-expanded="false">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_url ?? $this->defaultProfilePhotoUrl }}"
                                alt="{{ __('Avatar :name', ['name' => Auth::user()->name]) }}"
                                class="w-px-40 h-auto rounded-circle object-fit-cover">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu"
                        aria-labelledby="{{ __('Menu Pengguna') }}">
                        <li role="none">
                            <a class="dropdown-item"
                                href="{{ Route::has($this->profileShowRoute) ? route($this->profileShowRoute) : '#' }}"
                                role="menuitem">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->profile_photo_url ?? $this->defaultProfilePhotoUrl }}"
                                                alt="{{ __('Avatar :name', ['name' => Auth::user()->name]) }}"
                                                class="w-px-40 h-auto rounded-circle object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                        <span class="badge rounded-pill bg-primary motac-role-badge">
                                            {{-- Display user's first role, title-cased, or default to 'Pengguna' --}}
                                            {{ Str::title(Auth::user()->getRoleNames()->first() ?? __('Pengguna')) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ Route::has($this->profileShowRoute) ? route($this->profileShowRoute) : '#' }}"
                                role="menuitem">
                                <i class="bi bi-person-circle me-2 fs-6"></i> {{ __('Profil Saya') }}</a></li>

                        @if ($this->canViewAdminSettings && Route::has($this->adminSettingsRoute))
                            <li><a class="dropdown-item" href="{{ route($this->adminSettingsRoute) }}" role="menuitem">
                                    <i class="bi bi-gear-fill me-2 fs-6"></i> {{ __('Pentadbiran Sistem') }}</a></li>
                        @endif
                        {{-- Jetstream API and Team Management links can be added here if Jetstream is used and configured --}}
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();"
                                role="menuitem">
                                <i class="bi bi-box-arrow-right me-2 fs-6"></i> {{ __('Log Keluar') }}
                            </a>
                            <form id="logout-form-navbar" method="POST" action="{{ route('logout') }}"
                                style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </li>
            @else
                {{-- User is not authenticated, show Login link --}}
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('login') }}" role="menuitem">
                        <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                        <span class="align-middle">{{ __('Log Masuk') }}</span>
                    </a>
                </li>
            @endauth
        </ul>
    </div>

    {{-- Close .containerNav div if navbar is NOT detached --}}
    @if (empty($this->navbarDetachedClass))
</div>
@endif
</nav>
</div>
