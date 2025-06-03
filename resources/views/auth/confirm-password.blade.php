@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses(); // Ensure Helper is namespaced or App\Helpers\Helpers
@endphp

@extends('layouts/blankLayout') {{-- This layout MUST enforce Noto Sans and MOTAC base Bootstrap styling --}}

@section('title', __('Sahkan Kata Laluan'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    {{-- Add MOTAC-specific overrides if page-auth.css conflicts with Noto Sans or MOTAC colors --}}
    <style>
        body {
            font-family: 'Noto Sans', sans-serif !important;
            line-height: 1.6;
        }

        .btn-primary {
            background-color: #0055A4 !important;
            border-color: #0055A4 !important;
        }

        .btn-primary:hover {
            background-color: #00417d !important;
            border-color: #00417d !important;
        }

        .form-control:focus,
        .form-check-input:focus {
            border-color: #0055A4;
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25);
        }

        .auth-cover-bg-color {
            background-color: #eef3f7;
        }

        /* Example light neutral */
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            {{-- Left Text / Illustration Panel --}}
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    {{-- ACTION REQUIRED: Replace with MOTAC-appropriate visuals --}}
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        {{-- Placeholder for MOTAC specific image --}} alt="{{ __('Ilustrasi Sahkan Kata Laluan MOTAC') }}"
                        class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" {{-- Placeholder for theme shape --}}
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>
            {{-- /Left Text --}}

            {{-- Confirm Password Form --}}
            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
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

                    <h3 class="mb-1 fw-semibold text-center">{{ __('Sahkan Kata Laluan') }}</h3>
                    <p class="text-center mb-4 text-muted">{{ __('Sila sahkan kata laluan anda sebelum meneruskan.') }}</p>

                    <form id="formConfirmPassword" action="{{ route('password.confirm') }}" method="POST">
                        @csrf
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">{{ __('Kata Laluan Anda') }}</label>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" required autofocus />
                                <span class="input-group-text cursor-pointer toggle-password">
                                    <i class="bi bi-eye-slash-fill"></i> {{-- Bootstrap Icon --}}
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('Sahkan Kata Laluan') }}
                        </button>
                    </form>
                </div>
            </div>
            {{-- /Confirm Password Form --}}
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        // Vanilla JS for password toggle (same as login.blade.php)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    if (input.type === "password") {
                        input.type = "text";
                        icon.classList.remove('bi-eye-slash-fill');
                        icon.classList.add('bi-eye-fill');
                    } else {
                        input.type = "password";
                        icon.classList.remove('bi-eye-fill');
                        icon.classList.add('bi-eye-slash-fill');
                    }
                });
            });
        });
    </script>
@endpush
