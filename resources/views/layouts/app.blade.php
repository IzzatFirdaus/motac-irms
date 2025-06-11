{{-- resources/views/layouts/app.blade.php --}}
@php
    $configData = \App\Helpers\Helpers::appClasses();

    // REVISED: Prepare all necessary variables for the layout and its components.
    $container = $configData['container'] ?? 'container-fluid';
    $containerNav = $configData['containerNav'] ?? 'container-fluid';
    $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- The Livewire vertical menu component --}}
            @livewire('sections.menu.vertical-menu')

            <div class="layout-page">
                {{-- REVISED: Pass the required parameters to the Livewire navbar component. --}}
                @livewire('sections.navbar.navbar', [
                    'containerNav' => $containerNav,
                    'navbarDetachedClass' => $navbarDetached,
                ])

                {{-- Main Content Wrapper --}}
                <div class="content-wrapper" id="main-content">
                    <div class="{{ $container }} flex-grow-1 container-p-y">

                        {{-- Main content from Blade views or Livewire components is injected here --}}
                        @if (isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif

                    </div>

                    {{-- The Livewire footer component --}}
                    @livewire('sections.footer.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        {{-- Overlay for mobile menu --}}
        <div class="layout-overlay layout-menu-toggle"></div>
        {{-- Drag target for mobile menu swipe --}}
        <div class="drag-target"></div>
    </div>
@endsection
