@php
    // The $illustrationStyleSuffix helps in dynamically loading light/dark mode illustrations.
    // Ensure that your MOTAC-themed blank layout and assets are correctly configured for this.
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.blankLayout') {{-- This MUST be your MOTAC-themed blank layout --}}

@section('title', __('404 - Halaman Tidak Ditemui'))

@section('page-style')
    {{-- Review page-misc.css: Ensure styles align with MOTAC theme or override as needed. --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* MOTAC Specific Overrides for Error Pages if page-misc.css is too generic */
        .misc-wrapper .error-code {
            /* Class for the 404 number */
            font-size: 6rem;
            /* Example size */
            font-weight: bold;
            color: var(--bs-secondary);
            /* Example: Using Bootstrap secondary, themed by MOTAC */
        }

        .misc-wrapper .error-title {
            /* Class for the "Halaman Tidak Ditemui!" title */
            color: var(--bs-primary);
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder error-code">404</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold error-title">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Halaman Tidak Ditemui!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Harap maaf, halaman yang anda cuba akses tidak wujud atau telah dipindahkan. Sila semak URL atau kembali ke halaman utama.') }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Laman Utama') }}
            </a>
            {{-- Original illustration image block removed --}}
            {{--
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/motac-error-404' . $illustrationStyleSuffix . '.png') }}"
           alt="{{ __('Ilustrasi Halaman Tidak Ditemui') }}"
           width="250" class="img-fluid motac-error-illustration">
    </div>
    --}}
        </div>
    </div>

    {{-- Background shape image: Evaluate if this fits MOTAC's simpler/professional aesthetic.
     If a large standalone icon is used, this decorative background might be distracting or unnecessary.
     Consider removing it for a cleaner look focused on the message and the icon.
--}}
    {{--
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
       alt="{{ __('Corak Latar Belakang Hiasan') }}"
       data-app-light-img="illustrations/bg-shape-image-light.png"
       data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
--}}
@endsection
