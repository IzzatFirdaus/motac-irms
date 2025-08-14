{{-- resources/views/auth/login.blade.php --}}
@php
    $customizerHidden = 'customizer-hide'; // Theme-specific variable
    // $configData = App\Helpers\Helpers::appClasses(); // Not strictly needed if blankLayout handles global styles
@endphp

@extends('layouts/blankLayout') {{-- Extends the MOTAC blank layout --}}

@section('title', __('Log Masuk Sistem')) {{-- Sets the page title --}}

@section('page-style')
    {{-- Removed page-auth.css as we are defining a simpler card layout --}}
    {{-- Global styles for Noto Sans, buttons, inputs are expected from blankLayout/theme --}}
    <style>
        /* Ensure body background and text colors match the theme (usually set in blankLayout) */
        /* body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; } */
        /* .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; } */
        /* .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background-color: #00417d !important; border-color: #00417d !important; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.5) !important; } */
        /* .form-control:focus, .form-select:focus, .form-check-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); } */
        /* .form-check-input:checked { background-color: #0055A4; border-color: #0055A4; } */

        .login-card-footer a {
            text-decoration: none;
        }

        .login-card-footer a:hover {
            text-decoration: underline;
        }

        .app-brand-text {
            /* Ensure consistent app brand text styling if needed */
            color: var(--bs-body-color) !important;
            /* Match default body text color */
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5"> {{-- Centered column for the login form --}}

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
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            {{ __('Log Masuk Sistem') }}
                        </h4>
                    </div>
                    <div class="card-body p-4 login-form-container">
                        <h5 class="mb-1 fw-semibold text-center">{{ __('Selamat Datang!') }}</h5>
                        <p class="mb-3 text-center text-muted small">
                            {{ __('Sila log masuk ke akaun anda.') }}
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success mb-3 py-2 small d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                            </div>
                        @endif
                        <x-validation-errors class="mb-3" />

                        <form id="formAuthentication" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="login-email" class="form-label">{{ __('E-mel / ID Pengguna') }}</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                    id="login-email" name="email" placeholder="{{ __('contoh: pengguna@motac.gov.my') }}"
                                    autofocus value="{{ old('email') }}" required aria-describedby="emailHelp">
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 form-password-toggle">
                                {{-- Label and Forgot Password link removed from here, will be in footer --}}
                                <label class="form-label" for="login-password">{{ __('Kata Laluan') }}</label>
                                <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                    <input type="password" id="login-password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="loginPasswordHelp" required autocomplete="current-password" />
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
                            <div class="mb-3"> {{-- Moved "Remember Me" and "Forgot Password?" here for better flow --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                                            {{ old('remember') ? 'checked' : '' }} checked>
                                        <label class="form-check-label" for="remember-me">
                                            {{ __('Ingat Saya') }}
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-decoration-none small">
                                            {{ __('Lupa Kata Laluan?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100" type="submit">
                                <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Log Masuk') }}
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small p-3 login-card-footer">
                        @if (Route::has('register'))
                            <p class="mb-2">
                                <span>{{ __('Pengguna baru?') }}</span>
                                <a href="{{ route('register') }}" class="ms-1">
                                    <span>{{ __('Cipta akaun di sini') }}</span>
                                </a>
                            </p>
                        @endif
                        <div>
                            @if (Route::has('policy.show'))
                                <a href="{{ route('policy.show') }}" class="mx-2">{{ __('Dasar Privasi') }}</a>
                            @endif
                            @if (Route::has('terms.show'))
                                <a href="{{ route('terms.show') }}" class="mx-2">{{ __('Terma Perkhidmatan') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        // Vanilla JS for password toggle
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggles = document.querySelectorAll('.toggle-password');
            passwordToggles.forEach(toggle => {
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
