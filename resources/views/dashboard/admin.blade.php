@extends('layouts.app') {{-- Assuming layouts.app is your main Bootstrap layout [cite: 6] --}}

@section('title', __('Pentadbir Dashboard'))

@section('content')
    <div class="container-fluid"> {{-- Or 'container' for fixed width --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard Pentadbir') }}</h1> {{-- [cite: 6] --}}
            {{-- Optional: Add a button or link here, e.g., for creating something new --}}
            {{-- <a href="#" class="btn btn-primary btn-sm">Tindakan Pantas</a> --}}
        </div>

        {{-- Statistics Row --}}
        <div class="row">
            {{-- Total Users Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2"> {{-- [cite: 6] --}}
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ __('Jumlah Pengguna') }}</div> {{-- [cite: 6] --}}
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users_count ?? '0' }}</div>
                                {{-- [cite: 6] --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i> {{-- FontAwesome example --}}
                            </div>
                        </div>
                        {{-- @if (Route::has('settings.users.index'))
                        <a href="{{ route('settings.users.index') }}" class="stretched-link"></a>
                    @endif --}}
                    </div>
                </div>
            </div>

            {{-- Pending Approvals Card (Combined for Email & Loan) --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2"> {{-- [cite: 6] --}}
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    {{ __('Permohonan Menunggu Kelulusan') }}</div> {{-- [cite: 6] --}}
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending_approvals_count ?? '0' }}
                                </div> {{-- [cite: 6] --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        {{-- @if (Route::has('approval-dashboard'))
                        <a href="{{ route('approval-dashboard') }}" class="stretched-link"></a>
                    @endif --}}
                    </div>
                </div>
            </div>

            {{-- ICT Equipment Available Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2"> {{-- [cite: 6] --}}
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('Peralatan ICT Tersedia') }}</div> {{-- [cite: 6] --}}
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipment_available_count ?? '0' }}
                                </div> {{-- [cite: 6] --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-laptop-house fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        {{-- @if (Route::has('resource-management.admin.equipment-admin.index'))
                        <a href="{{ route('resource-management.admin.equipment-admin.index') }}" class="stretched-link"></a>
                    @endif --}}
                    </div>
                </div>
            </div>

            {{-- Total ICT Equipment on Loan --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('Peralatan ICT Dipinjam') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipment_on_loan_count ?? '0' }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-people-carry fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row for Charts or other content --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Ringkasan Status Permohonan Emel') }}</h6>
                        {{-- [cite: 6] --}}
                    </div>
                    <div class="card-body">
                        {{-- Placeholder for Email Application Status Chart --}}
                        {{-- <canvas id="adminEmailStatusChart"></canvas> --}}
                        <p class="text-center text-muted">{{ __('Carta akan dipaparkan di sini.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Trend Pinjaman Peralatan ICT') }}</h6>
                        {{-- [cite: 6] --}}
                    </div>
                    <div class="card-body">
                        {{-- Placeholder for Equipment Loan Trends Chart --}}
                        {{-- <canvas id="adminLoanTrendsChart"></canvas> --}}
                        <p class="text-center text-muted">{{ __('Carta akan dipaparkan di sini.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Optional: Recent Activity or Pending Approvals List (could be a Livewire component) --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Senarai Menunggu Kelulusan Terkini') }}</h6>
                    </div>
                    <div class="card-body">
                        {{-- @livewire('approval-dashboard', ['limit' => 5]) --}} {{-- Example of embedding with a limit [cite: 6] --}}
                        <p>{{ __('Komponen Livewire untuk senarai kelulusan akan dipaparkan di sini.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    {{-- Changed from scripts to page-script to match layout --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        // Example Chart.js initialization (ensure Chart.js is loaded)
        // const ctxEmailAdmin = document.getElementById('adminEmailStatusChart');
        // if (ctxEmailAdmin) { new Chart(ctxEmailAdmin, { /* ... config ... */ }); }

        // const ctxLoanAdmin = document.getElementById('adminLoanTrendsChart');
        // if (ctxLoanAdmin) { new Chart(ctxLoanAdmin, { /* ... config ... */ }); }
        // });
    </script>
@endpush
