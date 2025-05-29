{{-- resources/views/layouts/contentNavbarLayout.blade.php --}}
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
  $customizerHidden = ($customizerHidden ?? $configData['customizerHidden'] ?? true);

  $navbarDetached = ($configData['navbarDetached'] ? 'navbar-detached' : '');
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
        @livewire('sections.menu.vertical-menu', ['configData' => $configData])
      @endif

      <div class="layout-page">
        <x-banner />

        @if ($isNavbar)
          @livewire('sections.navbar.navbar', [
            'navbarDetached' => $configData['navbarDetached'] ?? false,
            'containerNav' => $containerNav
          ])
        @endif
        <div class="content-wrapper">
          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
          @else
            <div class="{{ $container }} flex-grow-1 container-p-y">
          @endif
            @yield('content') {{-- Main Blade page content --}}
          </div>
          @if ($isFooter)
            @livewire('sections.footer.footer', ['containerClass' => $container])
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
