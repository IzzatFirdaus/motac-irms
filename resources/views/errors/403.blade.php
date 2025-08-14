@php
    // Ensure Helper is imported or fully qualified if needed, though usually available in views
    // due to a global helper or AppServiceProvider.
    $configData = Helper::appClasses();
    // Change from $configData['style'] to $configData['myStyle']
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts/blankLayout') {{-- MOTAC-themed blank layout --}}

@section('title', __('403 - Akses Dihalang'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
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
            <h1 class="mb-2 mx-2 display-1 fw-bolder">403</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-hand-thumbs-down-fill me-2"></i>{{ __('Akses Dihalang!') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Anda tidak mempunyai kebenaran yang mencukupi untuk mengakses sumber atau halaman ini. Sila hubungi pentadbir sistem jika anda memerlukan akses.') }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-house-door-fill me-2"></i>{{ __('Kembali ke Laman Utama') }}
            </a>
            <div class="mt-4">
                {{-- ACTION REQUIRED: Replace with MOTAC-appropriate "Forbidden" illustration --}}
                <img src="{{ asset('assets/img/illustrations/motac-error-403' . $illustrationStyleSuffix . '.png') }}"
                    {{-- Placeholder path --}} alt="{{ __('Ilustrasi Akses Dihalang') }}" width="200"
                    class="img-fluid motac-error-illustration">
            </div>
        </div>
    </div>
    {{--
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
       alt="{{ __('Corak Latar Belakang Hiasan') }}"
       data-app-light-img="illustrations/bg-shape-image-light.png"
       data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
--}}
@endsection
