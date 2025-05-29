{{-- layouts/app.blade.php --}}
@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = \App\Helpers\Helpers::appClasses();

  $contentNavbar = ($contentNavbar ?? $configData['contentNavbar'] ?? true);
  $containerNav = ($containerNav ?? $configData['containerNav'] ?? 'container-fluid');
  $isNavbar = ($isNavbar ?? $configData['isNavbar'] ?? true);
  $isMenu = ($isMenu ?? $configData['isMenu'] ?? true);
  $isFlex = ($isFlex ?? $configData['isFlex'] ?? false);
  $isFooter = ($isFooter ?? $configData['isFooter'] ?? true);
  // Customizer hidden by default for MOTAC internal system as per previous logic
  $customizerHidden = ($customizerHidden ?? $configData['customizerHidden'] ?? true);

  $navbarDetached = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : ''; // Ensure $configData['navbarDetached'] is checked
  $menuFixed = (isset($configData['menuFixed']) && $configData['menuFixed'] ? 'layout-menu-fixed' : '');
  $navbarFixed = (isset($configData['navbarFixed']) && $configData['navbarFixed'] ? 'layout-navbar-fixed' : '');
  $footerFixed = (isset($configData['footerFixed']) && $configData['footerFixed'] ? 'layout-footer-fixed' : '');
  $menuCollapsed = (isset($configData['menuCollapsed']) && $configData['menuCollapsed'] ? 'layout-menu-collapsed' : '');

  $container = ($container ?? $configData['container'] ?? 'container-fluid');
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }} {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">
    <div class="layout-container">

      @if ($isMenu)
        @livewire('sections.menu.vertical-menu', ['configData' => $configData]) {{-- Pass configData if menu needs it --}}
      @endif

      <div class="layout-page">
        {{-- Per System Design, Jetstream banners are used. Ensure x-banner is correctly implemented or remove if not used. --}}
        {{-- If using Jetstream, it typically injects banners automatically or via a stack. --}}
        {{-- If it's a custom component, ensure it's correctly defined. --}}
        {{-- For now, assuming it's correctly handled by your setup or Jetstream. --}}
        @if(config('jetstream.hasProfileFeatures') || config('jetstream.hasApiFeatures')) {{-- Example condition if x-banner is Jetstream related --}}
            <x-banner />
        @endif

        @if ($isNavbar)
          {{-- Pass detached status and container preference. --}}
          @livewire('sections.navbar.navbar', [
            'navbarDetached' => $configData['navbarDetached'] ?? false,
            'containerNav' => $containerNav
          ])
        @endif
        <div class="content-wrapper">
          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
          @else
            <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- container-p-y adds padding, standard for many themes --}}
          @endif


            @if (isset($slot))
              {{ $slot }} {{-- Main Livewire page content slot --}}
            @else
              @yield('content') {{-- Fallback for traditional Blade views --}}
            @endif


          </div> {{-- End content container (either $container or $container with flex properties) --}}

          @if ($isFooter)
            @livewire('sections.footer.footer', ['containerClass' => $container]) {{-- Pass container class to footer --}}
          @endif
          <div class="content-backdrop fade"></div>
        </div> {{-- End .content-wrapper --}}
      </div> {{-- End .layout-page --}}
    </div> {{-- End .layout-container --}}

    @if ($isMenu)
      <div class="layout-overlay layout-menu-toggle"></div>
    @endif
    <div class="drag-target"></div> {{-- For mobile menu swipe --}}
  </div> {{-- End .layout-wrapper --}}
@endsection
