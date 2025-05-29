@php
  // Assuming $configData is used by the layoutMaster or for theme purposes.
  // If not, this line might be specific to a particular template's helper.
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster') {{-- Or your application's primary layout, e.g., 'layouts.app' --}}

@section('title', __('Error - Page Not Found')) {{-- Translatable title --}}

@section('page-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection


@section('content')
  <div class="container-xxl container-p-y">
    <div class="misc-wrapper text-center"> {{-- Added text-center for better alignment of content --}}
      <h2 class="mb-1 mt-4 display-5 fw-bold">{{ __('Page Not Found') }}</h2>
      <p class="mb-4 mx-2">{{ __('Oops! 😖 The requested URL was not found on this server.') }}</p>
      <a href="{{url('/')}}" class="btn btn-primary mb-4">{{ __('Back to home') }}</a>
      <div class="mt-4">
        <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="{{ __('Page Not Found Illustration') }}" width="225" class="img-fluid">
      </div>
    </div>
  </div>
  {{-- This background image seems specific to a particular UI template. Ensure paths are correct for your project. --}}
  <div class="container-fluid misc-bg-wrapper">
    <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="{{ __('Background Shape') }}" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
  </div>
  @endsection
