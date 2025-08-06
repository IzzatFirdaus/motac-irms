@php
    // The $illustrationStyleSuffix helps in dynamically loading light/dark mode illustrations.
    // Ensure that your MOTAC-themed blank layout and assets are correctly configured for this.
    $illustrationStyleSuffix = isset($configData['myStyle']) ? '-' . $configData['myStyle'] : '';
@endphp

@extends('layouts/blankLayout') {{-- MOTAC-themed blank layout --}}

@section('title', __('503 - Laman Dalam Selenggaraan'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
    <style>
        .misc-wrapper .display-5 {
            color: var(--bs-info);
        }

        /* Example: Info color for maintenance title */
        .motac-error-illustration {
            max-width: 350px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2 display-1 fw-bolder">503</h1>
            <h2 class="mb-2 mt-4 display-5 fw-bold">
                <i class="bi bi-gear-fill me-2"></i>{{ __('Laman Dalam Selenggaraan') }}
            </h2>
            <p class="mb-4 mx-auto col-md-8 col-lg-6 text-muted">
                {{ __('Harap maaf, sistem kami sedang dalam selenggaraan buat sementara waktu. Kami akan kembali secepat mungkin. Terima kasih atas kesabaran anda!') }}
            </p>
            <div>
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-secondary d-inline-flex align-items-center" type="submit">
                            <i class="bi bi-box-arrow-left me-2"></i>{{ __('Log Keluar') }}
                        </button>
                    </form>
                @endauth
            </div>

            <div class="mt-4 pt-2">
                {{-- ACTION REQUIRED: Replace with MOTAC-appropriate "Maintenance" illustration --}}
                {{-- <img src="{{ asset('assets/img/illustrations/motac-maintenance' . $illustrationStyleSuffix . '.png') }}" --}}
                {{-- Placeholder path --}} {{-- alt="{{ __('Ilustrasi Laman Dalam Selenggaraan') }}" width="300" --}}
                {{-- class="img-fluid motac-error-illustration"> --}}
            </div>
        </div>
    </div>
    {{--
<div class="container-fluid misc-bg-wrapper misc-under-maintenance-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image' . $illustrationStyleSuffix . '.png') }}"
       alt="{{ __('Corak Latar Belakang Hiasan') }}"
       data-app-light-img="illustrations/bg-shape-image-light.png"
       data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
--}}
@endsection

{{--
@push('custom-scripts')
  <script>
    // Countdown timer script can be kept if specific maintenance window is communicated.
    // Otherwise, a static message is usually sufficient.
    // If using, ensure totalMilliseconds is correctly set.
</script>
@endpush
--}}
