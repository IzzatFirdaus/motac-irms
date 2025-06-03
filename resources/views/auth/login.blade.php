@php
    $customizerHidden = 'customizer-hide'; // Used by some themes to hide the theme customizer
    // $configData is likely for theme-specific settings (light/dark, RTL).
    // Ensure it's configured to support MOTAC's primary aesthetic.
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout') {{-- This layout MUST enforce Noto Sans and MOTAC base Bootstrap styling --}}

@section('title', __('Log Masuk Sistem'))

@section('page-style')
    {{-- Page Css files --}}
    {{-- This CSS should be reviewed. If it overrides Bootstrap in ways that conflict with MOTAC theme,
         custom MOTAC overrides will be needed here or in a global MOTAC theme CSS. --}}
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        /*
                Ensuring MOTAC Design System is applied.
                Ideally, much of this would be in your global MOTAC theme CSS loaded by blankLayout.
            */
        body {
            font-family: 'Noto Sans', sans-serif !important;
            /* Design Doc 2.2 */
            line-height: 1.6;
            /* Design Doc 2.2 */
        }

        .btn-primary {
            background-color: #0055A4 !important;
            /* MOTAC Blue - Design Doc 2.1 */
            border-color: #0055A4 !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: #00417d !important;
            /* Darker shade for interaction */
            border-color: #00417d !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.5) !important;
            /* Focus shadow with MOTAC blue */
        }

        .form-control:focus,
        .form-select:focus,
        /* Added form-select */
        .form-check-input:focus {
            border-color: #0055A4;
            /* MOTAC Blue - Design Doc 6.1 */
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25);
            /* Adjusted for MOTAC Blue */
        }

        .form-check-input:checked {
            background-color: #0055A4;
            /* MOTAC Blue */
            border-color: #0055A4;
        }

        /* Illustration panel styling - to align with "Modern Government Aesthetic" */
        .auth-cover-bg-color {
            /*
                    SUGGESTION: Replace with a professional MOTAC-branded background.
                    This could be a solid color from the MOTAC palette, a subtle texture,
                    or an approved official image.
                */
            background-color: #eef3f7;
            /* Example: A light, neutral background */
        }

        .auth-illustration {
            max-width: 450px;
            /* Adjust as needed */
            /* For a more professional look, consider if this type of illustration is suitable
                   or if a more abstract or photographic image aligned with MOTAC's work would be better. */
        }

        .platform-bg {
            /* This background shape might be part of the theme's specific look.
                   Evaluate if it fits the MOTAC professional aesthetic or if it should be removed/simplified. */
        }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            {{-- Left Text / Illustration Panel --}}
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    {{--
                        ACTION REQUIRED: Replace with MOTAC-appropriate visuals.
                        Consider a professional image related to Malaysian tourism, arts, culture,
                        or a clean graphic representing integrated resource management.
                        If using illustrations, ensure they match the desired "Modern Government Aesthetic".
                    --}}
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        {{-- Placeholder for MOTAC specific image --}} alt="{{ __('Ilustrasi Laman Log Masuk MOTAC') }}"
                        class="img-fluid my-5 auth-illustration" {{-- data-app-light-img="illustrations/motac-auth-professional-light.png" --}} {{-- data-app-dark-img="illustrations/motac-auth-professional-dark.png" --}}>

                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" {{-- Placeholder for theme shape, evaluate if needed for MOTAC --}}
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>
            {{-- /Left Text / Illustration Panel --}}

            {{-- Login Form --}}
            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    {{-- Logo --}}
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            {{--
                                Design Doc 7.1: Use official MOTAC SVG Logo.
                                Ensure _partials.macros renders the correct logo and the 'fill' is appropriate
                                for the background. Or replace with a direct <img> tag.
                                The height="32" here is an example.
                            --}}
                            <span class="app-brand-logo demo">
                                {{-- Assuming _partials.macros correctly renders the MOTAC logo SVG --}}
                                {{-- The `withbg` parameter might need adjustment based on actual SVG and desired styling. --}}
                                @include('_partials.macros', [
                                    'height' => 32,
                                    'withbg' =>
                                        'fill: var(--bs-primary);' /* Using Bootstrap primary which should be MOTAC Blue */,
                                ])
                            </span>
                            <span
                                class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                        </a>
                    </div>
                    {{-- /Logo --}}

                    <h3 class="mb-1 text-center fw-semibold">{{ __('Selamat Datang!') }}</h3>
                    <p class="mb-4 text-center text-muted">
                        {{ __('Sila log masuk ke akaun Sistem Pengurusan Sumber Bersepadu MOTAC anda.') }}
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success mb-3 py-2 small" role="alert"> {{-- Adjusted padding and size --}}
                            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="login-email" class="form-label">{{ __('E-mel / ID Pengguna') }}</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="login-email"
                                name="email" placeholder="{{ __('contoh: pengguna@motac.gov.my') }}" autofocus
                                value="{{ old('email') }}" aria-describedby="emailHelp">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="login-password">{{ __('Kata Laluan') }}</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                                        <small>{{ __('Lupa Kata Laluan?') }}</small>
                                    </a>
                                @endif
                            </div>
                            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                <input type="password" id="login-password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer toggle-password">
                                    <i class="bi bi-eye-slash-fill"></i> {{-- Bootstrap Icon, default to slash --}}
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                                    {{ old('remember') ? 'checked' : '' }} checked>
                                <label class="form-check-label" for="remember-me">
                                    {{ __('Ingat Saya') }}
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100" type="submit">
                            <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Log Masuk') }}
                        </button>
                    </form>

                    {{--
                    Optional: Link to registration if self-registration is allowed for this system.
                    The system design implies user creation is admin-driven.
                    If registration is open:
                    <p class="text-center">
                        <span>{{ __('Pengguna baru?') }}</span>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            <span>{{ __('Cipta akaun') }}</span>
                        </a>
                        @endif
                    </p>
                    --}}
                </div>
            </div>
            {{-- /Login Form --}}
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
                    const input = this.previousElementSibling; // Get the input field
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
