@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Sahkan Alamat E-mel'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body {
            font-family: 'Noto Sans', sans-serif !important;
            line-height: 1.6;
        }

        .btn-primary {
            background-color: #0055A4 !important;
            border-color: #0055A4 !important;
        }

        /* MOTAC Blue */
        .btn-primary:hover {
            background-color: #00417d !important;
            border-color: #00417d !important;
        }

        .btn-label-secondary {
            /* Define MOTAC's "label-secondary" style or map to btn-outline-secondary */
        }

        .btn-danger {
            /* Define MOTAC's danger button style */
        }

        .auth-cover-bg-color {
            background-color: #eef3f7;
        }

        /* If not using illustration side panel */
        .card {
            border: 1px solid var(--motac-border, #dee2e6);
        }

        /* Ensure cards have borders if theme requires */
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-basic px-4 d-flex align-items-center min-vh-100"> {{-- Added flex utils for centering --}}
        <div class="authentication-inner py-4 w-100" style="max-width: 400px;"> {{-- Consistent width --}}

            {{-- Logo --}}
            <div class="app-brand mb-4 d-flex justify-content-center">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        @include('_partials.macros', [
                            'height' => 32,
                            'withbg' => 'fill: var(--bs-primary);',
                        ])
                    </span>
                    <span
                        class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                </a>
            </div>
            {{-- /Logo --}}

            <div class="card shadow-sm"> {{-- Added shadow --}}
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1 fw-semibold">{{ __('Sahkan Alamat E-mel Anda') }} <i
                                class="bi bi-envelope-check-fill text-primary"></i></h3>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mt-3 py-2 small" role="alert"> {{-- Using Bootstrap Alert --}}
                            <i class="bi bi-check-circle-fill me-1"></i>
                            {{ __('Pautan pengesahan baharu telah dihantar ke alamat e-mel yang anda berikan semasa pendaftaran.') }}
                        </div>
                    @endif

                    <p class="text-center mt-3 text-muted small">
                        {{ __('Pautan pengaktifan akaun telah dihantar ke alamat e-mel anda:') }} <br>
                        <span class="fw-semibold text-dark">{{ Auth::user()->email }}</span><br>
                        {{ __('Sila ikuti pautan di dalamnya untuk meneruskan.') }}
                    </p>

                    <div class="mt-4 d-flex flex-column justify-content-center gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            {{-- Ensure btn-label-secondary is styled according to MOTAC theme or use btn-outline-secondary --}}
                            <button type="submit" class="btn btn-outline-secondary d-grid w-100">
                                <i class="bi bi-send-arrow-up-fill me-1"></i>{{ __('Hantar Semula Pautan Pengesahan') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger d-grid w-100">
                                <i class="bi bi-box-arrow-left me-1"></i>{{ __('Log Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
