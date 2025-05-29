{{--
    resources/views/reports/index.blade.php (Refactored to Bootstrap 5)
--}}
@extends('layouts.app')

@section('title', __('Available Reports'))

@section('content')

    <div class="container py-4">

        <h2 class="h3 fw-bold mb-4">{{ __('Available Reports') }}</h2>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

            {{-- Equipment Report Link --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-semibold mb-3">{{ __('Equipment Report') }}</h3>
                        <p class="card-text text-muted mb-4">{{ __('View reports on equipment loan applications.') }}</p>
                        {{-- @can('viewEquipment', App\Http\Controllers\ReportController::class) --}}
                        <a href="{{ route('admin.reports.equipment') }}" class="btn btn-primary mt-auto">
                            {{ __('View Report') }}
                        </a>
                        {{-- @endcan --}}
                    </div>
                </div>
            </div>

            {{-- Email Accounts Report Link --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-semibold mb-3">{{ __('Email Accounts Report') }}</h3>
                        <p class="card-text text-muted mb-4">{{ __('View reports on email account applications.') }}</p>
                        {{-- @can('viewEmailAccounts', App\Http\Controllers\ReportController::class) --}}
                        <a href="{{ route('admin.reports.email-accounts') }}" class="btn btn-primary mt-auto">
                            {{ __('View Report') }}
                        </a>
                        {{-- @endcan --}}
                    </div>
                </div>
            </div>

            {{-- User Activity Report Link --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-semibold mb-3">{{ __('User Activity Report') }}</h3>
                        <p class="card-text text-muted mb-4">{{ __('View reports on user application activity.') }}</p>
                        {{-- @can('viewUserActivity', App\Http\Controllers\ReportController::class) --}}
                        <a href="{{ route('admin.reports.user-activity') }}" class="btn btn-primary mt-auto">
                            {{ __('View Report') }}
                        </a>
                        {{-- @endcan --}}
                    </div>
                </div>
            </div>

            {{-- Loan Applications Report Link --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-semibold mb-3">{{ __('Loan Applications Report') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('View reports on loan applications status and history.') }}</p>
                        {{-- @can('viewLoanApplications', App\Http\Controllers\ReportController::class) --}}
                        <a href="{{ route('admin.reports.loan-applications') }}" class="btn btn-primary mt-auto">
                            {{ __('View Report') }}
                        </a>
                        {{-- @endcan --}}
                    </div>
                </div>
            </div>

            {{-- Loan History Report Link --}}
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title fw-semibold mb-3">{{ __('Loan History Report') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('View detailed history of equipment loan transactions.') }}</p>
                        {{-- @can('viewLoanHistory', App\Http\Controllers\ReportController::class) --}}
                        <a href="{{ route('admin.reports.loan-history') }}" class="btn btn-primary mt-auto">
                            {{ __('View Report') }}
                        </a>
                        {{-- @endcan --}}
                    </div>
                </div>
            </div>

            {{-- Add more report links here as needed --}}

        </div>
    </div>
@endsection

{{-- The custom styles for .btn .btn-primary from the original file are likely superseded by Bootstrap's own styles
     or should be part of your global CSS if you are customizing Bootstrap buttons. --}}
