@php
    // Illustration suffix for adaptive theming (light/dark)
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts.layout-blank') {{-- Clean, minimal layout --}}

@section('title', __('422 - Tidak Dapat Diproses'))

@section('page-style')
    {{-- Base styles for error pages --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        /* Use secondary color for validation error titles */
        .misc-wrapper .display-5 {
            color: var(--bs-secondary);
        }
        .motac-error-illustration {
            max-width: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            {{-- Error code --}}
            <h1 class="mb-2 mx-2 display-1 fw-bolder">422</h1>
            {{-- Title with icon --}}
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ __('Data Tidak Sah / Tidak Dapat Diproses') }}
            </h2>
            {{-- Guidance for user --}}
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Terdapat masalah dalam data yang dihantar. Sila semak semula borang dan cuba sekali lagi.') }}
            </p>
            {{-- Back to previous page --}}
            <a href="javascript:history.back()" class="motac-btn-secondary d-inline-flex align-items-center" aria-label="{{ __('Kembali') }}">
                <i class="bi bi-arrow-left-circle-fill me-2" aria-hidden="true"></i>{{ __('Kembali') }}
            </a>
            {{-- Optional illustration --}}
            {{--
            <div class="mt-4">
                <img src="{{ asset('assets/img/illustrations/motac-error-422' . $illustrationStyleSuffix . '.png') }}"
                     alt="{{ __('Ilustrasi Data Tidak Sah') }}" class="img-fluid motac-error-illustration">
            </div>
            --}}
        </div>
    </div>
@endsection
