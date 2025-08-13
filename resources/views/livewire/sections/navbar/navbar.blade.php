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
-->
<nav class="motac-navbar-alt {{ $containerNav }} {{ $navbarDetachedClass }}" id="layout-navbar" aria-label="@lang('common.main_title')">
    <div class="navbar-left">
        {{-- Hamburger menu for mobile view --}}
        <button class="navbar-hamburger" aria-label="@lang('common.toggle_sidebar')" title="@lang('common.toggle_sidebar')">
            <i class="bi bi-list"></i>
        </button>
        {{-- Brand/Logo --}}
        <a href="{{ url('/') }}" class="navbar-brand text-decoration-none">
            <span class="navbar-logo">
                <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="Logo MOTAC">
            </span>
            <span>motac-irms</span>
            <span class="navbar-ministry d-none d-lg-inline">
                {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}
            </span>
        </a>
    </div>
    {{-- Main navigation links (center) --}}
    <div class="navbar-links d-none d-lg-flex">
        {{-- Link to My Loan Applications --}}
        <a href="{{ route('loan-applications.my-applications.index') }}" class="navbar-link">
            {{ __('Permohonan Pinjaman Saya') }}
        </a>
        {{-- Link to Reports --}}
        <a href="{{ route('reports.index') }}" class="navbar-link">
            {{ __('Laporan') }}
        </a>
    </div>
    <div class="navbar-right">
        {{-- Language Switcher Dropdown --}}
        @if (count($availableLocales) > 1)
            <div class="navbar-action" tabindex="0">
                <a href="#" aria-haspopup="true" aria-expanded="false" aria-label="@lang('common.language_selector')" title="@lang('common.language_selector')">
                    <span class="bi bi-translate"></span>
                </a>
                <div class="navbar-dropdown">
                    @foreach ($availableLocales as $localeKey => $localeData)
                        <a href="{{ route('language.swap', ['lang' => $localeKey]) }}"
                           rel="nofollow"
                           hreflang="{{ $localeKey }}"
                           class="d-flex align-items-center"
                           @if(app()->getLocale() === $localeKey) aria-current="true" @endif>
                            <span class="flag-icon flag-icon-{{ $localeData['flag_code'] }}"></span>
                            {{ $localeKey === 'ms' ? 'Bahasa Melayu' : 'English' }}
                            @if(app()->getLocale() === $localeKey)
                                <i class="bi bi-check-lg ms-2 text-success"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Theme Switcher (toggle icon, not dropdown) --}}
        <div class="navbar-action" tabindex="0" x-data="{
                theme: localStorage.getItem('theme') || 'light',
                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    document.documentElement.setAttribute('data-bs-theme', this.theme);
                }
            }"
            x-init="document.documentElement.setAttribute('data-bs-theme', theme)">
            <button type="button"
                aria-label="@lang('common.toggle_theme')"
                title="@lang('common.toggle_theme')"
                @click="toggleTheme()"
                style="background: none; border: none; padding: 7px 10px; font-size: 1.21em; color: var(--motac-navbar-text);">
                <i class="bi bi-moon-stars-fill" x-show="theme === 'light'"></i>
                <i class="bi bi-sun-fill" x-show="theme === 'dark'"></i>
            </button>
        </div>

        {{-- Notifications Dropdown (Livewire component) --}}
        @auth
            @livewire('sections.navbar.notifications-dropdown')
        @endauth

        {{-- User Profile Dropdown - inlined to avoid missing partial --}}
        @auth
            @php
                $currentUser = Auth::user();
            @endphp
            <div class="navbar-action" tabindex="0">
                <a href="#" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('User Menu') }}">
                    <img src="{{ $currentUser->profile_photo_url }}"
                        alt="Avatar {{ $currentUser->name }}"
                        class="navbar-avatar">
                </a>
                <div class="navbar-dropdown">
                    <div style="padding: 16px 22px 10px 22px; border-bottom:1px solid #e6e6e6;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <img src="{{ $currentUser->profile_photo_url }}"
                                alt="Avatar {{ $currentUser->name }}"
                                class="navbar-avatar">
                            <div>
                                <strong>{{ $currentUser->name }}</strong><br>
                                <small class="text-muted" style="font-size: 0.97em;">
                                    {{ Str::title($currentUser->getRoleNames()->first() ?? __('User')) }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> {{ __('Profil Saya') }}</a>
                    @can('view-settings-admin')
                        <a href="{{ route('settings.users.index') }}"><i class="bi bi-gear"></i> {{ __('Tetapan Sistem') }}</a>
                    @endcan
                    <a href="#"><i class="bi bi-question-circle"></i> {{ __('Bantuan') }}</a>
                    <div style="border-top:1px solid #e6e6e6;"></div>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> {{ __('Log Keluar') }}
                    </a>
                </div>
            </div>
        @else
            <div class="navbar-action" tabindex="0">
                <a class="navbar-link" href="{{ route('login') }}" title="@lang('common.login')">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="d-none d-md-inline">@lang('common.login')</span>
                </a>
            </div>
        @endauth
    </div>
    {{-- Navbar CSS now recommended to be moved to a dedicated file for maintainability. --}}
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    {{-- Simple JS for dropdowns (keep for compatibility, see improvement plan to consolidate) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.navbar-action > a, .navbar-action > button').forEach(function(trigger){
                trigger.addEventListener('click', function(e){
                    e.preventDefault();
                    var parent = trigger.parentNode;
                    document.querySelectorAll('.navbar-action.show').forEach(function(a){
                        if (a !== parent) a.classList.remove('show');
                    });
                    parent.classList.toggle('show');
                });
            });
            document.addEventListener('click', function(e){
                if (!e.target.closest('.navbar-action')) {
                    document.querySelectorAll('.navbar-action.show').forEach(function(a){ a.classList.remove('show'); });
                }
            });
        });
    </script>
</nav>
