{{--
    Application Navbar (top navigation bar) - Refactored and documented.
    Expects:
      - $containerNav (Bootstrap container class)
      - $navbarDetachedClass (Extra class for navbar detachment)
      - $availableLocales (array of locales for language switcher)
      - $currentLocaleData (array for current locale info)
--}}

<!--
  IMPORTANT: Livewire components may only have ONE root HTML element.
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
        {{-- Language Switcher Dropdown (GET only, a11y-friendly) --}}
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

        {{-- User Profile Dropdown --}}
        @auth
            @include('layouts.partials.navbar.navbar-user-profile')
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
