{{-- resources/views/dashboard/admin.blade.php --}}
@extends('layouts.app') {{-- MOTAC-themed main application layout --}}

@section('title', __('Papan Pemuka Pentadbir Sistem'))

@section('content')
<div class="container-fluid py-4"> {{-- Added py-4 for consistent padding --}}
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Papan Pemuka Pentadbir') }}</h1> {{-- text-dark and fw-bold for emphasis --}}
        {{-- Optional Admin Quick Action Button --}}
        {{--
        <a href="{{-- route('some.admin.settings.route') --}}" class="btn btn-primary btn-sm shadow-sm motac-btn-primary d-inline-flex align-items-center">
            <i class="bi bi-gear-fill me-1"></i> {{-- Bootstrap Icon --}}
            {{-- __('Tetapan Sistem') --}}
        {{-- </a>
        --}}
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
                                {{ __('Jumlah Pengguna Sistem') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $users_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-gray-300"></i> {{-- Bootstrap Icon, fs-2 for ti-2x, text-gray-300 or text-muted --}}
                        </div>
                    </div>
                    {{-- Changed to settings.users.index as it's an admin-only user management page --}}
                    @if (Route::has('settings.users.index'))
                    <a href="{{ route('settings.users.index') }}" class="stretched-link" title="{{ __('Urus Pengguna Sistem') }}"></a>
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
                                {{ __('Permohonan Menunggu Kelulusan') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $pending_approvals_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-2 text-gray-300"></i> {{-- Bootstrap Icon --}}
                        </div>
                    </div>
                    @if (Route::has('approvals.index'))
                    <a href="{{ route('approvals.index') }}" class="stretched-link" title="{{ __('Lihat Semua Tugasan Kelulusan') }}"></a>
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
                                {{ __('Peralatan ICT Tersedia') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_available_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam-fill fs-2 text-gray-300"></i> {{-- Bootstrap Icon --}}
                        </div>
                    </div>
                    {{-- Corrected route name based on web.php --}}
                     @if (Route::has('resource-management.equipment-admin.index'))
                        <a href="{{ route('resource-management.equipment-admin.index') }}" class="stretched-link" title="{{ __('Urus Inventori Peralatan') }}"></a>
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
                                {{ __('Peralatan ICT Sedang Dipinjam') }}</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_on_loan_count ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-truck fs-2 text-gray-300"></i> {{-- Bootstrap Icon (alternative: bi-arrow-repeat) --}}
                        </div>
                    </div>
                    {{-- Corrected route name based on web.php (BPM's issued loans view) --}}
                    @if (Route::has('resource-management.bpm.issued-loans'))
                        <a href="{{ route('resource-management.bpm.issued-loans') }}" class="stretched-link" title="{{ __('Lihat Senarai Pinjaman Aktif') }}"></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row for Charts --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm motac-card">
                <div class="card-header py-3 motac-card-header d-flex align-items-center"> {{-- Added d-flex for icon --}}
                    <i class="bi bi-envelope-paper-fill me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                    <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Permohonan E-mel/ID Pengguna') }}</h6>
                </div>
                <div class="card-body motac-card-body" style="min-height: 300px;"> {{-- Added min-height --}}
                    <p class="text-center text-muted small py-5 my-4">{{ __('Carta ringkasan status permohonan e-mel/ID akan dipaparkan di sini.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm motac-card">
                <div class="card-header py-3 motac-card-header d-flex align-items-center">
                    <i class="bi bi-laptop-fill me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                    <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Pinjaman Peralatan ICT') }}</h6>
                </div>
                <div class="card-body motac-card-body" style="min-height: 300px;">
                    <p class="text-center text-muted small py-5 my-4">{{ __('Carta trend pinjaman peralatan ICT akan dipaparkan di sini.') }}</p>
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
                        <i class="bi bi-list-check me-2"></i>{{ __('Senarai Tugasan Kelulusan Terkini') }}
                    </h6>
                    @if (Route::has('approvals.index'))
                        <a href="{{route('approvals.index')}}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{__('Lihat Semua Tugasan')}}</a>
                    @endif
                </div>
                <div class="card-body motac-card-body p-0"> {{-- Removed padding if Livewire component handles it --}}
                    {{-- Assuming 'approval-dashboard' is the correct tag for App\Livewire\ResourceManagement\Approval\Dashboard --}}
                    @livewire('resource-management.approval.dashboard', ['displayLimit' => 5, 'showFilters' => false])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-script')
    {{-- <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     console.log("Admin dashboard scripts initialized. Chart placeholders are present.");
        // });
    </script>
@endpush
