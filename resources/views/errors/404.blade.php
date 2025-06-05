@php
    // $configData and Helper::appClasses() are from the original template.
    // Their usage for 'myStyle' for background shapes might be removed if MOTAC uses a simpler background.
    // $configData = Helper::appClasses(); // This line can remain commented if $configData is globally available.

    // Corrected to use 'myStyle' and ensure $configData is set before accessing its keys.
    // The error "Undefined array key 'style'" implies $configData itself is set.
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
            /* Example: Using MOTAC Blue for title */
        }

        .motac-standalone-icon {
            font-size: 6rem;
            /* Adjust size as needed, display-1 might be too large here */
            margin-bottom: 1.5rem;
            /* Space below the icon */
            color: var(--bs-warning);
            /* Example: Using MOTAC warning color */
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">

            {{-- Large Standalone Bootstrap Icon replacing the illustration --}}
            <div class="mt-4 mb-4">
                <i class="bi bi-compass-fill motac-standalone-icon"></i>
                {{--
          Icon Choice: bi-compass-fill suggests "lost" or "cannot find direction".
          Alternatives:
          - bi-question-circle-fill (General query/unknown)
          - bi-search-heart-break (Search failed, more illustrative)
          - bi-signpost-split-fill (Wrong turn)
          - bi-binoculars-fill (Looking but not finding)
      --}}
            </div>

            <h1 class="mb-2 mx-2 error-code">404</h1>
            <h2 class="mb-2 display-5 fw-bold error-title">
                {{-- Icon removed from here as we have a large standalone one now --}}
                {{ __('Halaman Tidak Ditemui!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Harap maaf, halaman yang anda cuba akses tidak wujud atau telah dipindahkan. Sila semak URL atau kembali ke halaman utama.') }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Halaman Utama') }}
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
