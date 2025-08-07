{{-- resources/views/content/pages-misc-error-page.blade.php --}}
{{--
    MOTAC - Custom Miscellaneous Error Page
    Renamed from pages-misc-error.blade.php to pages-misc-error-page.blade.php for clarity and consistency.
--}}

@php
  // $configData is used for theme-specific illustration paths from the original template.
  // If $configData or Helper::appClasses() is not part of your MOTAC setup,
  // you might hardcode paths or use a different configuration method.
  $illustrationStyle = isset($configData) ? $configData['style'] : 'light';
@endphp

@extends('layouts.app')
{{-- Use layouts.blankLayout if a full-page error without nav is desired.
     Ensure this layout loads Noto Sans and your MOTAC themed Bootstrap 5 CSS. --}}

@section('title', __('Halaman Tidak Ditemui')) {{-- Translated title for accessibility and SEO --}}

@section('page-style')
  {{-- Page-specific styles.
       If page-misc.css from a generic theme conflicts with MOTAC's design,
       its styles might need to be overridden or the page rebuilt using MOTAC theme classes. --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
  <style>
    /* MOTAC-specific overrides for error pages */
    .misc-wrapper .display-5 {
        color: var(--bs-danger); /* Use Bootstrap danger color for the error code/title */
    }
    .motac-error-icon {
        font-size: 5rem;
        color: var(--bs-secondary); /* Example color for a decorative icon */
        margin-bottom: 1rem;
    }
  </style>
@endsection

@section('content')
  <div class="container-xxl container-p-y">
    <div class="misc-wrapper text-center">
      {{-- Optional: A large, clear icon for visual emphasis --}}
      {{-- <div class="motac-error-icon"><i class="bi bi-exclamation-diamond-fill"></i></div> --}}

      <h1 class="display-1 fw-bolder mb-2">404</h1> {{-- Large error code for attention --}}
      <h2 class="mb-2 mt-4 display-5 fw-bold">{{ __('Halaman Tidak Ditemui') }}</h2>
      <p class="mb-4 mx-auto col-md-8 col-lg-6">
        {{ __('Harap maaf, halaman yang anda cari tidak dapat ditemui pada pelayan ini. Sila semak URL atau kembali ke halaman utama.') }}
      </p>
      <a href="{{ url('/') }}" class="btn btn-primary d-inline-flex align-items-center">
        <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Halaman Utama') }}
      </a>
      <div class="mt-4">
        {{-- Use a MOTAC-branded graphic or keep it simple for a professional look.
             The path below is a placeholder assuming a MOTAC-specific illustration. --}}
        <img src="{{ asset('assets/img/illustrations/motac_page_not_found.png') }}"
             alt="{{ __('Ilustrasi Halaman Tidak Ditemui') }}"
             width="300"
             class="img-fluid">
      </div>
    </div>
  </div>
  {{-- If your theme uses a background illustration, you may include it here.
       For MOTAC, prefer a clean background unless branding requires otherwise. --}}
  {{--
  <div class="container-fluid misc-bg-wrapper">
    <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$illustrationStyle.'.png') }}"
         alt="{{ __('Corak Latar Belakang Hiasan') }}"
         data-app-light-img="illustrations/bg-shape-image-light.png"
         data-app-dark-img="illustrations/bg-shape-image-dark.png">
  </div>
  --}}
@endsection
