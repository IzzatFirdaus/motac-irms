@php
    // Used to dynamically load the correct illustration if you use light/dark mode
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.layout-blank') {{-- Use the MOTAC blank layout to unify error screens --}}

@section('title', __('419 - Sesi Telah Tamat'))

@section('page-style')
    {{-- MOTAC custom or vendor error page styles --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* MOTAC Specific: Highlight the error code/title */
        .misc-wrapper .display-5 {
            color: var(--bs-warning);
        }
        .motac-error-illustration {
            max-width: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder">419</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-clock-history me-2"></i>{{ __('Sesi Telah Tamat!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Sesi anda telah tamat atau permintaan tidak sah. Sila muat semula halaman atau log masuk semula untuk meneruskan.') }}
            </p>
            <a href="{{ url('/login') }}" class="motac-btn-warning d-inline-flex align-items-center" aria-label="{{ __('Log Masuk Semula') }}">
                <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>{{ __('Log Masuk Semula') }}
            </a>
            <div class="mt-4">
                {{-- Optional: Add your own MOTAC-branded "Session Expired" illustration here --}}
                {{--
                <img src="{{ asset('assets/img/illustrations/motac-error-419' . $illustrationStyleSuffix . '.png') }}"
                    alt="{{ __('Ilustrasi Sesi Tamat') }}" width="200"
                    class="img-fluid motac-error-illustration">
                --}}
            </div>
        </div>
    </div>
    {{-- Optional background decoration for branding --}}
    {{--
    <div class="container-fluid misc-bg-wrapper">
      <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
           alt="{{ __('Corak Latar Belakang Hiasan') }}"
           data-app-light-img="illustrations/bg-shape-image-light.png"
           data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
    --}}
@endsection
