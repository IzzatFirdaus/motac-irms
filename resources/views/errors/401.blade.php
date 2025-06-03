@php
    // $configData and Helper::appClasses() are from the original template.
    // Their usage for 'style' might be removed if MOTAC uses a single consistent illustration set.
    // $configData = Helper::appClasses();
    // Fallback if Helper or $configData['style'] is not available for illustration paths
    $illustrationStyleSuffix = isset($configData) ? '-' . $configData['style'] : '';
@endphp

@extends('layouts.blankLayout') {{-- This MUST be your MOTAC-themed blank layout --}}

@section('title', __('401 - Tidak Dibenarkan'))

@section('page-style')
    {{-- Review page-misc.css: Ensure styles align with MOTAC theme or override as needed. --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* MOTAC Specific Overrides for Error Pages if page-misc.css is too generic */
        .misc-wrapper .display-5 {
            color: var(--bs-danger);
            /* Example: Using Bootstrap danger color for error code/title */
        }

        .motac-error-illustration {
            max-width: 250px;
            /* Control illustration size */
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder">401</h1> {{-- Prominent Error Code --}}
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-slash-circle-fill me-2"></i>{{ __('Tidak Dibenarkan!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Anda tidak mempunyai kebenaran yang sah untuk mengakses halaman ini. Sila pastikan anda telah log masuk dengan akaun yang betul atau hubungi pentadbir sistem jika anda percaya ini adalah satu kesilapan.') }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Laman Utama') }}
            </a>
            <div class="mt-4">
                {{-- ACTION REQUIRED: Replace with MOTAC-appropriate "Unauthorized" illustration --}}
                <img src="{{ asset('assets/img/illustrations/motac-error-401' . $illustrationStyleSuffix . '.png') }}"
                    {{-- Placeholder path --}} alt="{{ __('Ilustrasi Akses Tidak Dibenarkan') }}" width="200"
                    class="img-fluid motac-error-illustration">
            </div>
        </div>
    </div>

    {{-- Background shape image: Evaluate if this fits MOTAC's simpler/professional aesthetic.
     If kept, ensure path is correct and image is MOTAC-branded.
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
       alt="{{ __('Corak Latar Belakang Hiasan') }}"
       data-app-light-img="illustrations/bg-shape-image-light.png"
       data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
--}}
@endsection
