@php
    // $configData = \App\Helpers\Helpers::appClasses(); // Only uncomment if blankLayout or its content specifically needs $configData here.
    $customizerHidden = 'customizer-hide'; // Theme-specific variable
@endphp

@extends('layouts.blankLayout') {{-- This MUST be your MOTAC-themed blank layout --}}

@section('title', __('Terma Perkhidmatan'))

@section('page-style')
    {{-- page-auth.css from the base theme, provides structure for auth pages --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        /* Inline styles to ensure Noto Sans and MOTAC theme colors apply to Markdown content */
        .card-body-markdown { /* Added a specific class for more targeted styling */
            font-family: 'Noto Sans', sans-serif !important; /* Enforce Noto Sans */
            color: var(--bs-body-color); /* Uses Bootstrap's body color, themed by MOTAC theme */
            line-height: 1.6;
        }

        .card-body-markdown h1,
        .card-body-markdown h2,
        .card-body-markdown h3,
        .card-body-markdown h4,
        .card-body-markdown h5,
        .card-body-markdown h6 {
            font-family: 'Noto Sans', sans-serif !important; /* Enforce Noto Sans for headings */
            color: var(--bs-emphasis-color); /* Uses Bootstrap's emphasis color, themed by MOTAC theme */
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        .card-body-markdown h1 { font-size: 1.75rem; }
        .card-body-markdown h2 { font-size: 1.5rem; }
        .card-body-markdown h3 { font-size: 1.25rem; }

        .card-body-markdown p {
            margin-bottom: 1em;
        }

        .card-body-markdown ul,
        .card-body-markdown ol {
            padding-left: 2em; /* Standard padding for lists */
            margin-bottom: 1em;
        }

        .card-body-markdown a {
            color: var(--bs-primary); /* Uses Bootstrap's primary color, themed as MOTAC Blue */
            text-decoration: underline;
        }
        .card-body-markdown a:hover {
            color: var(--bs-primary-darken); /* Assuming a darker primary for hover, or use default hover */
        }
        .card-body-markdown strong {
            font-weight: 600; /* Semibold for strong tags */
        }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-basic px-4 py-4">
        <div class="authentication-inner" style="max-width: 800px; margin: auto;"> {{-- Increased max-width for readability --}}
            <div class="app-brand justify-content-center mb-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <x-application-logo style="height: 40px; width: auto;" />
                </a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5 card-body-markdown"> {{-- Added card-body-markdown class --}}
                    {!! $terms !!} {{-- Content comes from this variable (HTML from Markdown) --}}
                </div>
            </div>
            <div class="text-center mt-4 mb-3">
                <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('Kembali ke Laman Utama') }}
                </a>
            </div>
        </div>
    </div>
@endsection
