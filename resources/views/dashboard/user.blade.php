@extends('layouts.app')

@section('title', __('dashboard.user_title'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.welcome_user', ['userName' => Auth::user()?->name ?? __('User')]) }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3"><i class="bi bi-envelope-plus-fill fs-1 text-primary"></i></div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.apply_email_id_title') }}</h5>
                    <p class="card-text small text-muted mb-3">{{ __('dashboard.apply_email_id_text') }}</p>
                    <div class="mt-auto w-100">
                        @can('create', App\Models\EmailApplication::class)<a href="{{ route('email-applications.create') }}" class="btn btn-primary btn-sm mb-2 w-100">{{ __('dashboard.apply_new_email_id') }}</a>@endcan
                        <a href="{{ route('email-applications.index') }}" class="btn btn-outline-primary btn-sm w-100">{{ __('dashboard.view_my_email_apps') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3"><i class="bi bi-laptop-fill fs-1 text-success"></i></div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.apply_loan_title') }}</h5>
                    <p class="card-text small text-muted mb-3">{{ __('dashboard.apply_loan_text') }}</p>
                    <div class="mt-auto w-100">
                        @can('create', App\Models\LoanApplication::class)<a href="{{ route('loan-applications.create') }}" class="btn btn-success btn-sm mb-2 w-100">{{ __('dashboard.apply_new_loan') }}</a>@endcan
                        <a href="{{ route('loan-applications.index') }}" class="btn btn-outline-success btn-sm w-100">{{ __('dashboard.view_my_loan_apps') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3"><i class="bi bi-bell-fill fs-1 text-warning"></i></div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('dashboard.notifications_title') }}</h5>
                    <p class="card-text small text-muted mb-3">{{ __('dashboard.notifications_text') }}</p>
                    <a href="{{ route('notifications.index') }}" class="btn btn-warning mt-auto btn-sm w-100">{{ __('dashboard.view_all_notifications') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-journals me-2 text-primary"></i><h6 class="card-title mb-0 fw-bold">{{ __('dashboard.your_recent_activity_summary') }}</h6></div>
                <div class="card-body"><p class="text-center text-muted small py-4 my-4"><i class="bi bi-info-circle-fill fs-2 d-block mb-2"></i>{{ __('dashboard.your_recent_activity_text') }}</p></div>
            </div>
        </div>
    </div>
</div>
@endsection
