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
<div>
    @php
        // Get the current application locale key
        $appCurrentLocaleKey = app()->getLocale();
        // Get the data for the current locale from the component's public property
        $currentLocaleViewData = $this->availableLocales[$appCurrentLocaleKey] ?? null;

        // Determine the flag code for the current locale
        $viewCurrentFlagCode = $currentLocaleViewData && isset($currentLocaleViewData['flag_code'])
                            ? $currentLocaleViewData['flag_code']
                            : (str_starts_with($appCurrentLocaleKey, 'en') ? 'us' : 'my'); // Fallback logic

        // Determine the display name for the current locale
        $viewCurrentLocaleName = $currentLocaleViewData && isset($currentLocaleViewData['name'])
                            ? __($currentLocaleViewData['name']) // Translate the name
                            : Str::upper($appCurrentLocaleKey);   // Fallback to uppercase locale key
    @endphp

    @pushOnce('custom-css')
        {{-- Styles specific to this navbar component --}}
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

            .navbar-dropdown .dropdown-menu { min-width: 22rem; }
            .navbar-dropdown .dropdown-notifications-list { max-height: 350px; overflow-y: auto; }
            .object-fit-cover { object-fit: cover; }
            .motac-role-badge { font-size: 0.7rem; padding: 0.2em 0.5em; vertical-align: middle; margin-left: 0.5rem; }
        </style>
    @endPushOnce

    {{-- The class 'bg-navbar-theme' should be styled by your MOTAC theme to use var(--motac-primary) and appropriate text/icon colors --}}
    @if (!empty($this->navbarDetachedClass))
        <nav class="layout-navbar {{ $this->containerNav }} navbar navbar-expand-xl {{ $this->navbarDetachedClass }} align-items-center bg-navbar-theme"
            id="layout-navbar" aria-label="{{ __('Navigasi Atas Utama') }}">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar"
            aria-label="{{ __('Navigasi Atas Utama') }}">
            <div class="{{ $this->containerNav }}">
    @endif

    @if ($this->navbarFull ?? false)
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    {{-- Design Doc 3.1 & 7.1: MOTAC logo lockup, height ~40px. Use SVG. --}}
                    <img src="{{ asset($this->configData['templateLogoSvg'] ?? 'assets/img/logo/motac-logo-lockup.svg') }}"
                        alt="{{ __('Logo Kementerian Pelancongan, Seni dan Budaya Malaysia') }}" height="36">
                </span>
                {{-- <span class="app-brand-text demo menu-text fw-bold">{{ __(config('app.name', 'Sistem MOTAC')) }}</span> --}}
            </a>
            @unless ($this->navbarHideToggle ?? false)
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none"
                    aria-label="{{ __('Buka/Tutup Menu') }}">
                    {{-- Iconography: Design Language 2.4. Changed from ti-x --}}
                    <i class="bi bi-x fs-3 align-middle"></i>
                </a>
            @endunless
        </div>
    @endif

    @unless ($this->navbarHideToggle ?? false)
        <div
            class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                        {{ ($this->menuHorizontal ?? false) ? 'd-xl-none' : '' }}
                        {{ ($this->contentNavbar ?? false) ? 'd-xl-none' : '' }}">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)"
                aria-label="{{ __('Buka/Tutup Menu Sisi') }}">
                {{-- Iconography: Design Language 2.4. Changed from ti-menu-2 --}}
                <i class="bi bi-list fs-3"></i>
            </a>
        </div>
    @endunless

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);"
                title="{{ $this->activeTheme === 'dark' ? __('Tukar ke Mod Cerah') : __('Tukar ke Mod Gelap') }}"
                aria-label="{{ $this->activeTheme === 'dark' ? __('Tukar ke Mod Cerah') : __('Tukar ke Mod Gelap') }}"
                onclick="@this.dispatch('toggleTheme')"> {{-- Dispatch event for theme toggle --}}
                {{-- Iconography: Design Language 2.4. Changed from ti-sun/ti-moon-stars --}}
                <i class="bi {{ $this->activeTheme === 'dark' ? 'bi-sun-fill' : 'bi-moon-stars-fill' }} fs-5"></i>
            </a>
        </div>

        <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Sambungan Internet Terputus') }}">
            {{-- Iconography: Design Language 2.4. Changed from ti-wifi-off --}}
            <span class="animation-fade"><i class="bi bi-wifi-off fs-4"></i></span>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto" role="menubar">
            {{-- Removed Import Progress Bar section as the functionality was removed from the PHP component
            @if ($this->activeProgressBar && $this->percentage >= 0 && $this->percentage <= 100)
                <li wire:poll.750ms="updateProgressBar" class="nav-item mx-3" style="width: 200px;" role="none">
                    <div class="progress" style="height: 12px;"
                        title="{{ __('Proses Import Data: :percentage%', ['percentage' => $this->percentage]) }}"
                        aria-valuenow="{{ $this->percentage }}" aria-valuemin="0" aria-valuemax="100"
                        aria-label="{{ __('Kemajuan Import Data') }}">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                            style="width: {{ $this->percentage }}%;">
                            <small>{{ $this->percentage > 0 ? $this->percentage.'%':'' }}</small>
                        </div>
                    </div>
                </li>
            @endif
            --}}

            <li class="nav-item dropdown-language dropdown me-2 me-xl-1" role="none">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    role="menuitem" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Pilihan Bahasa') }}">
                    <i class="fi fi-{{ $viewCurrentFlagCode }} fis rounded-circle me-1 fs-4"></i> {{-- Use corrected flag code --}}
                    <span class="d-none d-sm-inline-block">{{ $viewCurrentLocaleName }}</span> {{-- Use corrected display name --}}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" role="menu"
                    aria-labelledby="{{ __('Pilihan Bahasa') }}">
                    @foreach ($this->availableLocales as $localeKey => $localeData)
                        @if ($localeData['display'] ?? false) {{-- Check if locale should be displayed --}}
                            <li role="none">
                                {{-- Use app()->getLocale() for comparison --}}
                                <a class="dropdown-item {{ app()->getLocale() === $localeKey ? 'active' : '' }}"
                                    href="{{ route('language.swap', ['locale' => $localeKey]) }}"
                                    aria-label="{{ __($localeData['name']) }}" hreflang="{{ $localeKey }}"
                                    lang="{{ $localeKey }}" role="menuitem">
                                    <i class="fi fi-{{ $localeData['flag_code'] ?: 'xx' }} fis rounded-circle me-2 fs-4"></i>
                                    {{ __($localeData['name']) }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>

            @auth
                {{-- Notifications Dropdown --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('Notifikasi') }}">
                        {{-- Iconography: Design Language 2.4. Changed from ti-bell --}}
                        <i class="bi bi-bell-fill fs-5"></i>
                        @if ($this->unreadNotifications->count())
                            <span
                                class="badge bg-danger rounded-pill badge-notifications">{{ $this->unreadNotifications->count() }}</span> {{-- Ensure bg-danger is MOTAC themed --}}
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0" wire:ignore.self>
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($this->unreadNotifications->count())
                                    <button wire:click.prevent='markAllNotificationsAsRead'
                                        class="btn btn-sm btn-text-secondary p-0 border-0"
                                        title="{{ __('Tandakan semua sudah dibaca') }}"
                                        aria-label="{{ __('Tandakan semua sudah dibaca') }}">
                                        {{-- Iconography: Design Language 2.4. Changed from ti-mail-opened --}}
                                        <i class="bi bi-envelope-open-fill fs-4"></i>
                                    </button>
                                @endif
                                <button wire:click.prevent='$dispatch("refreshNotifications")'
                                    class="btn btn-sm btn-text-secondary p-0 border-0 ms-2" title="{{ __('Muat Semula') }}"
                                    aria-label="{{ __('Muat Semula Notifikasi') }}">
                                    <span wire:loading.remove wire:target="refreshNotifications">
                                        {{-- Iconography: Design Language 2.4. Changed from ti-refresh --}}
                                        <i class="bi bi-arrow-clockwise fs-4"></i>
                                    </span>
                                    <span wire:loading wire:target="refreshNotifications" class="animation-rotate">
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
                                        $notificationLink = Arr::get($notificationData, 'link', '#!');
                                        $level = Arr::get($notificationData, 'level', 'info');
                                        // Ensure $icon variable from notificationData contains full Bootstrap Icon class like "bi bi-info-circle-fill"
                                        $iconClass = Arr::get($notificationData, 'icon', 'bi bi-info-circle-fill');
                                    @endphp
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item"
                                        wire:click="handleNotificationClick('{{ $notification->id }}', '{{ $notificationLink }}')"
                                        style="cursor:pointer;" role="listitem">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    {{-- Ensure bg-label-* classes align with MOTAC colors --}}
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-{{ $level }}">
                                                        {{-- Iconography: Design Language 2.4. Use $iconClass --}}
                                                        <i class="{{ $iconClass }}"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small fw-semibold">
                                                    {{ __(Arr::get($notificationData, 'title', __('Notifikasi Sistem'))) }}
                                                </h6>
                                                <p class="mb-0 small text-wrap">
                                                    {{ __(Arr::get($notificationData, 'message', __('Tiada butiran.'))) }}
                                                </p>
                                                <small
                                                    class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                <span class="badge badge-dot bg-primary"></span> {{-- Ensure bg-primary uses MOTAC primary --}}
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
                            <li class="dropdown-menu-footer border-top">
                                <a href="{{ route('notifications.index') }}"
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
                            <img src="{{ Auth::user()->profile_photo_url ?? asset($this->defaultProfilePhotoUrl ?? 'assets/img/avatars/default-avatar.png') }}"
                                alt="{{ __('Avatar :name', ['name' => Auth::user()->name]) }}"
                                class="w-px-40 h-auto rounded-circle object-fit-cover">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu"
                        aria-labelledby="{{ __('Menu Pengguna') }}">
                        <li role="none">
                            <a class="dropdown-item"
                                href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}" role="menuitem">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->profile_photo_url ?? asset($this->defaultProfilePhotoUrl ?? 'assets/img/avatars/default-avatar.png') }}"
                                                alt="{{ __('Avatar :name', ['name' => Auth::user()->name]) }}"
                                                class="w-px-40 h-auto rounded-circle object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                        {{-- MOTAC Role Badge - Ensure bg-primary uses MOTAC primary color --}}
                                        <span class="badge rounded-pill bg-primary motac-role-badge">
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
                                href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}" role="menuitem">
                                {{-- Iconography: Design Language 2.4. Changed from ti-user-circle --}}
                                <i class="bi bi-person-circle me-2 fs-6"></i> {{ __('Profil Saya') }}</a></li>

                        @if (Auth::user()->hasRole('Admin'))
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}" role="menuitem">
                                {{-- Iconography: Design Language 2.4. Changed from ti-settings --}}
                                <i class="bi bi-gear-fill me-2 fs-6"></i> {{ __('Pentadbiran Sistem') }}</a></li>
                        @endif

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();" {{-- Unique ID for form --}}
                                role="menuitem">
                                {{-- Iconography: Design Language 2.4. Changed from ti-logout --}}
                                <i class="bi bi-box-arrow-right me-2 fs-6"></i> {{ __('Log Keluar') }}
                            </a>
                            <form id="logout-form-navbar" method="POST" action="{{ route('logout') }}"
                                style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </li>
            @else {{-- If not authenticated --}}
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('login') }}" role="menuitem">
                        {{-- Iconography: Design Language 2.4. Changed from ti-login --}}
                        <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                        <span class="align-middle">{{ __('Log Masuk') }}</span>
                    </a>
                </li>
            @endauth
        </ul>
    </div>

    @if (empty($this->navbarDetachedClass) && !($this->navbarFull ?? false) )
        </div> {{-- End .container-fluid or custom $this->containerNav if navbar not detached --}}
    @endif
</nav>
</div>
