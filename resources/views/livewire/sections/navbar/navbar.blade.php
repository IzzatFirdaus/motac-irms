{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
        $currentLocale = App::getLocale();
        $activeTheme = $configData['style'] ?? 'light';
    @endphp

    @push('custom-css')
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; }
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .navbar-dropdown .dropdown-menu { min-width: 22rem; }
        </style>
    @endpush

    @if (isset($navbarDetached) && $navbarDetached === 'navbar-detached')
        <nav class="layout-navbar {{ $containerNav ?? 'container-fluid' }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Top Navigation">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar" aria-label="Top Navigation">
            <div class="{{ $containerNav ?? 'container-fluid' }}">
    @endif

        {{-- Branding --}}
        @if (isset($navbarFull) && $navbarFull)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset(config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg')) }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">{{ __(config('variables.templateName', 'Sistem MOTAC')) }}</span>
                </a>
                @unless(isset($navbarHideToggle))
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endunless
            </div>
        @endif

        {{-- Toggle Button --}}
        @unless(isset($navbarHideToggle))
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 {{ isset($menuHorizontal) || isset($contentNavbar) ? 'd-xl-none' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" aria-label="Toggle Menu">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endunless

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            {{-- Theme Switcher --}}
            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" title="{{ $activeTheme == 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}">
                    <i class="ti {{ $activeTheme == 'dark' ? 'ti-sun' : 'ti-moon-stars' }} ti-sm"></i>
                </a>
            </div>

            {{-- Offline Indicator --}}
            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <i class="animation-fade ti ti-wifi-off fs-4"></i>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto" role="menubar">
                {{-- Import Progress Bar --}}
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
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" role="menuitem" aria-label="{{ __('Tukar Bahasa') }}">
                        <i class="fi fi-{{ $currentLocale === 'my' ? 'my' : ($currentLocale === 'ar' ? 'sy' : 'us') }} fis rounded-circle me-1 fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu">
                        <li><a class="dropdown-item {{ $currentLocale === 'my' ? 'active' : '' }}" href="{{ route('language.swap', ['locale' => 'my']) }}"><i class="fi fi-my fis rounded-circle me-2 fs-4"></i> {{ __('Bahasa Melayu') }}</a></li>
                        <li><a class="dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('language.swap', ['locale' => 'en']) }}"><i class="fi fi-us fis rounded-circle me-2 fs-4"></i> {{ __('English') }}</a></li>
                        @if(config('app.available_locales.ar.display', false))
                            <li><a class="dropdown-item {{ $currentLocale === 'ar' ? 'active' : '' }}" href="{{ route('language.swap', ['locale' => 'ar']) }}"><i class="fi fi-sy fis rounded-circle me-2 fs-4"></i> {{ __('العربية') }}</a></li>
                        @endif
                    </ul>
                </li>

                {{-- Notifications --}}
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2" role="none">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Notifikasi') }}">
                        <i class="ti ti-bell ti-md"></i>
                        @if ($unreadNotifications->count())
                            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0" wire:ignore.self>
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">{{ __('Notifikasi') }}</h5>
                                @if ($unreadNotifications->count())
                                    <a wire:click.prevent='markAllNotificationsAsRead' href="javascript:void(0)" class="text-body" title="{{__('Tandakan semua sudah dibaca')}}"><i class="ti ti-mail-opened fs-4"></i></a>
                                @endif
                                <a wire:click.prevent='$dispatch("refreshNotifications")' href="javascript:void(0)" class="text-body ms-2" title="{{__('Muat Semula')}}">
                                    <div wire:loading.remove><i class="ti ti-refresh fs-4"></i></div>
                                    <div wire:loading><span class="animation-rotate"><i class="ti ti-refresh fs-4"></i></span></div>
                                </a>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                @forelse ($unreadNotifications as $notification)
                                    <li class="list-group-item dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    <i class="ti {{ Arr::get($notification->data, 'icon', 'ti-info-circle') }}"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small">{{ __(Arr::get($notification->data, 'title', __('Notifikasi Sistem'))) }}</h6>
                                                <p class="mb-0 small">{{ __(Arr::get($notification->data, 'message', __('Tiada butiran.'))) }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <a wire:click="markNotificationAsRead('{{ $notification->id }}')" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
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
                        @if(Auth::user()?->notifications()->count())
                            <li class="dropdown-menu-footer border-top">
                                <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary">{{ __('Lihat Semua Notifikasi') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- User Menu --}}
                <li class="nav-item dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-label="{{ __('Profil Pengguna') }}">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}"
                                 alt="{{ Auth::user()->name ?? __('Pengguna') }}"
                                 class="w-px-40 h-auto rounded-circle object-fit-cover">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : '#' }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-online me-3">
                                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}"
                                             alt="{{ Auth::user()->name ?? 'Avatar' }}"
                                             class="w-px-40 h-auto rounded-circle object-fit-cover">
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ Auth::user()->name ?? __('Pengguna Tetamu') }}</span><br>
                                        <small class="text-muted">{{ Str::title(Auth::user()->getRoleNames()->first() ?? __('Pengguna')) }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="ti ti-user-circle me-2"></i> {{ __('Profil Saya') }}</a></li>
                        @if (Auth::user()->hasRole('Admin'))
                            <li><a class="dropdown-item" href="#"><i class="ti ti-settings me-2"></i> {{ __('Tetapan Sistem') }}</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-logout me-2"></i> {{ __('Log Keluar') }}
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        {{-- Optional end toggle if detached --}}
        @if (!isset($navbarFull) && $navbarDetached === 'navbar-detached')
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-none d-xl-block">
                <i class="ti ti-menu-2 ti-sm align-middle"></i>
            </a>
        @endif

        @if (!isset($navbarDetached) || $navbarDetached === '')
            </div>
        @endif
    </nav>
</div>
