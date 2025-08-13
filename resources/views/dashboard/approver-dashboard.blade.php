{{-- resources/views/dashboard/approver-dashboard.blade.php --}}
{{-- Approver Dashboard for MOTAC IRMS --}}

@extends('layouts.app')

@section('title', __('dashboard.approver_title'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.approver_tasks_title') }}</h1>
        @if (Route::has('approvals.history'))
            <a href="{{ route('approvals.history', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"><i class="bi bi-clock-history me-1"></i>{{ __('dashboard.view_my_approval_history') }}</a>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-card-checklist me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary">{{ __('dashboard.apps_awaiting_your_action') }}</h6></div>
                <div class="card-body p-0">
                    {{-- Livewire component for user approval dashboard --}}
                    @livewire('resource-management.approval.approval-dashboard', ['userId' => Auth::id(), 'defaultStatusFilter' => 'pending', 'showFilters' => true])
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-graph-up me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary">{{ __('dashboard.personal_approval_stats') }}</h6></div>
                <div class="card-body">
                    <p class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill me-2 text-success"></i>{{ __('dashboard.total_approved') }}<strong class="text-dark ms-1">{{ $approved_last_30_days ?? __('common.not_available') }}</strong></p>
                    <p class="mb-0 d-flex align-items-center"><i class="bi bi-x-circle-fill me-2 text-danger"></i>{{ __('dashboard.total_rejected') }}<strong class="text-dark ms-1">{{ $rejected_last_30_days ?? __('common.not_available') }}</strong></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex align-items-center"><i class="bi bi-info-circle-fill me-2 text-primary"></i><h6 class="m-0 fw-bold text-primary">{{ __('dashboard.approval_guidance_title') }}</h6></div>
                <div class="card-body">
                    <p class="small text-muted mb-3">{{ __('dashboard.approval_guidance_text') }}</p>
                    @if (config('system_links.approval_guidelines_url'))
                        <a href="{{ config('system_links.approval_guidelines_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info d-inline-flex align-items-center"><i class="bi bi-book-half me-1"></i>{{ __('dashboard.read_full_guidelines') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
