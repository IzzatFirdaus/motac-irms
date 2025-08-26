@php
    // Illustration suffix for adaptive theming (if needed)
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.layout-blank') {{-- MOTAC-themed minimal layout --}}

@section('title', __('500 - Ralat Pelayan Dalaman'))

@section('page-style')
    {{-- Vendor and custom styles for error pages --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* Use danger color for internal server errors */
        .misc-wrapper .display-5 {
            color: var(--bs-danger);
        }
        .motac-error-illustration {
            max-width: 300px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            {{-- Error status --}}
            <h1 class="mb-2 mx-2 display-1 fw-bolder">500</h1>
            {{-- Title with server icon --}}
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-server me-2"></i>{{ __('Ralat Pelayan Dalaman') }}
            </h2>
            {{-- User-friendly message --}}
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Maaf, berlaku ralat di pihak kami. Sila cuba semula nanti atau hubungi sokongan jika masalah berterusan.') }}
            </p>
            {{-- Button to home page --}}
            <a href="{{ url('/') }}" class="motac-btn-danger d-inline-flex align-items-center" aria-label="{{ __('Laman Utama') }}">
                <i class="bi bi-house-door-fill me-2" aria-hidden="true"></i>{{ __('Laman Utama') }}
            </a>
            {{-- Optional illustration for server error --}}
            {{--
            <div class="mt-4">
                <img src="{{ asset('assets/img/illustrations/motac-error-500' . $illustrationStyleSuffix . '.png') }}"
                     alt="{{ __('Ilustrasi Ralat Server') }}" class="img-fluid motac-error-illustration">
            </div>
            --}}
        </div>
    </div>
@endsection
