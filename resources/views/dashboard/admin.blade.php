{{-- resources/views/dashboard/admin.blade.php --}}
@extends('layouts.app') {{-- Assuming layouts.app is your main application layout --}}

@section('title', __('Papan Pemuka Pentadbir Sistem'))

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-body">{{ __('Papan Pemuka Pentadbir') }}</h1>
        {{-- Optional: Quick Action Button for Admin
        <a href="#" class="btn btn-primary btn-sm shadow-sm">
            <i class="ti ti-plus ti-xs me-1"></i> {{ __('Tindakan Utama') }}
        </a>
        --}}
    </div>

    {{-- Statistics Row --}}
    {{-- Design Language: Informative Dashboards, Bootstrap 5 cards --}}
    <div class="row">
        {{-- Total Users Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                {{ __('Jumlah Pengguna Sistem') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $users_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-users ti-2x text-muted"></i>
                        </div>
                    </div>
                    {{-- @if (Route::has('settings.users.index'))
                    <a href="{{ route('settings.users.index') }}" class="stretched-link" title="{{ __('Urus Pengguna') }}"></a>
                    @endif --}}
                </div>
            </div>
        </div>

        {{-- Pending Approvals Card (Combined for Email & Loan) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                {{ __('Permohonan Menunggu Kelulusan') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_approvals_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-hourglass-low ti-2x text-muted"></i>
                        </div>
                    </div>
                    {{-- @if (Route::has('approval-dashboard'))
                    <a href="{{ route('approval-dashboard') }}" class="stretched-link" title="{{ __('Lihat Tugasan Kelulusan') }}"></a>
                    @endif --}}
                </div>
            </div>
        </div>

        {{-- ICT Equipment Available Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                {{ __('Peralatan ICT Tersedia') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_available_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-building-warehouse ti-2x text-muted"></i>
                        </div>
                    </div>
                     {{-- @if (Route::has('resource-management.admin.equipment-admin.index'))
                        <a href="{{ route('resource-management.admin.equipment-admin.index') }}" class="stretched-link" title="{{ __('Urus Inventori Peralatan') }}"></a>
                    @endif --}}
                </div>
            </div>
        </div>

        {{-- Total ICT Equipment on Loan Card --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                {{ __('Peralatan ICT Sedang Dipinjam') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_on_loan_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti ti-transform ti-2x text-muted"></i>
                        </div>
                    </div>
                    {{-- Link to relevant report or issued loans list if available
                    @if (Route::has('resource-management.admin.bpm.issued-loans'))
                        <a href="{{ route('resource-management.admin.bpm.issued-loans') }}" class="stretched-link" title="{{ __('Lihat Senarai Pinjaman Aktif') }}"></a>
                    @endif
                    --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Row for Charts or other detailed content --}}
    {{-- System Design 6.2: Admin dashboard has overview of resource utilization and system reports --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Permohonan E-mel/ID Pengguna') }}</h6>
                </div>
                <div class="card-body">
                    {{-- Placeholder for Email Application Status Chart (e.g., using Chart.js) --}}
                    {{-- <canvas id="adminEmailStatusChart"></canvas> --}}
                    <p class="text-center text-muted small py-5">{{ __('Carta ringkasan status permohonan e-mel/ID akan dipaparkan di sini.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Pinjaman Peralatan ICT') }}</h6>
                </div>
                <div class="card-body">
                    {{-- Placeholder for Equipment Loan Trends Chart --}}
                    {{-- <canvas id="adminLoanTrendsChart"></canvas> --}}
                    <p class="text-center text-muted small py-5">{{ __('Carta trend pinjaman peralatan ICT akan dipaparkan di sini.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity or Quick Access to Management Modules --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Senarai Tugasan Kelulusan Terkini') }}</h6>
                    {{-- @if (Route::has('approval-dashboard'))
                        <a href="{{route('approval-dashboard')}}" class="btn btn-sm btn-outline-primary">{{__('Lihat Semua Tugasan')}}</a>
                    @endif --}}
                </div>
                <div class="card-body">
                    {{-- Embedding ApprovalDashboard Livewire component for a list of pending items --}}
                    {{-- Data like 'limit' can be passed to the component if it supports it. --}}
                    @livewire('approval-dashboard', ['displayLimit' => 5, 'showFilters' => false])
                    {{-- <p class="text-center text-muted small py-3">{{ __('Komponen Livewire untuk senarai tugasan kelulusan terkini akan dipaparkan di sini.') }}</p> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-script')
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}} {{-- Load Chart.js locally or via npm package --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Example Chart.js initialization (ensure Chart.js is loaded before this script)
            // You would fetch data via Livewire component or an API endpoint to populate these charts.

            // const ctxEmailAdmin = document.getElementById('adminEmailStatusChart');
            // if (ctxEmailAdmin) { new Chart(ctxEmailAdmin, { /* ... chart config ... */ }); }

            // const ctxLoanAdmin = document.getElementById('adminLoanTrendsChart');
            // if (ctxLoanAdmin) { new Chart(ctxLoanAdmin, { /* ... chart config ... */ }); }
            console.log("Admin dashboard scripts loaded. Chart placeholders are ready.");
        });
    </script>
@endpush
