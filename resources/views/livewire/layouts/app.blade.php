{{-- resources/views/livewire/layouts/app.blade.php --}}
@extends('layouts.commonMaster')

@php
    $configData = \App\Helpers\Helpers::appClasses();

    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);

    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $navbarDetachedClass = $configData['navbarDetached'] ?? false ? 'navbar-detached' : '';
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // Prepare menuData and role for vertical-menu component
    // This logic assumes MenuServiceProvider shares 'menuData' globally.
    // If VerticalMenu.php Livewire component fetches this data itself, this specific block for $menuData might be redundant here.
    $_menuData = $menuData ?? (app()->has(\App\Providers\MenuServiceProvider::class) ? app(\App\Providers\MenuServiceProvider::class)->getMenuData() : null);
    $_currentUserRole = Auth::check() ? Auth::user()->getRoleNames()->first() : null;

@endphp

@section('layoutContent')
    @isset($pageConfigs)
        {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
    @endisset

    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">

            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', [
                    'menuData' => $_menuData, // Pass menu data
                    'role' => $_currentUserRole, // Pass current user role
                    'configData' => $configData // Pass config data for logo/name inside menu
                ])
            @endif

            <div class="layout-page">
                @include('_partials._alerts.alert-general')

                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $containerNav,
                        'navbarDetachedClass' => $navbarDetachedClass,
                         // Pass other necessary props from $configData if Navbar.php expects them directly
                        'navbarFull' => $configData['navbarFull'] ?? true,
                        'navbarHideToggle' => ($configData['myLayout'] ?? 'vertical') === 'horizontal',
                        'activeTheme' => $configData['myStyle'] ?? 'light'
                    ])
                @endif

                <div class="content-wrapper">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                            {{ $slot }}
                        </div>
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                            {{ $slot }}
                        </div>
                    @endif

                    @if ($isFooter)
                        @livewire('sections.footer.footer')
                    @endif

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        @if ($isMenu)
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif
        <div class="drag-target"></div>
    </div>
@endsection
