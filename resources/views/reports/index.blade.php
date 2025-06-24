{{-- resources/views/reports/index.blade.php --}}
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

        {{-- Equipment Inventory Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-tools fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.equipment_inventory.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.equipment_inventory.description') }}</p>
                    <a href="{{ route('reports.equipment-inventory') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Email Application Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                     <div class="mb-2"><i class="bi bi-envelope-at-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.email_applications.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.email_applications.description') }}</p>
                    <a href="{{ route('reports.email-accounts') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- User Activity Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-person-lines-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.user_activity.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.user_activity.description') }}</p>
                    <a href="{{ route('reports.activity-log') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Loan Application Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-journal-arrow-down fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.loan_applications.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.loan_applications.description') }}</p>
                    <a href="{{ route('reports.loan-applications') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Loan History Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                     <div class="mb-2"><i class="bi bi-clock-history fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('reports.loan_history.title') }}</h3>
                    <p class="card-text small text-muted mb-4">{{ __('reports.loan_history.description') }}</p>
                    <a href="{{ route('reports.loan-history') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
