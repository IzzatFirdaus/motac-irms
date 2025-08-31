{{-- resources/views/auth/login.blade.php --}}
{{-- MYDS & MyGOVEA Compliant Login Page for MOTAC System --}}

@php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
@endphp

@extends('layouts.layout-blank')

@section('title', __('Log Masuk Sistem'))

@section('page-style')
    <style>
        /* MYDS-compliant login page styling */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--myds-bg-secondary) 0%, var(--myds-bg-muted) 100%);
            padding: 20px;
        }

        .login-card {
            background: var(--myds-bg-primary);
            border: 1px solid var(--myds-gray-200);
            border-radius: var(--myds-radius-lg);
            box-shadow: var(--myds-shadow-lg);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            background: linear-gradient(135deg, var(--myds-primary) 0%, #1d4ed8 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .login-body {
            padding: 32px 24px;
        }

        .login-footer {
            background: var(--myds-bg-secondary);
            padding: 20px 24px;
            text-align: center;
            border-top: 1px solid var(--myds-gray-200);
        }

        .app-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-decoration: none;
        }

        .app-brand-logo {
            width: 48px;
            height: 48px;
            background: white;
            border-radius: var(--myds-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--myds-shadow-sm);
        }

        .app-brand-text {
            font-family: var(--myds-font-heading);
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--myds-txt-primary);
        }

        /* Dark mode adjustments */
        [data-bs-theme="dark"] .login-container {
            background: linear-gradient(135deg, var(--myds-gray-800) 0%, var(--myds-gray-700) 100%);
        }

        [data-bs-theme="dark"] .login-card {
            background: var(--myds-gray-800);
            border-color: var(--myds-gray-600);
        }

        [data-bs-theme="dark"] .login-footer {
            background: var(--myds-gray-700);
            border-color: var(--myds-gray-600);
        }
    </style>
@endsection

@section('content')
    <div class="login-container">
        <div class="login-card">
            {{-- Header Section --}}
            <header class="login-header" role="banner">
                <div class="app-brand">
                    <div class="app-brand-logo">
                        <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                             alt="{{ __('Logo MOTAC') }}"
                             width="32"
                             height="32"
                             loading="eager">
                    </div>
                    <div class="app-brand-text text-white">{{ __('motac-irms') }}</div>
                </div>

                <h1 class="h4 mb-2 text-white" style="font-family: var(--myds-font-heading);">
                    <i class="ti ti-login me-2" aria-hidden="true"></i>
                    {{ __('Log Masuk Sistem') }}
                </h1>
                <p class="mb-0 text-white-50" style="font-family: var(--myds-font-body);">
                    {{ __('Sistem Pengurusan Sumber Bersepadu MOTAC') }}
                </p>
            </header>

            {{-- Main Form Section --}}
            <main class="login-body">
                <div class="text-center mb-4">
                    <h2 class="h5 fw-semibold mb-2" style="font-family: var(--myds-font-heading); color: var(--myds-txt-primary);">
                        {{ __('Selamat Datang!') }}
                    </h2>
                    <p class="text-muted mb-0" style="font-family: var(--myds-font-body);">
                        {{ __('Sila log masuk ke akaun anda untuk mengakses sistem.') }}
                    </p>
                </div>

                {{-- Session Status Message --}}
                @if (session('status'))
                    <div class="alert alert-success mb-3 d-flex align-items-center" role="alert">
                        <i class="ti ti-check me-2" aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Validation Errors --}}
                <x-validation-errors class="mb-3" />

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    {{-- Email Field --}}
                    <div class="mb-3">
                        <label for="login-email" class="myds-form-label">
                            {{ __('E-mel / ID Pengguna') }}
                            <span class="text-danger" aria-label="{{ __('Medan Wajib') }}">*</span>
                        </label>
                        <input type="text"
                               class="form-control myds-form-control mygovea-accessible @error('email') is-invalid @enderror"
                               id="login-email"
                               name="email"
                               placeholder="{{ __('contoh: pengguna@motac.gov.my') }}"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               aria-describedby="email-help">
                        <div id="email-help" class="myds-form-text">
                            {{ __('Gunakan alamat e-mel rasmi MOTAC atau ID pengguna yang diberikan.') }}
                        </div>
                        @error('email')
                            <div class="myds-form-error" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password Field --}}
                    <div class="mb-3">
                        <label for="login-password" class="myds-form-label">
                            {{ __('Kata Laluan') }}
                            <span class="text-danger" aria-label="{{ __('Medan Wajib') }}">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control myds-form-control @error('password') is-invalid @enderror"
                                   id="login-password"
                                   name="password"
                                   placeholder="••••••••••••"
                                   required
                                   autocomplete="current-password"
                                   aria-describedby="password-toggle password-help">
                            <button type="button"
                                    class="btn btn-outline-secondary mygovea-accessible password-toggle"
                                    id="password-toggle"
                                    aria-label="{{ __('Tunjukkan/Sembunyikan Kata Laluan') }}"
                                    tabindex="0">
                                <i class="ti ti-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-help" class="myds-form-text">
                            {{ __('Masukkan kata laluan yang diberikan oleh pentadbir sistem.') }}
                        </div>
                        @error('password')
                            <div class="myds-form-error" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="remember-me"
                                   name="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-me">
                                {{ __('Ingat Saya') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-decoration-none mygovea-accessible"
                               style="color: var(--myds-primary); font-size: 0.875rem;">
                                {{ __('Lupa Kata Laluan?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn btn-myds-primary w-100 mygovea-accessible"
                            style="font-family: var(--myds-font-body);">
                        <i class="ti ti-login me-2" aria-hidden="true"></i>
                        {{ __('Log Masuk') }}
                    </button>
                </form>
            </main>

            {{-- Footer Section --}}
            <footer class="login-footer" role="contentinfo">
                @if (Route::has('register'))
                    <p class="mb-2" style="font-family: var(--myds-font-body);">
                        <span class="text-muted">{{ __('Pengguna baru?') }}</span>
                        <a href="{{ route('register') }}"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-primary); font-weight: 500;">
                            {{ __('Cipta akaun di sini') }}
                        </a>
                    </p>
                @endif

                <div class="d-flex justify-content-center flex-wrap gap-3">
                    @if (Route::has('policy.show'))
                        <a href="{{ route('policy.show') }}"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-txt-muted); font-size: 0.875rem;">
                            {{ __('Dasar Privasi') }}
                        </a>
                    @endif
                    @if (Route::has('terms.show'))
                        <a href="{{ route('terms.show') }}"
                           class="text-decoration-none mygovea-accessible"
                           style="color: var(--myds-txt-muted); font-size: 0.875rem;">
                            {{ __('Terma Perkhidmatan') }}
                        </a>
                    @endif
                </div>
            </footer>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        // MYDS-compliant password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('login-password');
            const passwordToggle = document.getElementById('password-toggle');
            const toggleIcon = passwordToggle.querySelector('i');

            if (passwordToggle && passwordInput && toggleIcon) {
                passwordToggle.addEventListener('click', function() {
                    const isPassword = passwordInput.type === 'password';

                    // Toggle input type
                    passwordInput.type = isPassword ? 'text' : 'password';

                    // Update icon
                    toggleIcon.className = isPassword ? 'ti ti-eye-off' : 'ti ti-eye';

                    // Update ARIA label
                    passwordToggle.setAttribute('aria-label',
                        isPassword ? '{{ __('Sembunyikan Kata Laluan') }}' : '{{ __('Tunjukkan Kata Laluan') }}'
                    );
                });

                // Keyboard accessibility
                passwordToggle.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            }
        });
    </script>
@endpush
