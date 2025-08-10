{{-- resources/views/auth/login.blade.php --}}
{{-- Login page for the MOTAC system. Updated to use the new layout name: layout-blank.blade.php --}}

@php
    $customizerHidden = $customizerHidden ?? 'customizer-hide'; // Ensure variable is set for layout
@endphp

@extends('layouts.layout-blank') {{-- Use the updated minimal layout for authentication pages --}}

@section('title', __('Log Masuk Sistem')) {{-- Sets the page title --}}

@section('page-style')
    <style>
        /* Footer link styles for login card */
        .login-card-footer a {
            text-decoration: none;
        }
        .login-card-footer a:hover {
            text-decoration: underline;
        }
        .app-brand-text {
            color: var(--bs-body-color) !important;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                {{-- Application Logo and Name --}}
                <div class="app-brand justify-content-center mb-4">
                    <a href="{{ url('/') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC IRMS') }}"
                                style="height: 32px; width: auto;">
                        </span>
                        <span class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text">{{ __('motac-irms') }}</span>
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
                        {{-- Session status message --}}
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
                                <label class="form-label" for="login-password">{{ __('Kata Laluan') }}</label>
                                <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                    <input type="password" id="login-password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="loginPasswordHelp" required autocomplete="current-password" />
                                    <span class="input-group-text cursor-pointer toggle-password" tabindex="0">
                                        <i class="bi bi-eye-slash-fill"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
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
        // Password visibility toggle logic
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const input = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');
                    if (!input) return;
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

                // Also allow keyboard access to toggle with Enter/Space
                toggle.addEventListener('keydown', function(e) {
                    if (e.key === "Enter" || e.key === " ") {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
    </script>
@endpush
