{{-- resources/views/layouts/app.blade.php --}}
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
            @livewire('sections.menu.vertical-menu')

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

        {{-- Menu Overlay --}}
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
@endsection
