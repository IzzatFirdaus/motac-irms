{{-- resources/views/layouts/sections/navbar/navbar.blade.php --}}
<div>
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

    {{-- Navbar container start --}}
    @if (!empty($navbarDetachedEffectiveClass))
        <nav class="layout-navbar {{ $containerNavClass }} navbar navbar-expand-xl {{ $navbarDetachedEffectiveClass }} align-items-center bg-navbar-theme" id="layout-navbar">
    @else
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="{{ $containerNavClass }}">
    @endif

        {{-- Logo and system name (for full navbar) --}}
        @if (isset($this->navbarFull) && $this->navbarFull === true)
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC') }}" height="24">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">
                        {{ __($configData['templateName'] ?? config('variables.templateName', 'Sistem MOTAC')) }}
                    </span>
                </a>
                @unless (isset($this->navbarHideToggle) && $this->navbarHideToggle === true)
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="ti ti-x ti-sm align-middle"></i>
                    </a>
                @endunless
            </div>
        @endif

        {{-- Menu toggle for mobile --}}
        @unless (isset($this->navbarHideToggle) && $this->navbarHideToggle === true)
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
                {{ isset($this->menuHorizontal) && $this->menuHorizontal ? ' d-xl-none ' : '' }}
                {{ isset($this->contentNavbar) && $this->contentNavbar ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="ti ti-menu-2 ti-sm"></i>
                </a>
            </div>
        @endunless

        {{-- Right-side navbar items --}}
        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            {{-- Theme toggle --}}
            <div class="navbar-nav align-items-center">
                <a wire:ignore class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);"
                   title="{{ $activeTheme === 'dark' ? __('Mod Cerah') : __('Mod Gelap') }}"
                   onclick="@this.dispatch('toggleTheme')">
                    <i class="ti ti-sm {{ $activeTheme === 'dark' ? 'ti-sun' : 'ti-moon-stars' }}"></i>
                </a>
            </div>

            {{-- Offline indicator --}}
            <div wire:offline class="text-danger ms-2 me-2" title="{{ __('Anda kini di luar talian.') }}">
                <span class="animation-fade"><i class="ti ti-wifi-off fs-4"></i></span>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">

                {{-- Progress bar --}}
                @if ($this->activeProgressBar && $this->percentage >= 0 && $this->percentage <= 100)
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

                {{-- Language switcher --}}
                <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="fi {{ $this->getLocaleFlagIcon($currentLocale) }} fis rounded-circle me-1 fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($this->availableLocales as $localeCode => $properties)
                            @if($properties['display'] ?? false)
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
                @auth
                    @include('livewire.sections.navbar.partials.notifications')
                @endauth

                {{-- User dropdown --}}
                @auth
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

    {{-- Close detached container --}}
    @if (empty($navbarDetachedEffectiveClass))
        </div>
    @endif
</nav>
</div>
