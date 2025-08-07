{{-- resources/views/policy.blade.php --}}
@php
    $customizerHidden = 'customizer-hide'; // Theme-specific variable to optionally hide customizer UI
@endphp

@extends('layouts.blankLayout') {{-- Uses the MOTAC-themed blank layout for static/legal pages --}}

@section('title', __('Dasar Privasi')) {{-- Set the page title, translatable --}}

@section('page-style')
    {{-- Inline style for Markdown content in the card --}}
    <style>
        .card-body-markdown {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-body-color);
            line-height: 1.6;
        }

        .card-body-markdown h1,
        .card-body-markdown h2,
        .card-body-markdown h3,
        .card-body-markdown h4,
        .card-body-markdown h5,
        .card-body-markdown h6 {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-emphasis-color);
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            font-weight: 600;
        }

        .card-body-markdown h1 {
            font-size: 1.75rem;
        }

        .card-body-markdown h2 {
            font-size: 1.5rem;
        }

        .card-body-markdown h3 {
            font-size: 1.25rem;
        }

        .card-body-markdown p {
            margin-bottom: 1em;
        }

        .card-body-markdown ul,
        .card-body-markdown ol {
            padding-left: 2em;
            margin-bottom: 1em;
        }

        .card-body-markdown a {
            color: var(--bs-primary);
            text-decoration: underline;
        }

        .card-body-markdown a:hover {
            filter: brightness(90%);
        }

        .card-body-markdown strong {
            font-weight: 600;
        }

        .app-brand-text {
            /* Ensure consistent app brand text styling if needed */
            color: var(--bs-body-color) !important;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7"> {{-- Centered column for policy content --}}

                {{-- Application Logo and Name - Above the card --}}
                <div class="app-brand justify-content-center mb-4">
                    <a href="{{ url('/') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC IRMS') }}"
                                style="height: 32px; width: auto;">
                        </span>
                        <span
                            class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text">{{ __('motac-irms') }}</span>
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-shield-check me-2"></i> {{-- Icon for Policy --}}
                            {{ __('Dasar Privasi') }}
                        </h4>
                    </div>
                    <div class="card-body p-4 card-body-markdown">
                        {!! $policy !!} {{-- Renders HTML content from Markdown. Ensure $policy is sanitized/escaped at controller level. --}}
                    </div>
                </div>

                <div class="text-center mt-4 mb-3">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('Kembali ke Laman Utama') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
