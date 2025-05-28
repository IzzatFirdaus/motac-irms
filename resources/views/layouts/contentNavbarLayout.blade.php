{{-- resources/views/layouts/contentNavbarLayout.blade.php --}}
{{-- This is the main layout for authenticated pages using traditional Blade views. --}}
{{-- System Design: Implies a standard layout for content pages. --}}
{{-- Design Language: Standard Application Layout, container-fluid. --}}

@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  // $configData is globally available from commonMaster.blade.php (via Helpers::appClasses())
  $configData = \App\Helpers\Helpers::appClasses();

  /* Display elements - defaults from $configData, can be overridden by $pageConfigs */
  $contentNavbar = $contentNavbar ?? $configData['contentNavbar'] ?? true;
  $containerNav = $containerNav ?? $configData['containerNav'] ?? 'container-fluid'; // Default to fluid
  $isNavbar = $isNavbar ?? $configData['isNavbar'] ?? true;
  $isMenu = $isMenu ?? $configData['isMenu'] ?? true;
  $isFlex = $isFlex ?? $configData['isFlex'] ?? false;
  $isFooter = $isFooter ?? $configData['isFooter'] ?? true;
  $customizerHidden = $customizerHidden ?? $configData['customizerHidden'] ?? true; // Hide customizer by default

  /* HTML Classes from $configData */
  $navbarDetached = $configData['navbarDetached'] ? 'navbar-detached' : ''; // Ensure it's a class string or empty
  $menuFixed = $configData['menuFixed'] ?? true; // Already a boolean/string in Helpers.php
  $navbarFixed = $configData['navbarFixed'] ?? true;
  $footerFixed = $configData['footerFixed'] ?? false;
  $menuCollapsed = $configData['menuCollapsed'] ?? false;

  /* Content classes - defaults to 'container-fluid' from Helpers.php */
  $container = $container ?? $configData['container'] ?? 'container-fluid';
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
    <div class="layout-container">

      @if ($isMenu)
        {{-- Includes the Livewire component for the vertical menu --}}
        @livewire('sections.menu.vertical-menu')
      @endif

      <div class="layout-page">

        {{-- Jetstream Banner (if used) or custom MOTAC banner --}}
        {{-- System Design: The Big Picture mentions <x-banner /> --}}
        <x-banner />

        @if ($isNavbar)
          {{-- Includes the Livewire component for the navbar --}}
          {{-- Pass detached status. Navbar component should handle $containerNav internally if needed for its own container. --}}
          @livewire('sections.navbar.navbar', ['navbarDetached' => $navbarDetached])
        @endif
        <div class="content-wrapper">

          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
          @else
            <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- Standard padding for content area --}}
          @endif

            @yield('content') {{-- Main Blade content for traditional views --}}

          </div>
          @if ($isFooter)
            {{-- Includes the Livewire component for the footer --}}
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
