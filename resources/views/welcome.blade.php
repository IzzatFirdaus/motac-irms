{{-- MOTAC Welcome Page --}}
{{-- Welcome page for the MOTAC Integrated Resource Management System. --}}
{{-- Uses system standards: logo display, Bootstrap 5, and MOTAC theming. --}}
{{-- All text uses translation keys for bilingual English/Malay switching --}}

@extends('layouts.app')

{{-- Use the official system name as the page title --}}
@section('title', __('app.system_name'))

@section('content')
    <div class="container py-5">
        {{-- System Introduction Section --}}
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
            <div class="container-fluid py-4 text-center">
                {{-- System Logo --}}
                <div class="mb-4">
                    <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                        alt="{{ __('app.motac_full_name') }}"
                        style="height: 60px; width: auto;">
                </div>

                {{-- System Name --}}
                <h1 class="display-5 fw-bold app-brand-text">
                    {{ __('app.system_name') }}
                </h1>
                <p class="fs-5 text-muted col-md-10 mx-auto">
                    {{ __('app.internal_system_description') }}
                </p>
                {{-- Show dashboard button for authenticated users --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg motac-btn-primary">
                        <i class="bi bi-speedometer2 me-2"></i> {{ __('dashboard.dashboard') }}
                    </a>
                @endauth
            </div>
        </div>

        {{-- Features Section --}}
        <div class="row g-4 mb-5">
            {{-- ICT Equipment Loan Feature --}}
            <div class="col-md-6">
                <div class="card h-100 shadow-sm motac-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3 text-primary">
                            <i class="bi bi-laptop me-2"></i>{{ __('dashboard.apply_ict_loan_title') }}
                        </h2>
                        <p class="small">
                            {{ __('dashboard.apply_ict_loan_text') }}
                        </p>
                        @auth
                            <a href="{{ route('loan-applications.create') }}" class="btn btn-outline-primary btn-sm motac-btn-outline">
                                <i class="bi bi-handbag-fill me-1"></i> {{ __('forms.button_submit') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Helpdesk Support/Ticketing Feature --}}
            <div class="col-md-6">
                <div class="card h-100 shadow-sm motac-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3 text-success">
                            <i class="bi bi-headset me-2"></i>{{ __('dashboard.create_helpdesk_ticket_title') }}
                        </h2>
                        <p class="small">
                            {{ __('dashboard.create_helpdesk_ticket_text') }}
                        </p>
                        @auth
                            <a href="{{ route('helpdesk.create') }}" class="btn btn-outline-success btn-sm motac-btn-outline">
                                <i class="bi bi-ticket-detailed me-1"></i> {{ __('forms.helpdesk_button_submit_ticket') }}
                            </a>
                            <a href="{{ route('helpdesk.index') }}" class="btn btn-outline-info btn-sm ms-2 motac-btn-outline">
                                <i class="bi bi-card-checklist me-1"></i> {{ __('dashboard.view_my_tickets') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Useful Resources and Contacts --}}
        <div class="mt-5 pt-4 border-top">
            <h3 class="h5 fw-semibold mb-3">{{ __('dashboard.notifications_title') }}</h3>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="#" class="text-decoration-none">
                        <i class="bi bi-book-half me-2"></i>{{ __('dashboard.view_my_loan_applications_title') }}
                    </a>
                </li>
                <li class="mb-2">
                    <a href="#" class="text-decoration-none">
                        <i class="bi bi-headset me-2"></i>{{ __('dashboard.contact_us') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        /* System standard: app-brand-text color */
        .app-brand-text {
            color: var(--bs-primary) !important;
            letter-spacing: 0.5px;
        }
        /* Custom motif for MOTAC cards if needed */
        .motac-card {
            border-radius: 1rem;
        }
    </style>
@endpush
