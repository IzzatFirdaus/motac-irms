{{-- resources/views/livewire/sections/navbar/navbar.blade.php --}}
<div>
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetachedClass }} align-items-center bg-navbar-theme"
        id="layout-navbar" aria-label="Main Top Navigation">

        {{-- Hamburger menu toggle for mobile view --}}
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" aria-label="Toggle menu">
                <i class="bi bi-list fs-3"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">

                {{-- Language Switcher Dropdown --}}
                @if (count($availableLocales) > 1)
                    <li class="nav-item dropdown-language dropdown me-2 me-xl-1">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                            aria-expanded="false" aria-label="Language Selector">
                            <span class="flag-icon flag-icon-{{ $currentLocaleData['flag_code'] }} rounded-circle me-1"
                                style="font-size: 1.1rem;"></span>
                            <span class="d-none d-md-inline-block align-middle">{{ __($currentLocaleData['name']) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($availableLocales as $localeKey => $localeData)
                                <li>
                                    <a class="dropdown-item {{ $currentLocaleData['key'] === $localeKey ? 'active' : '' }}"
                                        href="{{ route('language.swap', ['locale' => $localeKey]) }}"
                                        data-language="{{ $localeKey }}">
                                        <span
                                            class="flag-icon flag-icon-{{ $localeData['flag_code'] }} rounded-circle me-2"
                                            style="font-size: 1.1rem;"></span>
                                        <span class="align-middle">{{ __($localeData['name']) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

                {{-- REVISED: Dynamic Theme (Light/Dark Mode) Switcher --}}
                <li class="nav-item me-2 me-xl-1"
                    x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                    x-init="document.documentElement.setAttribute('data-bs-theme', theme); $watch('theme', val => {
                        localStorage.setItem('theme', val);
                        document.documentElement.setAttribute('data-bs-theme', val);
                    })">
                    <a class="nav-link hide-arrow" href="javascript:void(0);"
                        @click="theme = (theme === 'light' ? 'dark' : 'light')" aria-label="Toggle Theme"
                        title="Toggle Theme">
                        <i class="bi bi-sun-fill fs-5" x-show="theme === 'dark'" style="display: none;"></i>
                        <i class="bi bi-moon-stars-fill fs-5" x-show="theme === 'light'" style="display: none;"></i>
                    </a>
                </li>

                {{-- Authenticated User Section --}}
                @auth
                    @livewire('sections.navbar.notifications-dropdown')
                    @include('layouts.partials.navbar.dropdown-user-profile')
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                            <span class="align-middle">{{ __('Log Masuk') }}</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
</div>
