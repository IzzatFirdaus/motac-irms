@php
    // Suffix for light/dark illustrations if utilized
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.layout-blank') {{-- Minimal error layout --}}

@section('title', __('429 - Terlalu Banyak Permintaan'))

@section('page-style')
    {{-- Standard error page CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* Info color for rate limit messages */
        .misc-wrapper .display-5 {
            color: var(--bs-info);
        }
        .motac-error-illustration {
            max-width: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            {{-- Large status code --}}
            <h1 class="mb-2 mx-2 display-1 fw-bolder">429</h1>
            {{-- Title and icon --}}
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-speedometer2 me-2"></i>{{ __('Terlalu Banyak Permintaan') }}
            </h2>
            {{-- Advice to user --}}
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Anda telah menghantar terlalu banyak permintaan dalam masa singkat. Sila tunggu sebentar dan cuba lagi.') }}
            </p>
            {{-- Option to retry --}}
            <a href="javascript:location.reload()" class="motac-btn-info d-inline-flex align-items-center" aria-label="{{ __('Cuba Sekali Lagi') }}">
                <i class="bi bi-arrow-clockwise me-2" aria-hidden="true"></i>{{ __('Cuba Sekali Lagi') }}
            </a>
            {{-- Optional illustration --}}
            {{--
            <div class="mt-4">
                <img src="{{ asset('assets/img/illustrations/motac-error-429' . $illustrationStyleSuffix . '.png') }}"
                     alt="{{ __('Ilustrasi Had Permintaan') }}" class="img-fluid motac-error-illustration">
            </div>
            --}}
        </div>
    </div>
@endsection
