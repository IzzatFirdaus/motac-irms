{{-- resources/views/auth/register.blade.php --}}
{{-- Registration page for the MOTAC system. Updated to use the new layout name: layout-blank.blade.php --}}

@php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
@endphp

@extends('layouts.layout-blank') {{-- Use the updated minimal layout for authentication pages --}}

@section('title', __('Daftar Akaun Baru'))

@section('page-style')
    <style>
        .register-card-footer a {
            text-decoration: none;
        }
        .register-card-footer a:hover {
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
              <a href="{{url('/')}}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets/img/logo/motac-logo.svg') }}" alt="{{ __('Logo MOTAC IRMS') }}" style="height: 32px; width: auto;">
                </span>
                <span class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text">{{ __('motac-irms') }}</span>
              </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0 text-white d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        {{ __('Daftar Akaun Baru') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="mb-1 fw-semibold text-center">{{ __('Sertai Sistem Kami') }}</h5>
                    <p class="mb-3 text-center text-muted small">{{ __('Sila lengkapkan maklumat di bawah untuk mendaftar.') }}</p>

                    <x-validation-errors class="mb-3" />

                    <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nama Penuh') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Masukkan nama penuh anda') }}" autofocus value="{{ old('name') }}" required />
                            @error('name')
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Alamat E-mel') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('cth: pengguna@example.com') }}" value="{{ old('email') }}" required/>
                            @error('email')
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">{{ __('Kata Laluan') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="passwordHelpBlock" required autocomplete="new-password"/>
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="passwordHelpBlock" class="form-text small">
                                {{ __('Kata laluan mesti sekurang-kurangnya 8 aksara.') }}
                            </div>
                            @error('password')
                            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation">{{ __('Sahkan Kata Laluan') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation" required autocomplete="new-password"/>
                                <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="mb-3">
                                <div class="form-check @error('terms') is-invalid @enderror">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required />
                                    <label class="form-check-label small" for="terms">
                                        {{ __('Saya bersetuju dengan') }}
                                        <a href="{{ route('terms.show') }}" target="_blank" class="text-decoration-none">{{ __('terma perkhidmatan') }}</a> &amp;
                                        <a href="{{ route('policy.show') }}" target="_blank" class="text-decoration-none">{{ __('dasar privasi') }}</a>. <span class="text-danger">*</span>
                                    </label>
                                </div>
                                @error('terms')
                                <div class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></div>
                                @enderror
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary d-grid w-100">
                            <i class="bi bi-person-plus-fill me-1"></i>{{ __('Daftar Akaun') }}
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center text-muted small p-3 register-card-footer">
                    <p class="mb-0">
                        <span>{{ __('Sudah mempunyai akaun?') }}</span>
                        @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="ms-1">
                            <span>{{ __('Log masuk di sini') }}</span>
                        </a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script>
    // Password visibility toggle for register form
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
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
