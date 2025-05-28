{{-- app.blade.php --}}
@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/commonMaster') {{-- Ensures it extends the revised commonMaster --}}

@php
  /* Display elements */
  $contentNavbar = ($contentNavbar ?? true);
  // Changed default to container-fluid for internal system (task-oriented)
  $containerNav = ($containerNav ?? 'container-fluid');
  $isNavbar = ($isNavbar ?? true);
  $isMenu = ($isMenu ?? true);
  $isFlex = ($isFlex ?? false);
  $isFooter = ($isFooter ?? true);
  $customizerHidden = ($customizerHidden ?? ''); // Template customizer - keep if used
  //$pricingModal = ($pricingModal ?? false); // Likely HRMS specific, remove if not used by MOTAC

  /* HTML Classes */
  $navbarDetached = ($configData['navbarDetached'] ?? 'navbar-detached'); // Keep from config
  $menuFixed = (isset($configData['menuFixed']) ? $configData['menuFixed'] : '');
  $navbarFixed = (isset($configData['navbarFixed']) ? $configData['navbarFixed'] : '');
  $footerFixed = (isset($configData['footerFixed']) ? $configData['footerFixed'] : '');
  $menuCollapsed = (isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '');

  /* Content classes */
  // Changed default to container-fluid
  $container = ($container ?? 'container-fluid');
@endphp

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
    <div class="layout-container">

      @if ($isMenu)
        @livewire('sections.menu.vertical-menu')
      @endif

      <div class="layout-page">

        {{-- Removed Jetstream Banner comment, assuming setup is done or not used --}}
        {{-- <x-banner /> --}}

        @if ($isNavbar)
          @livewire('sections.navbar.navbar', ['navbarDetached' => $navbarDetached])
        @endif
        <div class="content-wrapper">

          @if ($isFlex)
            <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
          @else
            <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- Standard padding for content area --}}
          @endif

            {{ $slot }} {{-- Main Livewire/Blade content slot --}}

            {{-- Remove pricingModal if not used by MOTAC system --}}
            {{--@if ($pricingModal)
            //  @include('_partials/_modals/modal-pricing')
            //@endif--}}
            </div>
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
