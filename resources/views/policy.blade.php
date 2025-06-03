@php
    // $configData = \App\Helpers\Helpers::appClasses(); // If needed by blankLayout, ensure it's available
$customizerHidden = 'customizer-hide'; // Theme-specific variable
@endphp

@extends('layouts.blankLayout') {{-- This MUST be your MOTAC-themed blank layout --}}

@section('title', __('Dasar Privasi'))

@section('page-style')
    {{-- Ensure page-auth.css aligns with or is overridden by MOTAC theme for elements within $policy --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        /* Ensure Noto Sans and MOTAC text colors are applied to content from $policy if not handled by blankLayout */
        .card-body {
            font-family: 'Noto Sans', sans-serif;
            color: var(--bs-body-color);
            /* Should be MOTAC text color */
            line-height: 1.6;
            /* Good for readability */
        }

        .card-body h1,
        .card-body h2,
        .card-body h3,
        .card-body h4,
        .card-body h5,
        .card-body h6 {
            font-family: 'Noto Sans', sans-serif;
            /* Ensure headings also use Noto Sans */
            color: var(--bs-emphasis-color);
            /* Or your MOTAC heading color */
        }

        .card-body a {
            color: var(--bs-primary);
            /* Should be MOTAC Primary Blue */
        }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-basic px-4 py-4"> {{-- Added py-4 for vertical padding --}}
        <div class="authentication-inner" style="max-width: 800px; margin: auto;"> {{-- Centering content --}}
            <div class="app-brand justify-content-center mb-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    {{-- Ensure x-application-logo renders the official MOTAC logo, styled appropriately --}}
                    <x-application-logo style="height: 40px; width: auto;" />
                </a>
            </div>
            <div class="card shadow-sm"> {{-- Added shadow-sm for subtle depth --}}
                <div class="card-body p-4 p-md-5"> {{-- Added more padding --}}
                    {!! $policy !!} {{-- Content comes from this variable --}}
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('Kembali ke Laman Utama') }}
                </a>
            </div>
        </div>
    </div>
@endsection
