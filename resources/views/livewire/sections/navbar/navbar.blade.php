{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
<div>
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetachedClass }} align-items-center bg-navbar-theme"
        id="layout-navbar" aria-label="@lang('common.main_title')">

        {{-- Hamburger menu toggle for mobile view --}}
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4"
               href="javascript:void(0)"
               aria-label="@lang('common.toggle_sidebar')"
               title="@lang('common.toggle_sidebar')">
                <i class="bi bi-list fs-3"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">

                {{-- Language Switcher Dropdown --}}
                @if (count($availableLocales) > 1)
                    <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                        <a class="nav-link dropdown-toggle hide-arrow"
                           href="javascript:void(0);"
                           data-bs-toggle="dropdown"
                           aria-expanded="false"
                           aria-label="@lang('common.language_selector')"
                           title="@lang('common.language_selector')">
                            {{-- Display current language flag and name --}}
                            <span class="flag-icon flag-icon-{{ $currentLocaleData['flag'] ?? $currentLocaleData['flag_code'] }} rounded-circle me-1"
                                style="font-size: 1.1rem;"
                                role="img"
                                aria-label="{{ $currentLocaleData['name'] }} flag"></span>
                            <span class="d-none d-md-inline-block align-middle">
                                @if(app()->getLocale() === 'ms')
                                    @lang('common.bahasa_melayu')
                                @else
                                    @lang('common.english')
                                @endif
                            </span>
                        </a>

                        {{-- Language dropdown menu --}}
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($availableLocales as $localeKey => $localeData)
                                <li>
                                    <a class="dropdown-item {{ ($currentLocaleData['key'] ?? app()->getLocale()) === $localeKey ? 'active' : '' }}"
                                        href="{{ route('language.swap', ['lang' => $localeKey]) }}"
                                        data-language="{{ $localeKey }}"
                                        @if(($currentLocaleData['key'] ?? app()->getLocale()) === $localeKey) aria-current="true" @endif>
                                        {{-- Language flag --}}
                                        <span class="flag-icon flag-icon-{{ $localeData['flag'] ?? $localeData['flag_code'] }} rounded-circle me-2"
                                            style="font-size: 1.1rem;"
                                            role="img"
                                            aria-label="{{ $localeData['name'] }} flag"></span>
                                        {{-- Language name --}}
                                        <span class="align-middle">
                                            @if($localeKey === 'ms')
                                                Bahasa Melayu
                                            @else
                                                English
                                            @endif
                                        </span>
                                        {{-- Active indicator --}}
                                        @if(($currentLocaleData['key'] ?? app()->getLocale()) === $localeKey)
                                            <i class="bi bi-check-lg ms-auto text-success"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

                {{-- Dynamic Theme (Light/Dark Mode) Switcher --}}
                <li class="nav-item me-2 me-xl-1"
                    x-data="{
                        theme: localStorage.getItem('theme') || 'light',
                        toggleTheme() {
                            this.theme = this.theme === 'light' ? 'dark' : 'light';
                            localStorage.setItem('theme', this.theme);
                            document.documentElement.setAttribute('data-bs-theme', this.theme);
                        }
                    }"
                    x-init="document.documentElement.setAttribute('data-bs-theme', theme)">
                    <a class="nav-link hide-arrow"
                       href="javascript:void(0);"
                       @click="toggleTheme()"
                       aria-label="@lang('common.toggle_theme')"
                       title="@lang('common.toggle_theme')">
                        {{-- Sun icon for dark mode (shows when current theme is dark) --}}
                        <i class="bi bi-sun-fill fs-5"
                           x-show="theme === 'dark'"
                           x-transition:enter="transition ease-out duration-200"
                           x-transition:enter-start="opacity-0 scale-90"
                           x-transition:enter-end="opacity-100 scale-100"
                           style="display: none;"></i>
                        {{-- Moon icon for light mode (shows when current theme is light) --}}
                        <i class="bi bi-moon-stars-fill fs-5"
                           x-show="theme === 'light'"
                           x-transition:enter="transition ease-out duration-200"
                           x-transition:enter-start="opacity-0 scale-90"
                           x-transition:enter-end="opacity-100 scale-100"
                           style="display: none;"></i>
                    </a>
                </li>

                {{-- Authenticated User Section --}}
                @auth
                    {{-- Notifications dropdown component --}}
                    @livewire('sections.navbar.notifications-dropdown')

                    {{-- User profile dropdown --}}
                    @include('layouts.partials.navbar.dropdown-user-profile')
                @else
                    {{-- Login link for guest users --}}
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('login') }}"
                           title="@lang('common.login')">
                            <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                            <span class="align-middle d-none d-md-inline">@lang('common.login')</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
</div>
