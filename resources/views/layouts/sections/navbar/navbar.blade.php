{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
{{-- This is the Blade view for the Navbar Livewire component. --}}
{{-- System Design: Phase 3 (Navbar), Design Language: Standard Application Layout, MOTAC Branding. --}}
<div>
    @php
        // $configData is globally available or use \App\Helpers\Helpers::appClasses()
        $configData = \App\Helpers\Helpers::appClasses();
        $currentLocale = App::getLocale();
        $activeTheme = $configData['style'] ?? 'light'; // 'light' or 'dark'

        // $containerNav and $navbarDetachedClass are passed as props from the layout
        $containerNavClass = $containerNav ?? $configData['containerNav'] ?? 'container-fluid';
        $navbarDetachedEffectiveClass = $navbarDetachedClass ?? ($configData['navbarDetached'] ? 'navbar-detached' : '');

    @endphp

    @pushOnce('custom-css') {{-- Use pushOnce to avoid duplicate styles if navbar is somehow rendered multiple times --}}
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        </style>
    @endPushOnce

    @if (!empty($navbarDetachedEffectiveClass))
        <nav class="layout-navbar {{ $containerNavClass }} navbar navbar-expand-xl {{ $navbarDetachedEffectiveClass }} align-items-center bg-navbar-theme" id="layout-navbar">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="{{ $containerNavClass }}"> {{-- Container for non-detached navbar --}}
    @endif

        {{-- App Brand (Logo & Name) - Shown if $navbarFull is true (e.g. horizontal layout) --}}
        @if (isset($navbarFull) && $navbarFull === true)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
                </a>
                @if(!(isset($navbarHideToggle) && $navbarHideToggle === true))
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endif
            </div>
        @endif

        {{-- Hamburger Menu Toggle (for vertical menu on smaller screens or when navbar is not full) --}}
        @if (!(isset($navbarHideToggle) && $navbarHideToggle === true))
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                {{ isset($menuHorizontal) && $menuHorizontal ? ' d-xl-none ' : '' }}
                {{ isset($contentNavbar) && $contentNavbar ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endif

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            {{-- Style Switcher (Dark/Light Mode) --}}
            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" title="{{ $activeTheme === 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}">
                    <i class="ti ti-sm {{ $activeTheme === 'dark' ? 'ti-sun' : 'ti-moon-stars' }}"></i>
                </a>
            </div>

            {{-- Offline Indicator --}}
            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <i class="animation-fade ti ti-wifi-off fs-4"></i>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                {{-- Import Progress Bar (if active) --}}
                @if ($activeProgressBar && $percentage >= 0 && $percentage <= 100) {{-- Ensure percentage is valid --}}
                    <li wire:poll.1s="updateProgressBar" class="nav-item mx-3" style="width: 200px;">
                        <div class="progress" style="height: 12px;" title="{{ __('Proses Import Data') }} {{ $percentage }}%">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                                style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                aria-valuemax="100"><small>{{ $percentage > 0 ? $percentage.'%' : '' }}</small></div>
                        </div>
                    </li>
                @endif

                {{-- Language Dropdown --}}
                <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        @php $localeIcon = 'fi-us'; /* Default to US for 'en' */ @endphp
                        @if ($currentLocale === 'my') @php $localeIcon = 'fi-my'; @endphp
                        @elseif ($currentLocale === 'ar') @php $localeIcon = 'fi-sa'; /* Or another representative Arab country */ @endphp
                        @endif
                        <i class="fi {{ $localeIcon }} fis rounded-circle me-1 fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (config('app.available_locales', []) as $localeCode => $properties)
                            @if($properties['display'] ?? false) {{-- Check display flag from config/app.php --}}
                            <li>
                                <a class="dropdown-item {{ $currentLocale === $localeCode ? 'active' : '' }}"
                                   href="{{ url('lang/' . $localeCode) }}"
                                   data-language="{{ $localeCode }}"
                                   data-text-direction="{{ $properties['direction'] ?? 'ltr' }}">
                                    <i class="fi {{ $properties['flag_icon'] ?? ('fi-' . strtolower(substr($localeCode,0,2))) }} fis rounded-circle me-2 fs-4"></i>
                                    <span class="align-middle">{{ __($properties['name'] ?? Str::upper($localeCode)) }}</span>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                {{-- Notifications Dropdown --}}
                @if(Auth::check()) {{-- Only show notifications for authenticated users --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ti ti-bell ti-md"></i>
                        @if ($unreadNotifications && $unreadNotifications->count())
                            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul wire:ignore.self class="dropdown-menu dropdown-menu-end py-0">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($unreadNotifications && $unreadNotifications->count())
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
                                @forelse ($unreadNotifications ?? [] as $notification)
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-circle bg-label-info"><i class="ti {{ $notification->data['icon'] ?? 'ti-info-circle' }} ti-sm"></i></span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small">{{ __(Arr::get($notification->data, 'title', __('Notifikasi Sistem'))) }}</h6>
                                                <p class="mb-0 small text-wrap">{{ __(Str::limit(Arr::get($notification->data, 'message', __('Tiada butiran.')), 100)) }}</p>
                                                <small class="text-muted small">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                @if(!$notification->read_at)
                                                <a href="javascript:void(0)" wire:click.prevent="markNotificationAsRead('{{ $notification->id }}')" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <p class="d-flex justify-content-center text-muted my-3 small">
                                            {{ __('Tiada notifikasi baharu.') }}
                                        </p>
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        @if(Auth::user() && Auth::user()->notifications()->count() > 0)
                        <li class="dropdown-menu-footer border-top">
                            <a href="{{ route('notifications.index') }}"
                                class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                                {{ __('Lihat Semua Notifikasi') }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- User Profile Dropdown --}}
                @if(Auth::check())
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}" alt="{{__('Avatar Pengguna')}}" class="w-px-40 h-auto rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{-- route('profile.show') --}}"> {{-- TODO: Replace with actual user profile route if available --}}
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}" alt="{{__('Avatar Pengguna')}}" class="w-px-40 h-auto rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">
                                            {{ Auth::user()->name ?? __('Pengguna') }}
                                        </span>
                                        <small class="text-muted">{{ __(Str::title(Auth::user()->getRoleNames()->first() ?? __('Pengguna Biasa'))) }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><div class="dropdown-divider"></div></li>
                        <li>
                            {{-- TODO: Update with actual route to user's profile page --}}
                            <a class="dropdown-item" href="{{-- route('user.profile.page') --}}">
                                <i class="ti ti-user-circle me-2 ti-sm"></i>
                                <span class="align-middle">{{ __('Profil Saya') }}</span>
                            </a>
                        </li>
                        @can('view_admin_settings') {{-- Example permission for admin-only link --}}
                        <li>
                            <a class="dropdown-item" href="{{-- route('settings.index') --}}"> {{-- TODO: Link to system settings if admin --}}
                                <i class="ti ti-settings me-2 ti-sm"></i>
                                <span class="align-middle">{{ __('Tetapan Sistem') }}</span>
                            </a>
                        </li>
                        @endcan
                        <li><div class="dropdown-divider"></div></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class='ti ti-logout me-2 ti-sm'></i>
                                <span class="align-middle">{{ __('Log Keluar') }}</span>
                            </a>
                        </li>
                        <form method="POST" id="logout-form" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </li>
                @else {{-- If user is not authenticated --}}
                 <li class="nav-item">
                    <a class="nav-link" href="{{ Route::has('login') ? route('login') : url('/login') }}">
                        <i class="ti ti-login me-2 ti-sm"></i>
                        <span class="align-middle">{{ __('Log Masuk') }}</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

    @if (empty($navbarDetachedEffectiveClass))
        </div> {{-- End .containerNav if not detached --}}
    @endif
</nav>
</div>
