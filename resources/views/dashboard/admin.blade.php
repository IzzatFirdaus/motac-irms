@extends('layouts.app')

@section('title', __('dashboard.admin_title'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.admin_title') }}</h1>
    </div>

    {{-- Statistics Row --}}
    <div class="row">
        {{-- Total Users Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                {{ __('dashboard.total_users') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $users_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                    @if (Route::has('settings.users.index'))
                    <a href="{{ route('settings.users.index') }}" class="stretched-link" title="{{ __('dashboard.manage_users') }}"></a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pending Approvals Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                {{ __('dashboard.pending_approvals') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_approvals_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-2 text-gray-300"></i>
                        </div>
                    </div>
                    @if (Route::has('approvals.index'))
                    <a href="{{ route('approvals.index') }}" class="stretched-link" title="{{ __('dashboard.view_all_tasks') }}"></a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ICT Equipment Available Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                {{ __('dashboard.available_equipment') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_available_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                     @if (Route::has('resource-management.equipment-admin.index'))
                        <a href="{{ route('resource-management.equipment-admin.index') }}" class="stretched-link" title="{{ __('dashboard.manage_inventory') }}"></a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Total ICT Equipment on Loan Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2 motac-card">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                {{ __('dashboard.loaned_equipment') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_on_loan_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-truck fs-2 text-gray-300"></i>
                        </div>
                    </div>
                    @if (Route::has('resource-management.bpm.issued-loans'))
                        <a href="{{ route('resource-management.bpm.issued-loans') }}" class="stretched-link" title="{{ __('dashboard.view_active_loans') }}"></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row for Application Statistics --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm motac-card h-100">
                <div class="card-header py-3 motac-card-header d-flex align-items-center">
                    <i class="bi bi-envelope-paper-fill me-2 text-primary"></i>
                    <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.email_stats_title') }}</h6>
                </div>
                <div class="card-body motac-card-body">
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-check-circle-fill me-2 text-success"></i>{{ __('common.completed') }}</span> <strong class="text-dark">{{ $email_completed_count ?? 0 }}</strong></p>
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-hourglass-top me-2 text-warning"></i>{{ __('common.in_process') }}</span> <strong class="text-dark">{{ $email_pending_count ?? 0 }}</strong></p>
                    <p class="mb-0 d-flex justify-content-between"><span><i class="bi bi-x-circle-fill me-2 text-danger"></i>{{ __('common.rejected') }}</span> <strong class="text-dark">{{ $email_rejected_count ?? 0 }}</strong></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm motac-card h-100">
                <div class="card-header py-3 motac-card-header d-flex align-items-center">
                    <i class="bi bi-laptop-fill me-2 text-primary"></i>
                    <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.loan_stats_title') }}</h6>
                </div>
                <div class="card-body motac-card-body">
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-truck me-2 text-info"></i>{{ __('common.on_loan') }}</span> <strong class="text-dark">{{ $loan_issued_count ?? 0 }}</strong></p>
                    <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-patch-check-fill me-2 text-primary"></i>{{ __('common.approved_pending_issuance') }}</span> <strong class="text-dark">{{ $loan_approved_pending_issuance_count ?? 0 }}</strong></p>
                    <p class="mb-0 d-flex justify-content-between"><span><i class="bi bi-box-arrow-in-left me-2 text-success"></i>{{ __('common.returned') }}</span> <strong class="text-dark">{{ $loan_returned_count ?? 0 }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity / Approval Tasks --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4 motac-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i>{{ __('dashboard.latest_tasks_title') }}
                    </h6>
                    @if (Route::has('approvals.index'))
                        <a href="{{route('approvals.index')}}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{__('dashboard.view_all_tasks')}}</a>
                    @endif
                </div>
                <div class="card-body motac-card-body p-0">
                    {{-- This Livewire component for the list remains, as it handles its own logic (e.g., pagination, filtering) --}}
                    @livewire('resource-management.approval.dashboard', ['displayLimit' => 5, 'showFilters' => false])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
