@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Pengesahan Dua Faktor'))

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

        .btn-outline-secondary {
            /* Ensure this matches MOTAC outline secondary */
        }

        .form-control:focus {
            border-color: #0055A4;
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25);
        }

        .auth-cover-bg-color {
            background-color: #eef3f7;
        }

        /* Add styles to make Jetstream x-components look like Bootstrap inputs/buttons if not replacing them */
        /* Example for x-input to mimic .form-control */
        [type="text"].w-full.border-gray-300 {
            /* Target Jetstream's default input more specifically */
            /* Apply Bootstrap .form-control styles here if x-input doesn't render them */
        }
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
                        {{-- Placeholder --}} alt="{{ __('Ilustrasi Pengesahan Dua Faktor MOTAC') }}"
                        class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" {{-- Placeholder --}}
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>
            {{-- /Left Text --}}

            {{-- Two Steps Verification Form --}}
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

                    <h3 class="mb-1 fw-semibold text-center">{{ __('Pengesahan Dua Faktor') }} <i
                            class="bi bi-shield-shaded"></i></h3>
                    <div x-data="{ recovery: false }">
                        <div class="mb-3 text-center text-muted small" x-show="! recovery">
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan kod pengesahan dari aplikasi pengesah anda.') }}
                        </div>

                        <div class="mb-3 text-center text-muted small" x-show="recovery">
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan salah satu kod pemulihan kecemasan anda.') }}
                        </div>

                        {{-- x-validation-errors should be styled as .alert .alert-danger --}}
                        <x-validation-errors class="mb-3" />

                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf

                            <div class="mb-3" x-show="! recovery">
                                {{-- x-label should be <label class="form-label"> --}}
                                <x-label for="code" class="form-label" value="{{ __('Kod Pengesahan') }}" />
                                {{-- x-input should be <input class="form-control form-control-sm"> --}}
                                <x-input id="code"
                                    class="{{ $errors->has('code') ? 'is-invalid' : '' }} form-control form-control-sm"
                                    type="text" inputmode="numeric" name="code" autofocus x-ref="code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="code" />
                            </div>

                            <div class="mb-3" x-show="recovery">
                                <x-label for="recovery_code" class="form-label" value="{{ __('Kod Pemulihan') }}" />
                                <x-input id="recovery_code"
                                    class="{{ $errors->has('recovery_code') ? 'is-invalid' : '' }} form-control form-control-sm"
                                    type="text" name="recovery_code" x-ref="recovery_code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="recovery_code" />
                            </div>

                            <div class="d-flex justify-content-between my-3 flex-wrap gap-2"> {{-- Changed to justify-content-between --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary" x-show="! recovery"
                                    x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus()})">
                                    {{ __('Guna kod pemulihan') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" x-cloak x-show="recovery"
                                    x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                                    {{ __('Guna kod pengesahan') }}
                                </button>
                                {{-- x-button should be <button class="btn btn-primary btn-sm"> --}}
                                <x-button type="submit" class="btn btn-primary btn-sm"> {{-- Added btn-sm and type submit --}}
                                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Log Masuk') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- / Two Steps Verification Form --}}
        </div>
    </div>
@endsection
