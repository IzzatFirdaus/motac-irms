{{-- resources/views/layouts/app.blade.php --}}
{{-- Main application layout for MYDS-compliant MOTAC IRMS v4.0
     This layout applies MYDS colour tokens, typography, grid, accessibility, and component conventions.
--}}

@php
    // Helper for layout classes/config
    $configData = \App\Helpers\Helpers::appClasses();

    // Container classes for MYDS grid system
    $container = $configData['container'] ?? 'myds-container'; // Use MYDS grid container
    $containerNav = $configData['containerNav'] ?? 'myds-container';
    $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    <div class="layout-wrapper layout-content-navbar myds-bg myds-bg-white">
        <div class="layout-container myds-row">
            {{-- Sidebar / Menu using MYDS Sidebar --}}
            <nav class="myds-sidebar" aria-label="Navigasi utama">
                {{-- Sidebar header/logo --}}
                <div class="myds-sidebar-header">
                    <img src="/assets/logo-motac.svg" alt="MOTAC" height="40">
                </div>
                {{-- Sidebar menu --}}
                <ul class="myds-sidebar-menu">
                    <li class="myds-sidebar-item">
                        <a href="/dashboard" class="myds-sidebar-link myds-sidebar-link--active">
                            <i class="bi-house-door"></i>
                            <span>Papan Pemuka</span>
                        </a>
                    </li>
                    <li class="myds-sidebar-item">
                        <a href="/pinjaman" class="myds-sidebar-link">
                            <i class="bi-laptop"></i>
                            <span>Pinjaman ICT</span>
                        </a>
                    </li>
                    <li class="myds-sidebar-item">
                        <a href="/helpdesk" class="myds-sidebar-link">
                            <i class="bi-headset"></i>
                            <span>Helpdesk</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="layout-page myds-col-12 myds-col-md-9">
                {{-- Top Navbar using MYDS Navbar --}}
                <header class="myds-navbar {{ $navbarDetached }}">
                    <div class="{{ $containerNav }} myds-navbar-content">
                        <div class="myds-navbar-logo">
                            <a href="/">
                                <img src="/assets/logo-motac.svg" alt="MOTAC" height="32">
                            </a>
                        </div>
                        <nav class="myds-navbar-menu">
                            <a href="/dashboard" class="myds-navbar-link">Papan Pemuka</a>
                            <a href="/pinjaman" class="myds-navbar-link">Pinjaman ICT</a>
                            <a href="/helpdesk" class="myds-navbar-link">Helpdesk</a>
                        </nav>
                        <div class="myds-navbar-actions">
                            {{-- Example notification and user controls --}}
                            <button class="myds-button myds-button--ghost" aria-label="Notifikasi">
                                <i class="bi-bell"></i>
                                <span class="myds-badge myds-badge--danger myds-badge--sm">3</span>
                            </button>
                            {{-- User profile, language toggle, etc. --}}
                        </div>
                    </div>
                </header>

                {{-- Accessibility Skiplink --}}
                <a href="#main-content" class="myds-skip-link">Langkau ke kandungan utama</a>

                {{-- Main Page Content --}}
                <main class="content-wrapper myds-bg-white" id="main-content" tabindex="-1">
                    <div class="{{ $container }} flex-grow-1 container-p-y">
                        {{-- Blade slot or view content --}}
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </div>
                </main>

                {{-- Footer using MYDS Footer --}}
                <footer class="myds-footer">
                    <div class="myds-footer-section">
                        <div class="myds-footer-logo">
                            <img src="/assets/logo-motac.svg" alt="MOTAC" height="32">
                        </div>
                        <span>© {{ date('Y') }} Kementerian Pelancongan, Seni dan Budaya Malaysia</span>
                        <span>Kali terakhir dikemas kini: {{ date('d M Y') }}</span>
                        <nav class="myds-footer-links">
                            <a href="/dasar-privasi" class="myds-footer-link">Dasar Privasi</a>
                            <a href="/penafian" class="myds-footer-link">Penafian</a>
                        </nav>
                    </div>
                </footer>
                <div class="content-backdrop fade"></div>
            </div>
        </div>

        {{-- Menu Overlay for mobile/overlay states --}}
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
@endsection

{{--
    Notes for maintainers:
    - All containers use MYDS grid classes (myds-container, myds-row, myds-col-*)
    - Colour, typography, and spacing refer to MYDS tokens (see variables.css)
    - Sidebar and navbar structure follows MYDS component anatomy.
    - Accessibility: Skiplink, aria-labels, and focus management are present for WCAG compliance.
    - The layout is responsive: grid adapts for desktop, tablet, mobile (see MYDS-Design-Overview.md).
    - Footer uses MYDS conventions for legal, update, and contact links.
--}}
