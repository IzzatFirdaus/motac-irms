{{-- resources/views/layouts/layout-app.blade.php --}}
{{-- Main application layout for Livewire full-page apps or those using the vertical sidebar and top navbar.
    Filename updated from app.blade.php to layout-app.blade.php as per new convention.
--}}

@php
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout variables
    $container = $configData['container'] ?? 'container-fluid';
    $containerNav = $configData['containerNav'] ?? 'container-fluid';
    $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar / Menu --}}
            @livewire('sections.menu.vertical-menu') {{-- The vertical sidebar menu --}}

            <div class="layout-page">
                {{-- Top Navbar --}}
                @livewire('sections.navbar.navbar', [
                    'containerNav' => $containerNav,
                    'navbarDetachedClass' => $navbarDetached,
                ])

                {{-- Main Page Content --}}
                <div class="content-wrapper" id="main-content">
                    <div class="{{ $container }} flex-grow-1 container-p-y">
                        {{-- Blade slot or view content --}}
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </div>

                    {{-- Footer --}}
                    @livewire('sections.footer.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        {{-- Menu Overlay for mobile/overlay states --}}
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
@endsection
