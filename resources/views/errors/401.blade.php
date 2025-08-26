@php
    // The $illustrationStyleSuffix is used to load the right illustration for light/dark mode.
    // Make sure your MOTAC-themed blank layout and assets support this.
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.layout-blank') {{-- Use the MOTAC-themed blank layout; filename updated for consistency --}}

@section('title', __('401 - Tidak Dibenarkan'))

@section('page-style')
    {{-- MOTAC custom or vendor error page styles --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* MOTAC Specific: Highlight the error code/title in red (danger) */
        .misc-wrapper .display-5 {
            color: var(--bs-danger);
        }
        .motac-error-illustration {
            max-width: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder">401</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-person-fill-lock me-2"></i>{{ __('Akses Tidak Sah!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Anda tidak mempunyai kebenaran yang sah untuk mengakses halaman ini. Sila pastikan anda telah log masuk dengan akaun yang betul atau hubungi pentadbir sistem jika anda percaya ini adalah satu kesilapan.') }}
            </p>
            <a href="{{ url('/') }}" class="motac-btn-primary d-inline-flex align-items-center" aria-label="{{ __('Kembali ke Laman Utama') }}">
                <i class="bi bi-house-door-fill me-2" aria-hidden="true"></i>{{ __('Kembali ke Laman Utama') }}
            </a>
            <div class="mt-4">
                {{-- Illustration (add your own MOTAC-branded illustration for unauthorized access) --}}
                {{-- <img src="{{ asset('assets/img/illustrations/motac-error-401' . $illustrationStyleSuffix . '.png') }}"
                    alt="{{ __('Ilustrasi Akses Tidak Dibenarkan') }}" width="200"
                    class="img-fluid motac-error-illustration"> --}}
            </div>
        </div>
    </div>
    {{-- Optionally, a background decoration (disabled by default for a clean look) --}}
    {{--
    <div class="container-fluid misc-bg-wrapper">
      <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
           alt="{{ __('Corak Latar Belakang Hiasan') }}"
           data-app-light-img="illustrations/bg-shape-image-light.png"
           data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
    --}}
@endsection
