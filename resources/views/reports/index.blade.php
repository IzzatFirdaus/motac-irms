<<<<<<< HEAD
@extends('layouts.app')

@section('title', __('reports.page_title'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="bi bi-file-earmark-text-fill me-2"></i>{{ __('reports.page_header') }}
        </h1>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        {{-- Equipment Inventory --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-tools fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.equipment_inventory.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.equipment_inventory.description') }}</p>
                    <a href="{{ route('reports.equipment-inventory') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
=======
{{-- resources/views/reports/index.blade.php (Bootstrap 5 Version) --}}
@extends('layouts.app')

@section('title', __('Available Reports'))

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}

    <h2 class="h2 fw-bold text-dark mb-4">{{ __('Available Reports') }}</h2>

    {{-- Grid for report cards/links --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Bootstrap grid --}}

        {{-- Equipment Report Link --}}
        <div class="col">
            <div class="card h-100 shadow-sm"> {{-- Bootstrap card --}}
                <div class="card-body d-flex flex-column">
                    <h3 class="h5 card-title fw-semibold mb-3">{{ __('Equipment Report') }}</h3>
                    <p class="card-text text-muted mb-4">{{ __('View reports on equipment loan applications.') }}</p>
                    <a href="{{ route('admin.reports.equipment') }}" class="btn btn-primary mt-auto">
                        {{ __('View Report') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </a>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- Email Applications --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-envelope-at-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.email_applications.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.email_applications.description') }}</p>
                    <a href="{{ route('reports.email-accounts') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
=======
        {{-- Email Accounts Report Link --}}
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h3 class="h5 card-title fw-semibold mb-3">{{ __('Email Accounts Report') }}</h3>
                    <p class="card-text text-muted mb-4">{{ __('View reports on email account applications.') }}</p>
                    <a href="{{ route('admin.reports.email-accounts') }}" class="btn btn-primary mt-auto">
                        {{ __('View Report') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </a>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- User Activity --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-person-lines-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.user_activity.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.user_activity.description') }}</p>
                    <a href="{{ route('reports.activity-log') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
=======
        {{-- User Activity Report Link --}}
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h3 class="h5 card-title fw-semibold mb-3">{{ __('User Activity Report') }}</h3>
                    <p class="card-text text-muted mb-4">{{ __('View reports on user application activity.') }}</p>
                    <a href="{{ route('admin.reports.user-activity') }}" class="btn btn-primary mt-auto">
                        {{ __('View Report') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </a>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- Loan Applications --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-journal-arrow-down fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.loan_applications.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.loan_applications.description') }}</p>
                    <a href="{{ route('reports.loan-applications') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
=======
        {{-- Loan Applications Report Link --}}
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h3 class="h5 card-title fw-semibold mb-3">{{ __('Loan Applications Report') }}</h3>
                    <p class="card-text text-muted mb-4">{{ __('View reports on loan applications status and history.') }}</p>
                    <a href="{{ route('admin.reports.loan-applications') }}" class="btn btn-primary mt-auto">
                        {{ __('View Report') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </a>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- Loan History --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-clock-history fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.loan_history.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.loan_history.description') }}</p>
                    <a href="{{ route('reports.loan-history') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
=======
        {{-- Loan History Report Link --}}
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h3 class="h5 card-title fw-semibold mb-3">{{ __('Loan History Report') }}</h3>
                    <p class="card-text text-muted mb-4">{{ __('View detailed history of equipment loan transactions.') }}</p>
                    <a href="{{ route('admin.reports.loan-history') }}" class="btn btn-primary mt-auto">
                        {{ __('View Report') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </a>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- Utilization Summary --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-bar-chart-line-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Utilization Summary') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('Generate insights on equipment utilization rates by department or asset type.') }}</p>
                    <a href="{{ route('reports.utilization-report') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Loan Status Summary --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-pie-chart-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Loan Status Summary') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('Overview of active, pending, and returned loans across the system.') }}</p>
                    <a href="{{ route('reports.loan-status-summary') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
=======
        {{-- Add more report links here as needed --}}

    </div> {{-- End .row --}}
</div> {{-- End .container --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
@endsection
