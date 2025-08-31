{{-- MOTAC Welcome Page --}}
{{-- Welcome page for the MOTAC Integrated Resource Management System. --}}
{{-- MYDS & MyGOVEA compliant design with semantic HTML and accessibility features --}}
{{-- Uses MYDS typography, color tokens, and grid system --}}

@extends('layouts.app')

{{-- Use the official system name as the page title --}}
@section('title', __('app.system_name'))

@section('content')
    {{-- Skip link target for accessibility --}}
    <div class="myds-container py-5" id="main-content">
        {{-- Hero Section with MYDS styling --}}
        <header class="hero-section p-5 mb-4 bg-light rounded-3 shadow-sm" role="banner">
            <div class="text-center py-4">
                {{-- System Logo with proper alt text --}}
                <div class="mb-4" role="img" aria-label="{{ __('Logo Kementerian MOTAC') }}">
                    <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                        alt="{{ __('app.motac_full_name') }}"
                        width="60"
                        height="60"
                        loading="eager">
                </div>

                {{-- System Name using MYDS typography --}}
                <h1 class="display-5 fw-semibold mb-3" style="font-family: var(--myds-font-heading); color: var(--myds-primary);">
                    {{ __('app.system_name') }}
                </h1>

                <p class="fs-5 text-muted col-md-10 mx-auto mb-4" style="font-family: var(--myds-font-body);">
                    {{ __('app.internal_system_description') }}
                </p>

                {{-- Primary CTA for authenticated users --}}
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="btn btn-myds-primary btn-lg"
                       aria-label="{{ __('Pergi ke Dashboard Utama') }}">
                        <i class="ti ti-dashboard me-2" aria-hidden="true"></i>
                        {{ __('dashboard.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-myds-primary btn-lg"
                       aria-label="{{ __('Log Masuk ke Sistem') }}">
                        <i class="ti ti-login me-2" aria-hidden="true"></i>
                        {{ __('Log Masuk') }}
                    </a>
                @endauth
            </div>
        </header>

        {{-- Features Section using MYDS 12-8-4 grid principles --}}
        <section class="features-section mb-5" aria-labelledby="features-heading">
            <h2 id="features-heading" class="h4 fw-semibold mb-4 text-center" style="font-family: var(--myds-font-heading);">
                {{ __('Perkhidmatan Utama') }}
            </h2>

            <div class="row g-4">
                {{-- ICT Equipment Loan Feature --}}
                <div class="col-md-6">
                    <article class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <header class="d-flex align-items-center mb-3">
                                <div class="feature-icon me-3"
                                     style="width: 48px; height: 48px; background: var(--myds-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-device-laptop text-white" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                </div>
                                <h3 class="h5 fw-semibold mb-0" style="font-family: var(--myds-font-heading); color: var(--myds-primary);">
                                    {{ __('dashboard.apply_ict_loan_title') }}
                                </h3>
                            </header>

                            <p class="text-muted mb-4" style="font-family: var(--myds-font-body);">
                                {{ __('dashboard.apply_ict_loan_text') }}
                            </p>

                            @auth
                                <a href="{{ route('loan-applications.create') }}"
                                   class="btn btn-outline-primary mygovea-accessible"
                                   aria-label="{{ __('Mohon Pinjaman Peralatan ICT') }}">
                                    <i class="ti ti-plus me-2" aria-hidden="true"></i>
                                    {{ __('forms.button_submit') }}
                                </a>
                            @endauth
                        </div>
                    </article>
                </div>

                {{-- Helpdesk Support Feature --}}
                <div class="col-md-6">
                    <article class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <header class="d-flex align-items-center mb-3">
                                <div class="feature-icon me-3"
                                     style="width: 48px; height: 48px; background: var(--myds-success); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-headset text-white" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                </div>
                                <h3 class="h5 fw-semibold mb-0" style="font-family: var(--myds-font-heading); color: var(--myds-success);">
                                    {{ __('dashboard.create_helpdesk_ticket_title') }}
                                </h3>
                            </header>

                            <p class="text-muted mb-4" style="font-family: var(--myds-font-body);">
                                {{ __('dashboard.create_helpdesk_ticket_text') }}
                            </p>

                            @auth
                                <div class="d-flex gap-2">
                                    <a href="{{ route('helpdesk.create') }}"
                                       class="btn btn-outline-success mygovea-accessible"
                                       aria-label="{{ __('Buat Tiket Helpdesk Baru') }}">
                                        <i class="ti ti-ticket me-1" aria-hidden="true"></i>
                                        {{ __('forms.helpdesk_button_submit_ticket') }}
                                    </a>
                                    <a href="{{ route('helpdesk.index') }}"
                                       class="btn btn-outline-info mygovea-accessible"
                                       aria-label="{{ __('Lihat Tiket Saya') }}">
                                        <i class="ti ti-list me-1" aria-hidden="true"></i>
                                        {{ __('dashboard.view_my_tickets') }}
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- Quick Links Section --}}
        <section class="quick-links-section mt-5 pt-4 border-top" aria-labelledby="quicklinks-heading">
            <h2 id="quicklinks-heading" class="h5 fw-semibold mb-3" style="font-family: var(--myds-font-heading);">
                {{ __('Pautan Pantas') }}
            </h2>

            <nav aria-label="{{ __('Pautan Pantas ke Perkhidmatan') }}">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('loan-applications.my-applications.index') }}"
                           class="text-decoration-none d-flex align-items-center mygovea-accessible"
                           style="color: var(--myds-txt-secondary); font-family: var(--myds-font-body);">
                            <i class="ti ti-file-text me-2" style="color: var(--myds-primary);" aria-hidden="true"></i>
                            {{ __('dashboard.view_my_loan_applications_title') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('contact-us') }}"
                           class="text-decoration-none d-flex align-items-center mygovea-accessible"
                           style="color: var(--myds-txt-secondary); font-family: var(--myds-font-body);">
                            <i class="ti ti-phone me-2" style="color: var(--myds-primary);" aria-hidden="true"></i>
                            {{ __('dashboard.contact_us') }}
                        </a>
                    </li>
                </ul>
            </nav>
        </section>
    </div>
@endsection

@push('page-style')
    <style>
        /* MYDS-compliant welcome page styles */
        .hero-section {
            background: linear-gradient(135deg, var(--myds-bg-secondary) 0%, var(--myds-bg-muted) 100%);
            border: 1px solid var(--myds-gray-200);
        }

        .feature-icon {
            transition: transform 0.2s ease;
        }

        .card:hover .feature-icon {
            transform: scale(1.05);
        }

        .card {
            transition: all 0.3s ease;
            border: 1px solid var(--myds-gray-200) !important;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.1) !important;
        }

        /* Dark mode adjustments */
        [data-bs-theme="dark"] .hero-section {
            background: linear-gradient(135deg, var(--myds-gray-800) 0%, var(--myds-gray-700) 100%);
            border-color: var(--myds-gray-600);
        }

        [data-bs-theme="dark"] .card {
            background-color: var(--myds-gray-800);
            border-color: var(--myds-gray-600) !important;
        }

        [data-bs-theme="dark"] .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 767px) {
            .hero-section {
                padding: 2rem 1.5rem !important;
            }

            .display-5 {
                font-size: 2rem;
            }

            .btn-lg {
                padding: 14px 28px;
                font-size: 1rem;
            }
        }
    </style>
@endpush
