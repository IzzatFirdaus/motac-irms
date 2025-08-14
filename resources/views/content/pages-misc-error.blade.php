@php
  // $configData is used for theme-specific illustration paths from the original template.
  // This might be simplified or changed based on how MOTAC handles themed assets.
  // If $configData or Helper::appClasses() is not part of your MOTAC setup,
  // you might hardcode paths or use a different configuration method.
  // For MOTAC, you might not need dynamic style-based image paths unless you have distinct visual themes.
  // $configData = Helper::appClasses();
  // Fallback if Helper or $configData['style'] is not available
  $illustrationStyle = isset($configData) ? $configData['style'] : 'light';
@endphp

@extends('layouts.app') {{-- Or layouts.blankLayout if a full-page error without nav is desired --}}
                         {{-- Ensure this layout loads Noto Sans and your MOTAC themed Bootstrap 5 CSS --}}

@section('title', __('Halaman Tidak Ditemui')) {{-- Translated title --}}

@section('page-style')
  {{-- Review this CSS. If page-misc.css from a generic theme conflicts with MOTAC's design,
       its styles might need to be overridden or the page rebuilt using MOTAC theme classes. --}}
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
  <style>
    /* Add any MOTAC-specific overrides for error pages here if needed */
    .misc-wrapper .display-5 { /* Targeting the h2 */
        color: var(--bs-danger); /* Example: Using Bootstrap danger color for the error code */
    }
    .motac-error-icon { /* Class for a potential large icon */
        font-size: 5rem;
        color: var(--bs-secondary); /* Example color */
        margin-bottom: 1rem;
    }
  </style>
@endsection


@section('content')
  {{-- The container-xxl and container-p-y classes are fine if they are part of your layout's standard content area.
       For a blank layout, you might need to structure the centering differently. --}}
  <div class="container-xxl container-p-y">
    <div class="misc-wrapper text-center">
      {{-- Optional: A large, clear icon --}}
      {{-- <div class="motac-error-icon"><i class="bi bi-exclamation-diamond-fill"></i></div> --}}

      <h1 class="display-1 fw-bolder mb-2">404</h1> {{-- Common practice to show the error code --}}
      <h2 class="mb-2 mt-4 display-5 fw-bold">{{ __('Halaman Tidak Ditemui') }}</h2>
      <p class="mb-4 mx-auto col-md-8 col-lg-6">
        {{ __('Harap maaf, halaman yang anda cari tidak dapat ditemui pada pelayan ini. Sila semak URL atau kembali ke halaman utama.') }} {{-- Formalized and translated message --}}
      </p>
      <a href="{{url('/')}}" class="btn btn-primary d-inline-flex align-items-center"> {{-- MOTAC Primary Button --}}
        <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Halaman Utama') }}
      </a>
      <div class="mt-4">
        {{--
            ACTION REQUIRED: Replace this generic illustration.
            Consider a MOTAC-branded graphic, a more abstract "not found" image,
            or remove it for a simpler text-focused error page.
            The path below is a placeholder assuming a MOTAC-specific illustration.
        --}}
        <img src="{{ asset('assets/img/illustrations/motac_page_not_found.png') }}" {{-- Placeholder path --}}
             alt="{{ __('Ilustrasi Halaman Tidak Ditemui') }}"
             width="300" {{-- Adjusted width --}}
             class="img-fluid">
      </div>
    </div>
  </div>
  {{-- The misc-bg-wrapper and its image are part of a specific theme's background styling.
       For MOTAC, this might be removed in favor of a cleaner background defined by blankLayout or your MOTAC theme.
       If kept, ensure the image path and $illustrationStyle logic are relevant to MOTAC's asset structure.
  --}}
  {{--
  <div class="container-fluid misc-bg-wrapper">
    <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$illustrationStyle.'.png') }}"
         alt="{{ __('Corak Latar Belakang Hiasan') }}"
         data-app-light-img="illustrations/bg-shape-image-light.png"
         data-app-dark-img="illustrations/bg-shape-image-dark.png">
  </div>
  --}}
@endsection
