{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
{{-- Renders the top navigation bar. --}}
{{-- System Design: Phase 3 (Navbar), Design Language: Standard Application Layout --}}
<div>
    @php
        // $configData is globally available from commonMaster or App\Helpers\Helpers::appClasses()
        $configData = \App\Helpers\Helpers::appClasses();
        $currentLocale = App::getLocale();
        $activeTheme = $configData['style'] ?? 'light'; // 'light' or 'dark'

        // These props are passed from the layout (e.g., app.blade.php)
        // $containerNav should be 'container-fluid' for MOTAC internal system
        // $navbarDetachedClass is a string like 'navbar-detached' or empty
    @endphp

    @push('custom-css')
        {{-- Styles for animations, could be moved to a global CSS file if used elsewhere --}}
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        </style>
    @endpush

    {{-- Navbar Structure --}}
    @if (isset($navbarDetached) && $navbarDetached === 'navbar-detached')
        <nav class="layout-navbar {{ $containerNav ?? 'container-fluid' }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme" id="layout-navbar">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="{{ $containerNav ?? 'container-fluid' }}">
    @endif

        {{-- App Brand (Logo & Name) - Shown if $navbarFull is set (usually for horizontal menu layout) --}}
        @if (isset($navbarFull) && $navbarFull)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
                </a>
                @if(!isset($navbarHideToggle))
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endif
            </div>
        @endif

        {{-- Hamburger Menu Toggle (for vertical menu on smaller screens) --}}
        @if (!isset($navbarHideToggle))
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                {{ isset($menuHorizontal) ? ' d-xl-none ' : '' }}
                {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endif

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            {{-- Style Switcher (Dark/Light Mode) --}}
            {{-- Design Language: User-selectable dark mode --}}
            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" title="{{ $activeTheme == 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}">
                    @if($activeTheme == 'dark')
                        <i class='ti ti-sun ti-sm'></i>
                    @else
                        <i class='ti ti-moon-stars ti-sm'></i>
                    @endif
                </a>
            </div>

            {{-- Offline Indicator --}}
            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <i class="animation-fade ti ti-wifi-off fs-4"></i>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                {{-- Import Progress Bar (if active) --}}
                @if ($activeProgressBar && $percentage > 0 && $percentage < 100)
                    <li wire:poll.1s="updateProgressBar" class="nav-itemਡ mx-3" style="width: 200px;">
                        <div class="progress" style="height: 12px;" title="{{ __('Proses Import Data') }} {{ $percentage }}%">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                                style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                aria-valuemax="100"><small>{{ $percentage }}%</small></div>
                        </div>
                    </li>
                @endif
                {{-- Flash messages for import can be handled by the global alert system or Toastr --}}


                {{-- Language Dropdown --}}
                {{-- Design Language: Bahasa Melayu as Primary Language, English option --}}
                <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        @if ($currentLocale === 'my')
                            <i class="fi fi-my fis rounded-circle me-1 fs-3"></i>
                        @elseif ($currentLocale === 'ar')
                            <i class="fi fi-sy fis rounded-circle me-1 fs-3"></i>
                        @else {{-- Default to 'en' or others --}}
                            <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ $currentLocale === 'my' ? 'active' : '' }}" href="{{ url('lang/my') }}" data-language="my" data-text-direction="ltr">
                                <i class="fi fi-my fis rounded-circle me-2 fs-4"></i>
                                <span class="align-middle">{{ __('Bahasa Melayu') }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ url('lang/en') }}" data-language="en" data-text-direction="ltr">
                                <i class="fi fi-us fis rounded-circle me-2 fs-4"></i>
                                <span class="align-middle">{{ __('English') }}</span>
                            </a>
                        </li>
                        {{-- Conditionally display Arabic or other languages based on config --}}
                        @if(config('app.available_locales.ar.display', false)) {{-- Custom config key to control display --}}
                        <li>
                            <a class="dropdown-item {{ $currentLocale === 'ar' ? 'active' : '' }}" href="{{ url('lang/ar') }}" data-language="ar" data-text-direction="rtl">
                                <i class="fi fi-sy fis rounded-circle me-2 fs-4"></i>
                                <span class="align-middle">{{ __('العربية') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                {{-- Notifications Dropdown --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ti ti-bell ti-md"></i>
                        @if ($unreadNotifications->count())
                            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul wire:ignore.self class="dropdown-menu dropdown-menu-end py-0">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($unreadNotifications->count())
                                    <a wire:click.prevent='markAllNotificationsAsRead' href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Tandakan semua sudah dibaca')}}"><i class="ti ti-mail-opened fs-4"></i></a>
                                @endif
                                 <a wire:click.prevent='$dispatch("refreshNotifications")' href="javascript:void(0)" class="dropdown-notifications-refresh text-body ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Muat Semula Notifikasi')}}">
                                    <div wire:loading.remove wire:target='$dispatch("refreshNotifications")'><i class="ti ti-refresh fs-4"></i></div>
                                    <div wire:loading wire:target='$dispatch("refreshNotifications")'><span class="animation-rotate"><i class="ti ti-refresh fs-4"></i></span></div>
                                 </a>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                @forelse ($unreadNotifications as $notification)
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    {{-- Customize avatar based on notification type or sender if available in $notification->data --}}
                                                    <span class="avatar-initial rounded-circle bg-label-info"><i class="ti {{ Arr::get($notification->data, 'icon', 'ti-info-circle') }}"></i></span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small">{{ __(Arr::get($notification->data, 'title', __('Notifikasi Sistem'))) }}</h6>
                                                <p class="mb-0 small">{{ __(Arr::get($notification->data, 'message', __('Tiada butiran.'))) }}</p>
                                                <small class="text-muted small">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                <a href="javascript:void(0)" wire:click="markNotificationAsRead('{{ $notification->id }}')" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <p class="d-flex justify-content-center text-muted my-3">
                                            {{ __('Tiada notifikasi baharu buat masa ini.') }}
                                        </p>
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        @if(Auth::user() && Auth::user()->notifications()->count() > 0) {{-- Show link if there are ANY notifications, not just unread --}}
                        <li class="dropdown-menu-footer border-top">
                            <a href="{{ route('notifications.index') }}"
                                class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                                {{ __('Lihat Semua Notifikasi') }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                {{-- User Profile Dropdown --}}
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}" alt="{{__('Avatar Pengguna')}}" class="w-px-40 h-auto rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{-- {{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }} --}}"> {{-- TODO: Replace with actual profile route from web.php --}}
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}" alt="{{__('Avatar Pengguna')}}" class="w-px-40 h-auto rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">
                                            {{ Auth::user()->name ?? __('Pengguna Tetamu') }}
                                        </span>
                                        <small class="text-muted">{{ Auth::user() ? __(Str::title(Auth::user()->getRoleNames()->first() ?? __('Pengguna'))) : __('Peranan Tidak Ditetapkan') }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><div class="dropdown-divider"></div></li>
                        <li>
                            {{-- TODO: Link to user profile page if it exists --}}
                            <a class="dropdown-item" href="{{-- route('profile.user') --}}">
                                <i class="ti ti-user-circle me-2 ti-sm"></i>
                                <span class="align-middle">{{ __('Profil Saya') }}</span>
                            </a>
                        </li>
                        @if (Auth::user() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) {{-- Example for admin-only link --}}
                        <li>
                            <a class="dropdown-item" href="{{-- route('settings.index') --}}"> {{-- TODO: Link to settings if admin --}}
                                <i class="ti ti-settings me-2 ti-sm"></i>
                                <span class="align-middle">{{ __('Tetapan Sistem') }}</span>
                            </a>
                        </li>
                        @endif
                        <li><div class="dropdown-divider"></div></li>
                        @if (Auth::check())
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class='ti ti-logout me-2 ti-sm'></i>
                                    <span class="align-middle">{{ __('Log Keluar') }}</span>
                                </a>
                            </li>
                            <form method="POST" id="logout-form" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <li>
                                <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('/login') }}">
                                    <i class='ti ti-login me-2 ti-sm'></i>
                                    <span class="align-middle">{{ __('Log Masuk') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>

        {{-- Search input (Optional, if an advanced search is part of navbar) --}}
        {{-- <div class="navbar-search-wrapper search-input-wrapper d-none">
            <input type="text" class="form-control search-input {{ $containerNav }} border-0" placeholder="{{__('Cari...')}}" aria-label="Search...">
            <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
        </div> --}}

    @if (!isset($navbarFull) && isset($navbarDetached) && $navbarDetached === 'navbar-detached')
        {{-- This toggle is for detached navbar to show/hide menu on large screens, handled by main.js --}}
         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-none d-xl-block">
            <i class="ti ti-menu-2 ti-sm align-middle"></i>
        </a>
    @endif

    @if (!isset($navbarDetached) || $navbarDetached === '')
        </div> {{-- End .containerNav if not detached --}}
    @endif
</nav>
</div>
