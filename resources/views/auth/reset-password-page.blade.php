{{-- resources/views/auth/reset-password-page.blade.php --}}
{{-- Renamed from reset-password.blade.php for clarity and consistency --}}
@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Tetapkan Semula Kata Laluan'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus, .form-check-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); }
        .auth-cover-bg-color { background-color: #eef3f7; }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        alt="{{ __('Ilustrasi Tetapan Semula Kata Laluan MOTAC') }}" class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>
            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                @include('_partials.macros', ['height' => 32, 'withbg' => 'fill: var(--bs-primary);'])
                            </span>
                            <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                        </a>
                    </div>
                    <h3 class="mb-1 fw-semibold text-center">{{ __('Tetapkan Semula Kata Laluan Anda') }} <i class="bi bi-shield-lock-fill ms-1"></i></h3>
                    <p class="mb-4 text-center text-muted small">{{ __('Sila masukkan kata laluan baharu anda di bawah.') }}</p>
                    <x-validation-errors class="mb-3" />
                    <form id="formAuthentication" class="mb-3" action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Alamat E-mel') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="{{ __('cth: pengguna@motac.gov.my') }}"
                                value="{{ $request->email ?? old('email') }}" readonly />
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">{{ __('Kata Laluan Baru') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="passwordHelpBlockReset" required autofocus autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                             <div id="passwordHelpBlockReset" class="form-text small">
                                {{ __('Kata laluan mesti sekurang-kurangnya 8 aksara.') }}
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation">{{ __('Sahkan Kata Laluan Baru') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password_confirmation" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100 mb-3">
                            <i class="bi bi-key-fill me-1"></i>{{ __('Tetapkan Kata Laluan Baru') }}
                        </button>
                        <div class="text-center">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center text-decoration-none small">
                                    <i class="bi bi-chevron-left me-1"></i>{{ __('Kembali ke Log Masuk') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        // Toggle password visibility for reset password form
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
