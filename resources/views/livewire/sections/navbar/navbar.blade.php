<div>
    {{-- $configData is globally available from commonMaster.blade.php --}}
    @php
        // Props $containerNav and $navbarDetachedClass are passed from the parent layout (app.blade.php)
        // and made available via the Navbar Livewire component's public properties.
        // $navbarFull is another layout variable that might control brand visibility.
        $navbarFull = $navbarFull ?? $configData['navbarFull'] ?? false;
        // $navbarHideToggle is a theme-specific variable for controlling menu toggle visibility
        $navbarHideToggle = $navbarHideToggle ?? $configData['navbarHideToggle'] ?? false;
    @endphp

    @push('custom-css')
        <style>
            .animation-fade { animation: fade 2s infinite; }
            .animation-rotate { animation: rotation 2s infinite linear; } /* Added linear for smoother rotation */
            @keyframes fade { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
            @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        </style>
    @endpush

    {{-- Open Nav based on $navbarDetachedClass (prop from component, originating from app.blade.php) --}}
    @if ($navbarDetachedClass === 'navbar-detached')
        <nav class="layout-navbar {{ $this->containerNav }} navbar navbar-expand-xl {{ $this->navbarDetachedClass }} align-items-center bg-navbar-theme" id="layout-navbar">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="{{ $this->containerNav }}"> {{-- container-fluid or container-xxl --}}
    @endif

    {{-- App Brand (conditionally displayed based on $navbarFull, often for horizontal layouts) --}}
    @if ($navbarFull)
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    {{-- Ensure _partials.macros provides MOTAC logo --}}
                    @include('_partials.macros', ['height' => 20, 'width' => 20]) {{-- Added width for better control --}}
                </span>
                <span class="app-brand-text demo menu-text fw-bold">{{ $configData['templateName'] ?? config('app.name') }}</span>
            </a>
        </div>
    @endif

    {{-- Menu Toggler --}}
    @if (!$navbarHideToggle)
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none"> {{-- Simplified conditional classes --}}
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-sm"></i>
            </a>
        </div>
    @endif

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{-- Style Switcher & Offline Indicator --}}
        <div class="navbar-nav d-flex flex-row align-items-center">
            <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);" title="{{ __('Toggle style') }}">
                <i class='ti ti-sm'></i> {{-- Icon (ti-sun/ti-moon) is set by main.js --}}
            </a>
            <div wire:offline class="text-danger" title="{{ __('You are offline') }}">
                <i class="animation-fade ti ti-wifi-off fs-4 mx-2"></i>
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            {{-- Import Progress Bar --}}
            @if ($activeProgressBar)
                <li wire:poll.1s="updateProgressBar" class="nav-item mx-3" style="width: 250px;" title="{{ __('Import in progress...') }}">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                            style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                            aria-valuemax="100">{{ $percentage }}%</div>
                    </div>
                </li>
            @else
                {{-- Display session flashed messages for import status if progress bar is not active --}}
                @if (session()->has('success'))
                    <li class="nav-item mx-3 text-success" role="alert">
                        <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
                    </li>
                @endif
                @if (session()->has('error'))
                    <li class="nav-item mx-3 text-danger" role="alert">
                        <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
                    </li>
                @endif
            @endif

            {{-- Language Switcher - System Design 3.1 LanguageController.php --}}
            <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-label="{{ __('Select Language') }}">
                    {{-- Flag is dynamically set by main.js based on HTML lang, or server-rendered --}}
                    @if (App::getLocale() == 'ar')
                        <i class="fi fi-sy fis rounded-circle me-1 fs-3"></i>
                    @elseif (App::getLocale() == 'my')
                        <i class="fi fi-my fis rounded-circle me-1 fs-3"></i>
                    @else {{-- Default to English --}}
                        <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item {{ App::getLocale() == 'en' ? 'active' : '' }}"
                            href="{{ route('language.swap', 'en') }}" data-language="en" data-text-direction="ltr">
                            <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                            <span class="align-middle">English</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ App::getLocale() == 'my' ? 'active' : '' }}"
                            href="{{ route('language.swap', 'my') }}" data-language="my" data-text-direction="ltr">
                            <i class="fi fi-my fis rounded-circle me-1 fs-3"></i>
                            <span class="align-middle">Bahasa Melayu</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ App::getLocale() == 'ar' ? 'active' : '' }}"
                            href="{{ route('language.swap', 'ar') }}" data-language="ar" data-text-direction="rtl">
                            <i class="fi fi-sy fis rounded-circle me-1 fs-3"></i>
                            <span class="align-middle">العربية</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Notifications Dropdown - System Design 4.4, 9.5 --}}
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('View notifications') }}">
                    <i class="ti ti-bell ti-md"></i>
                    @if ($unreadNotifications->count())
                        <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadNotifications->count() }}</span>
                    @endif
                </a>
                <ul wire:ignore.self class="dropdown-menu dropdown-menu-end py-0" style="min-width: 350px; max-width: 400px;">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">{{ __('Notifications') }}</h5>
                            @if ($unreadNotifications->count())
                                <button wire:click='markAllNotificationsAsRead' type="button"
                                    class="btn btn-sm btn-text-secondary p-0 me-2" title="{{ __('Mark all as read') }}">
                                    <i class="ti ti-mail-opened fs-4"></i>
                                </button>
                            @endif
                            <div wire:loading.class='animation-rotate' wire:target="refreshNotificationsHandler,markAllNotificationsAsRead,markNotificationAsRead">
                                <button wire:click.prevent='$dispatch("refreshNotifications")' type="button"
                                    class="btn btn-sm btn-text-secondary p-0" title="{{ __('Refresh Notifications') }}">
                                    <i class="ti ti-refresh fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @forelse ($unreadNotifications as $notification)
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none text-reset d-block" @if(isset($notification->data['url']) && $notification->data['url'] !== '#') wire:click.prevent="markNotificationAsRead('{{ $notification->id }}')" @endif>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    {{-- Assuming notification data includes actor_photo_url or a default --}}
                                                    <img src="{{ $notification->data['actor_photo_url'] ?? asset('assets/img/avatars/1.png') }}"
                                                        alt="{{ $notification->data['actor_name'] ?? __('User') }} Avatar" class="h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small">{{ $notification->data['title'] ?? __('Notification') }}</h6>
                                                <p class="mb-0 small text-wrap">{{ Str::limit($notification->data['message'] ?? __('No message content.'), 100) }}</p>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                <button wire:click.stop="markNotificationAsRead('{{ $notification->id }}')" type="button"
                                                    class="btn btn-xs rounded-pill btn-icon btn-outline-primary waves-effect"
                                                    title="{{ __('Mark as read') }}">
                                                    <i class="ti ti-mail-opened ti-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="border-top">
                                    <p class="d-flex justify-content-center text-muted m-3 p-2 h-px-40 align-items-center text-center">
                                        {{ __('No new notifications.') }}
                                    </p>
                                </li>
                            @endforelse
                        </ul>
                    </li>
                    @if ($unreadNotifications->isNotEmpty() || (Auth::user() && Auth::user()->notifications()->count() > 0))
                    <li class="dropdown-menu-footer border-top">
                        <a href="{{ route('notifications.index') }}"
                            class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                            {{ __('View all notifications') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            {{-- User Dropdown - System Design 4.1 (User model) --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-label="{{ __('User menu') }}">
                    <div class="avatar avatar-online">
                        <img src="{{ Auth::user()?->profile_photo_url ?? asset('assets/img/avatars/1.png') }}"
                            alt="{{ Auth::user()?->name ?? __('User') }} {{ __('Avatar') }}" class="w-px-40 h-auto rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item"
                            href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ Auth::user()?->profile_photo_url ?? asset('assets/img/avatars/1.png') }}"
                                            alt="{{ Auth::user()?->name ?? __('User') }} {{ __('Avatar') }}" class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">
                                        {{ Auth::user()?->name ?? __('Guest') }}
                                    </span>
                                    <small class="text-muted">{{ Auth::user()?->getRoleNames()->first() ?? __('User Role') }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ __('My Profile') }}</span>
                        </a>
                    </li>
                    @if (Auth::user()?->hasRole('Admin')) {{-- System Design 8.1 RBAC --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.users.index') }}"> {{-- System Design 9.1 --}}
                                <i class="ti ti-settings me-2 ti-sm"></i>
                                <span class="align-middle">{{ __('Settings') }}</span>
                            </a>
                        </li>
                    @endif
                    <li><div class="dropdown-divider"></div></li>
                    @if (Auth::check())
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class='ti ti-logout me-2 ti-sm'></i>
                                <span class="align-middle">{{ __('Sign out') }}</span>
                            </a>
                        </li>
                        <form method="POST" id="logout-form" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <li>
                            <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('login') }}">
                                <i class='ti ti-login me-2 ti-sm'></i>
                                <span class="align-middle">{{ __('Login') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>

    {{-- Close Div For Navbar if Not Detached --}}
    @if (!($navbarDetachedClass === 'navbar-detached'))
        </div>
    @endif
</nav>
</div>
