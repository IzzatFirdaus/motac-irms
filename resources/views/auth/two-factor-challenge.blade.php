{{-- resources/views/auth/two-factor-challenge.blade.php --}}
{{-- Two-factor challenge page for the MOTAC system using layout-blank.blade.php --}}

@php
    $customizerHidden = $customizerHidden ?? 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts.layout-blank')

@section('title', __('Pengesahan Dua Faktor'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); }
        .auth-cover-bg-color { background-color: #eef3f7; }
        .x-label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .x-input { display: block; width: 100%; padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 400; line-height: 1.5; color: var(--bs-body-color); background-color: var(--bs-body-bg); background-clip: padding-box; border: 1px solid var(--bs-border-color); appearance: none; border-radius: 0.25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
        .x-input.is-invalid { border-color: var(--bs-danger); }
        .x-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); outline: 0; }
        .x-input-error { display: block; width: 100%; margin-top: 0.25rem; font-size: .75rem; color: var(--bs-danger); }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        alt="{{ __('Ilustrasi Pengesahan Dua Faktor MOTAC') }}" class="img-fluid my-5 auth-illustration">
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
                    <h3 class="mb-1 fw-semibold text-center">{{ __('Pengesahan Dua Faktor') }} <i class="bi bi-shield-lock-fill ms-1"></i></h3>
                    <div x-data="{ recovery: false }">
                        <div class="mb-3 text-center text-muted small" x-show="! recovery">
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan kod pengesahan dari aplikasi pengesah anda.') }}
                        </div>
                        <div class="mb-3 text-center text-muted small" x-show="recovery" x-cloak>
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan salah satu kod pemulihan kecemasan anda.') }}
                        </div>
                        <x-validation-errors class="mb-3 alert alert-danger small" />
                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf
                            <div class="mb-3" x-show="! recovery">
                                <x-label for="code" class="form-label" value="{{ __('Kod Pengesahan') }}" />
                                <x-input id="code" class="form-control form-control-sm {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                    type="text" inputmode="numeric" name="code" autofocus x-ref="code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="code" class="mt-2 small text-danger d-block" />
                            </div>
                            <div class="mb-3" x-show="recovery" x-cloak>
                                <x-label for="recovery_code" class="form-label" value="{{ __('Kod Pemulihan') }}" />
                                <x-input id="recovery_code" class="form-control form-control-sm {{ $errors->has('recovery_code') ? 'is-invalid' : '' }}"
                                    type="text" name="recovery_code" x-ref="recovery_code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="recovery_code" class="mt-2 small text-danger d-block" />
                            </div>
                            <div class="d-flex justify-content-between my-3 flex-wrap gap-2">
                                <button type="button" class="motac-btn-outline btn-sm" x-show="! recovery"
                                    x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus()})">
                                    {{ __('Guna kod pemulihan') }}
                                </button>
                                <button type="button" class="motac-btn-outline btn-sm" x-cloak x-show="recovery"
                                    x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                                    {{ __('Guna kod pengesahan') }}
                                </button>
                                <x-button type="submit" class="motac-btn-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Log Masuk') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
