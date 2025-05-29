{{-- layouts/app.blade.php --}}
@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  // Configuration data from Helpers, which includes Bootstrap-friendly settings
  $configData = \App\Helpers\Helpers::appClasses();

  // Layout control variables, driven by $configData
  $contentNavbar = ($contentNavbar ?? $configData['contentNavbar'] ?? true);
  $containerNav = ($containerNav ?? $configData['containerNav'] ?? 'container-fluid'); // e.g., 'container-fluid' or 'container'
  $isNavbar = ($isNavbar ?? $configData['isNavbar'] ?? true);
  $isMenu = ($isMenu ?? $configData['isMenu'] ?? true);
  $isFlex = ($isFlex ?? $configData['isFlex'] ?? false);
  $isFooter = ($isFooter ?? $configData['isFooter'] ?? true);
  $customizerHidden = ($customizerHidden ?? $configData['customizerHidden'] ?? true);

  // Theme-specific layout classes
  $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';
  $menuFixed = (isset($configData['menuFixed']) && $configData['menuFixed'] ? 'layout-menu-fixed' : '');
  $navbarFixed = (isset($configData['navbarFixed']) && $configData['navbarFixed'] ? 'layout-navbar-fixed' : '');
  $footerFixed = (isset($configData['footerFixed']) && $configData['footerFixed'] ? 'layout-footer-fixed' : '');
  $menuCollapsed = (isset($configData['menuCollapsed']) && $configData['menuCollapsed'] ? 'layout-menu-collapsed' : '');

  // Main content container class (e.g., 'container-fluid' or 'container')
  $container = ($container ?? $configData['container'] ?? 'container-fluid');
@endphp

@extends('layouts.commonMaster') {{-- This master layout should include Bootstrap CSS/JS and use $configData['bsTheme'] --}}

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }} {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">
    <div class="layout-container">

      @if ($isMenu)
        @livewire('sections.menu.vertical-menu', ['configData' => $configData])
      @endif

      <div class="layout-page">
        {{-- Jetstream banner (if used) --}}
        @if(config('jetstream.hasProfileFeatures') || config('jetstream.hasApiFeatures'))
            <x-banner />
        @endif

        @if ($isNavbar)
          @livewire('sections.navbar.navbar', [
            'navbarDetached' => $configData['navbarDetached'] ?? false,
            'containerNav' => $containerNav
          ])
        @endif

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
          {{-- Content --}}
          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
          @else
            {{-- container-p-y is a theme-specific padding class, works alongside Bootstrap container --}}
            <div class="{{ $container }} flex-grow-1 container-p-y">
          @endif

            @if (isset($slot))
              {{ $slot }} {{-- For Livewire full-page components --}}
            @else
              @yield('content') {{-- For traditional Blade views --}}
            @endif

          </div> {{-- End content container --}}
          {{-- / Content --}}

          @if ($isFooter)
            @livewire('sections.footer.footer', ['containerClass' => $container])
          @endif

          <div class="content-backdrop fade"></div>
        </div> {{-- End .content-wrapper --}}
      </div> {{-- End .layout-page --}}
    </div> {{-- End .layout-container --}}

    {{-- Overlay for mobile menu --}}
    @if ($isMenu)
      <div class="layout-overlay layout-menu-toggle"></div>
    @endif

    {{-- Drag target for mobile menu swipe --}}
    <div class="drag-target"></div>
  </div> {{-- End .layout-wrapper --}}
@endsection
