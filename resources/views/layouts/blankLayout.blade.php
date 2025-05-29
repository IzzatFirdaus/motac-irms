{{-- resources/views/layouts/blankLayout.blade.php --}}
@isset($pageConfigs)
  {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = \App\Helpers\Helpers::appClasses();
  $customizerHidden = $customizerHidden ?? $configData['customizerHidden'] ?? true;
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
  {{-- Modified to allow content to define its own container structure if needed, or use a default full-width flex container --}}
  <div class="authentication-wrapper authentication-basic px-4"> {{-- Example: Using auth wrapper classes from a theme like Sneat --}}
    <div class="authentication-inner py-4">
        @yield('content')
    </div>
  </div>
  {{-- Fallback generic container if above structure is not suitable for all blank pages
  <div class="container-fluid d-flex flex-column align-items-center justify-content-center p-0 min-vh-100">
    @yield('content')
  </div>
  --}}
@endsection
