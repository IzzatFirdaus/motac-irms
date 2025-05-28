{{-- resources/views/layouts/blankLayout.blade.php --}}
{{-- Used for pages that do not require the main navigation menu or footer, e.g., login, error pages. --}}
{{-- System Design: Implies need for different layouts (e.g., for auth pages). --}}

@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  // $configData is globally available from commonMaster.blade.php
  $configData = \App\Helpers\Helpers::appClasses();

  /* Display elements for blank layout - typically customizer is hidden */
  $customizerHidden = $customizerHidden ?? $configData['customizerHidden'] ?? true;
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')

  <div class="container-fluid d-flex flex-column align-items-center justify-content-center p-0 min-vh-100">
    {{-- Design Language: Ensure content is centered for typical blank pages like auth/error --}}
    @yield('content')
  </div>
  @endsection
