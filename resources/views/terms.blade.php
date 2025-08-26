{{-- resources/views/terms.blade.php --}}
@php
    use Illuminate\Support\Str;
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.app') {{-- Use the main MOTAC layout for consistent theme, menu, light/dark support --}}

@section('title', __('terms.title')) {{-- Translation key for terms page title --}}

@push('page-style')
    <style>
        .card-body-markdown {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-body-color);
            line-height: 1.65;
        }
        .card-body-markdown h1,
        .card-body-markdown h2,
        .card-body-markdown h3,
        .card-body-markdown h4,
        .card-body-markdown h5,
        .card-body-markdown h6 {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-emphasis-color);
            margin-top: 1.6em;
            margin-bottom: 0.5em;
            font-weight: 600;
        }
        .card-body-markdown h1 { font-size: 2rem; }
        .card-body-markdown h2 { font-size: 1.35rem; }
        .card-body-markdown h3 { font-size: 1.12rem; }
        .card-body-markdown ul,
        .card-body-markdown ol { padding-left: 2em; margin-bottom: 1.1em; }
        .card-body-markdown p { margin-bottom: 1em; }
        .card-body-markdown a { color: var(--bs-primary); text-decoration: underline; }
        .card-body-markdown a:hover { filter: brightness(90%); }
        .card-body-markdown strong { font-weight: 600; }
        .card-body-markdown blockquote {
            border-left: 4px solid var(--bs-primary);
            margin-left: 0;
            padding-left: 1em;
            color: #555;
        }
        .app-brand-text { color: var(--bs-body-color) !important; }
        @media (max-width: 600px) {
            .card-body-markdown { font-size: 0.98rem; }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                {{-- Application Logo and Name --}}
                <div class="app-brand justify-content-center mb-4">
                    <a href="{{ url('/') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                                 alt="{{ __('app.logo_motac_irms') }}"
                                 style="height: 32px; width: auto;">
                        </span>
                        <span class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text">
                            {{ __('app.system_name') }}
                        </span>
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-file-text-fill me-2"></i>
                            {{ __('terms.title') }}
                        </h4>
                    </div>
                    <div class="card-body p-4 card-body-markdown">
                        {{-- Output the rendered/sanitized markdown as HTML, language-aware --}}
                        {!! Str::markdown(__('terms.content')) !!}
                    </div>
                </div>

                <div class="text-center mt-4 mb-3">
                    <a href="{{ url('/') }}" class="motac-btn-outline motac-btn-sm d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('app.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
