{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
        $currentLocale = App::getLocale(); // Current locale e.g., 'ms', 'en'
        // $activeTheme is derived from $configData['style'] which should reflect the current theme (light/dark)
        $activeTheme = $configData['style'] ?? 'light';

        // Get the flag code for the currently active locale.
        // This assumes $availableLocales is a public property from your Livewire component,
        // processed in its mount() method to include 'flag_code'.
        $currentFlagCode = $availableLocales[$currentLocale]['flag_code'] ?? 'us'; // Default to 'us' if not found
    @endphp

    @push('custom-css')
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .navbar-dropdown .dropdown-menu { min-width: 22rem; } /* Ensures notification dropdown is wide enough */
            .object-fit-cover { object-fit: cover; } /* Utility class for images */
        </style>
    @endpush

    @if (isset($navbarDetached) && $navbarDetached === 'navbar-detached')
        <nav class="layout-navbar {{ $containerNav ?? 'container-fluid' }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Top Navigation">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Top Navigation">
            <div class="{{ $containerNav ?? 'container-fluid' }}">
    @endif

        {{-- Branding: Displayed on larger screens or when navbar is not detached --}}
        @if (isset($navbarFull) && $navbarFull)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        {{-- Use config for logo path, fallback to default --}}
                        <img src="{{ asset(config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg')) }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
                </a>
                {{-- Navbar toggle (for mobile view when $navbarFull is true) --}}
                @unless(isset($navbarHideToggle) && $navbarHideToggle)
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endunless
            </div>
        @endif

        {{-- Main Layout Menu Toggle Button (Hamburger) --}}
        @unless(isset($navbarHideToggle) && $navbarHideToggle)
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 {{ isset($menuHorizontal) && $menuHorizontal ? 'd-xl-none' : '' }} {{ isset($contentNavbar) ? 'd-xl-none' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" aria-label="Toggle Menu">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endunless

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            {{-- Theme Switcher --}}
            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" title="{{ $activeTheme === 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}">
                    <i class="ti {{ $activeTheme === 'dark' ? 'ti-sun' : 'ti-moon-stars' }} ti-sm"></i>
                </a>
            </div>

            {{-- Offline Indicator (Powered by Livewire) --}}
            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <i class="animation-fade ti ti-wifi-off fs-4"></i>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto" role="menubar">
                {{-- Import Progress Bar (Display if $activeProgressBar is true and percentage is within range) --}}
                @if ($activeProgressBar && $percentage > 0 && $percentage < 100)
                    <li wire:poll.1s="updateProgressBar" class="nav-item mx-3" style="width: 200px;" role="none">
                        <div class="progress" style="height: 12px;" title="{{ __('Proses Import Data') }} {{ $percentage }}%">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                                 style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                <small>{{ $percentage }}%</small>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Language Switcher --}}
                <li class="nav-item dropdown-language dropdown me-2 me-xl-1" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" role="menuitem" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Tukar Bahasa') }}">
                        {{-- EDITED LINE: Dynamically set flag based on processed $currentFlagCode --}}
                        <i class="fi fi-{{ $currentFlagCode }} fis rounded-circle me-1 fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu">
                        {{-- $availableLocales comes from the Livewire component's public property --}}
                        @foreach ($availableLocales as $localeKey => $localeData)
                            @if($localeData['display'] ?? false)
                                <li>
                                    <a class="dropdown-item {{ $currentLocale === $localeKey ? 'active' : '' }}"
                                       href="{{ route('language.swap', ['locale' => $localeKey]) }}"
                                       aria-label="{{ $localeData['name'] }}">
                                        {{-- EDITED LINE: Use flag_code from $localeData, fallback to 'us' --}}
                                        <i class="fi fi-{{ $localeData['flag_code'] ?: 'us' }} fis rounded-circle me-2 fs-4"></i>
                                        {{ __($localeData['name']) }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                {{-- Notifications Dropdown --}}
                {{-- IMPORTANT: The "View not found" error indicates the path below is incorrect for your project. --}}
                {{-- Please VERIFY and CORRECT the path to your notifications partial. --}}
                {{-- Example: If your file is at resources/views/partials/navbar_notifications.blade.php, --}}
                {{-- then use @include('partials.navbar_notifications') --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('Notifikasi') }}">
                        <i class="ti ti-bell ti-md"></i>
                        @if ($unreadNotifications->count())
                            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0" wire:ignore.self> {{-- wire:ignore.self helps with BS dropdowns in Livewire --}}
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($unreadNotifications->count())
                                    <a wire:click.prevent='markAllNotificationsAsRead' href="javascript:void(0)" class="text-body" title="{{__('Tandakan semua sudah dibaca')}}"><i class="ti ti-mail-opened fs-4"></i></a>
                                @endif
                                {{-- Refresh button with loading state --}}
                                <a wire:click.prevent='$dispatch("refreshNotifications")' href="javascript:void(0)" class="text-body ms-2" title="{{__('Muat Semula')}}">
                                    <div wire:loading.remove wire:target="refreshNotifications"><i class="ti ti-refresh fs-4"></i></div>
                                    <div wire:loading wire:target="refreshNotifications"><span class="animation-rotate"><i class="ti ti-refresh fs-4"></i></span></div>
                                </a>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container" style="max-height: 300px; {{-- Adjust max-height as needed --}}">
                            <ul class="list-group list-group-flush">
                                @forelse ($unreadNotifications as $notification)
                                    @php
                                        $notificationData = $notification->data; // Access data once
                                        $notificationLink = Arr::get($notificationData, 'link', '#');
                                    @endphp
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item"
                                        wire:click="handleNotificationClick('{{ $notification->id }}', '{{ $notificationLink }}')"
                                        style="cursor:pointer;">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-circle bg-label-{{ Arr::get($notificationData, 'level', 'info') }}">
                                                        <i class="ti {{ Arr::get($notificationData, 'icon', 'ti-info-circle') }}"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small fw-semibold">{{ __(Arr::get($notificationData, 'title', __('Notifikasi Sistem'))) }}</h6>
                                                <p class="mb-0 small text-wrap">{{ __(Arr::get($notificationData, 'message', __('Tiada butiran.'))) }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            {{-- The dot is usually for unread, but these are all unread. Click marks as read. --}}
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                                {{-- <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a> --}}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted small py-3">
                                        {{ __('Tiada notifikasi baharu buat masa ini.') }}
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        @if(Auth::check() && Auth::user()->notifications()->count() > 0) {{-- Check if there are ANY notifications (read or unread) --}}
                            <li class="dropdown-menu-footer border-top">
                                <a href="{{ route('notifications.index') }}" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                                    {{ __('Lihat Semua Notifikasi') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- User Menu Dropdown --}}
                {{-- IMPORTANT: Also verify this @include path if you encounter similar "View not found" errors for it. --}}
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-label="{{ __('Profil Pengguna') }}">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_url ?? asset($defaultProfilePhotoUrl ?? 'assets/img/avatars/default-avatar.png') }}"
                                 alt="{{ Auth::user()->name ?? __('Pengguna') }}"
                                 class="w-px-40 h-auto rounded-circle object-fit-cover">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->profile_photo_url ?? asset($defaultProfilePhotoUrl ?? 'assets/img/avatars/default-avatar.png') }}"
                                                 alt="{{ Auth::user()->name ?? 'Avatar' }}"
                                                 class="w-px-40 h-auto rounded-circle object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()->name ?? __('Pengguna Tetamu') }}</span>
                                        <small class="text-muted">{{ Auth::check() ? Str::title(Auth::user()->getRoleNames()->first() ?? __('Pengguna')) : __('Pengguna') }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        {{-- Ensure profile.show route exists or use a valid alternative --}}
                        <li><a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}"><i class="ti ti-user-circle me-2 ti-sm"></i> {{ __('Profil Saya') }}</a></li>

                        @if (Auth::check() && Auth::user()->hasRole('Admin'))
                            {{-- EDITED: Point "Tetapan Sistem" to a relevant settings page, e.g., user settings. Adjust if a more general settings dashboard exists. --}}
                            <li><a class="dropdown-item" href="{{ route('settings.users.index') }}"><i class="ti ti-settings me-2 ti-sm"></i> {{ __('Tetapan Sistem') }}</a></li>
                        @endif

                        {{-- Add other links like API Keys, Billing, FAQ if applicable --}}
                        {{-- <li><a class="dropdown-item" href="#"><i class="ti ti-credit-card me-2 ti-sm"></i> {{ __('Langganan') }}</a></li> --}}

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-logout me-2 ti-sm"></i> {{ __('Log Keluar') }}
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        {{-- Search Bar (Optional, if not in $navbarFull mode but still needed) --}}
        {{-- Example:
        @if (!isset($navbarFull) && $navbarDetached === 'navbar-detached')
            <div class="navbar-nav align-items-center @if (isset($searchVisible) && $searchVisible) {{ 'ms-auto' }} @endif">
                <form class="navbar-nav-right ..." action="{{ url('app/invoice/list') }}">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="{{__('Search...')}}" aria-label="{{__('Search...')}}">
                    </div>
                </form>
            </div>
        @endif
        --}}

        {{-- Optional end toggle if navbar is detached and not $navbarFull --}}
        @if (!isset($navbarFull) && (isset($navbarDetached) && $navbarDetached === 'navbar-detached'))
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-none d-xl-block">
                <i class="ti ti-menu-2 ti-sm align-middle"></i>
            </a>
        @endif

    {{-- Closing div for $containerNav if not detached --}}
    @if (!isset($navbarDetached) || $navbarDetached === '')
        </div> {{-- End .container-fluid or custom $containerNav --}}
    @endif
    </nav>
</div>
