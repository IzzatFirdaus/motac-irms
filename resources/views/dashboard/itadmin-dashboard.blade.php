{{-- resources/views/dashboard/itadmin-dashboard.blade.php --}}
{{-- IT Admin Dashboard - Renamed from itadmin.blade.php for naming consistency --}}
@extends('layouts.app')

@section('title', __('dashboard.it_admin_title'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.it_admin_title') }}</h1>
    </div>

    {{-- Statistics Row for Helpdesk --}}
    <div class="row">
        {{-- Pending Helpdesk Tickets Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="motac-card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                {{ __('dashboard.pending_helpdesk_tickets') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_helpdesk_tickets_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                    @if (Route::has('helpdesk.admin.index'))
                    <a href="{{ route('helpdesk.admin.index') }}" class="stretched-link" title="{{ __('dashboard.manage_helpdesk_tickets') }}"></a>
                    @endif
                </div>
            </div>
        </div>

        {{-- My Assigned Helpdesk Tickets Card --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="motac-card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="motac-card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                {{ __('dashboard.my_assigned_helpdesk_tickets') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $my_assigned_helpdesk_tickets_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                     @if (Route::has('helpdesk.admin.index'))
                    <a href="{{ route('helpdesk.admin.index', ['assigned_to_me' => true]) }}" class="stretched-link" title="{{ __('dashboard.view_my_assigned_tickets') }}"></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity / Helpdesk Tickets to Process --}}
    <div class="row">
        <div class="col-12">
            <div class="motac-card shadow-sm mb-4">
                <div class="motac-card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i>{{ __('dashboard.helpdesk_tickets_to_process_title') }}
                    </h6>
                    @if (Route::has('helpdesk.admin.index'))
                        <a href="{{route('helpdesk.admin.index')}}" class="motac-btn-outline btn-sm">{{__('dashboard.view_all_helpdesk_tickets')}}</a>
                    @endif
                </div>
                <div class="motac-card-body p-0">
                    {{-- Livewire component to list pending helpdesk tickets for IT Admin --}}
                    @livewire('helpdesk.admin.ticket-management', ['displayLimit' => 5, 'defaultStatusFilter' => 'open'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

