{{-- resources/views/auth/confirm-password-page.blade.php --}}
{{-- Renamed from confirm-password.blade.php for clarity and consistency --}}
@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses(); // Ensure Helper is namespaced
@endphp

@extends('layouts/blankLayout')

@section('title', __('Sahkan Kata Laluan'))

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
        .btn-primary:hover {
            background-color: #00417d !important;
            border-color: #00417d !important;
        }
        .form-control:focus,
        .form-check-input:focus {
            border-color: #0055A4;
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25);
        }
        .auth-cover-bg-color { background-color: #eef3f7; }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            {{-- Left Illustration Panel --}}
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        alt="{{ __('Ilustrasi Sahkan Kata Laluan MOTAC') }}" class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>
            {{-- Confirm Password Form --}}
            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    {{-- Logo --}}
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                @include('_partials.macros', ['height' => 32, 'withbg' => 'fill: var(--bs-primary);'])
                            </span>
                            <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                        </a>
                    </div>
                    <h3 class="mb-1 fw-semibold text-center">{{ __('Sahkan Kata Laluan') }}</h3>
                    <p class="text-center mb-4 text-muted small">{{ __('Ini adalah kawasan selamat aplikasi. Sila sahkan kata laluan anda sebelum meneruskan.') }}</p>
                    <x-validation-errors class="mb-3" />
                    <form id="formConfirmPassword" method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">{{ __('Kata Laluan') }}</label>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" required autofocus autocomplete="current-password" />
                                <span class="input-group-text cursor-pointer toggle-password">
                                    <i class="bi bi-eye-slash-fill"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('Sahkan') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        // For toggling password visibility
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
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
