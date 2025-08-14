@extends('layouts.app')

@section('title', __('dashboard.it_admin_title'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.it_admin_title') }}</h1>
    </div>

    {{-- Statistics Row --}}
    <div class="row">
        {{-- Pending Email Applications Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                {{ __('dashboard.pending_email_apps') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_email_applications_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-exclamation-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Processing Email Applications Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                {{ __('dashboard.processing_email_apps') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $processing_email_applications_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-paper-heart fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity / Pending Applications --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4 motac-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i>{{ __('dashboard.email_apps_to_process_title') }}
                    </h6>
                    @if (Route::has('resource-management.email-applications-admin.index'))
                        <a href="{{route('resource-management.email-applications-admin.index')}}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{__('dashboard.view_all_email_apps')}}</a>
                    @endif
                </div>
                <div class="card-body motac-card-body p-0">
                    {{-- Livewire component to list pending email applications --}}
                    @livewire('dashboard.it-admin.pending-email-applications-list')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
