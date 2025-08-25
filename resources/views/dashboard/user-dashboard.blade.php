{{-- resources/views/dashboard/user-dashboard.blade.php --}}
{{-- Regular User Dashboard - Renamed from user.blade.php for clarity and consistency --}}
@extends('layouts.app') {{-- Main application layout --}}

@section('title', __('dashboard.user_title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">
                {{ __('dashboard.welcome_user', ['userName' => Auth::user()?->name ?? __('User')]) }}</h1>
        </div>

        <div class="row">
            {{-- Apply for Helpdesk Ticket Card --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-headset fs-1 text-info"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.create_helpdesk_ticket_title') }}</h5>
                        <p class="card-text small text-muted mb-3">{{ __('dashboard.create_helpdesk_ticket_text') }}</p>
                        <div class="mt-auto w-100">
                            @can('create', App\Models\HelpdeskTicket::class)
                                <a href="{{ route('helpdesk.create') }}"
                                   class="btn btn-info mt-auto btn-sm w-100">{{ __('dashboard.create_new_ticket') }}</a>
                            @else
                                <button type="button" class="btn btn-secondary mt-auto btn-sm w-100" disabled>
                                    {{ __('dashboard.no_permission') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            {{-- View My Helpdesk Tickets Card --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-ticket-detailed-fill fs-1 text-primary"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.view_my_tickets_title') }}</h5>
                        <p class="card-text small text-muted mb-3">{{ __('dashboard.view_my_tickets_text') }}</p>
                        <div class="mt-auto w-100">
                            <a href="{{ route('helpdesk.index') }}"
                               class="btn btn-primary mt-auto btn-sm w-100">{{ __('dashboard.view_all_my_tickets') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Apply for ICT Loan Card --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-handbag-fill fs-1 text-success"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.apply_ict_loan_title') }}</h5>
                        <p class="card-text small text-muted mb-3">{{ __('dashboard.apply_ict_loan_text') }}</p>
                        <div class="mt-auto w-100">
                            @can('create', App\Models\LoanApplication::class)
                                <a href="{{ route('loan-applications.create') }}"
                                   class="btn btn-success mt-auto btn-sm w-100">{{ __('dashboard.apply_new_loan') }}</a>
                            @else
                                <button type="button" class="btn btn-secondary mt-auto btn-sm w-100" disabled>
                                    {{ __('dashboard.no_permission') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            {{-- View My Loan Applications Card --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-journal-check fs-1 text-warning"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.view_my_loan_applications_title') }}</h5>
                        <p class="card-text small text-muted mb-3">{{ __('dashboard.view_my_loan_applications_text') }}</p>
                        <div class="mt-auto w-100">
                            <a href="{{ route('loan-applications.index') }}"
                               class="btn btn-warning mt-auto btn-sm w-100">{{ __('dashboard.view_my_loan_applications') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications Card --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-bell-fill fs-1 text-warning"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.notifications_title') }}</h5>
                        <p class="card-text small text-muted mb-3">{{ __('dashboard.notifications_text') }}</p>
                        <a href="{{ route('notifications.index') }}"
                            class="btn btn-warning mt-auto btn-sm w-100">{{ __('dashboard.view_all_notifications') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-journals me-2 text-primary"></i>
                        <h6 class="card-title mb-0 fw-bold">{{ __('dashboard.your_recent_activity_summary') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-center text-muted small py-4 my-4"><i
                                class="bi bi-info-circle-fill fs-2 d-block mb-2"></i>{{ __('dashboard.your_recent_activity_text') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
