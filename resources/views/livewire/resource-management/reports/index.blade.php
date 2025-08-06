<<<<<<< HEAD
{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Laporan Sistem Yang Tersedia'))

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>{{ __('Laporan Sistem Yang Tersedia') }}
        </h1>
        {{-- Optional: Link back to Admin Dashboard or other relevant page --}}
        {{-- <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"><i class="bi bi-arrow-left me-1"></i>{{__('Ke Papan Pemuka')}}</a> --}}
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-3"><i class="bi bi-archive-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Inventori Peralatan ICT') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Dapatkan gambaran keseluruhan inventori peralatan ICT yang didaftarkan dalam sistem.') }}
                    </p>
                    <a href="{{ route('reports.equipment-inventory') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- NEW: Helpdesk Ticket Report Card --}}
        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-3"><i class="bi bi-headset fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Tiket Meja Bantuan') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Semak laporan status dan sejarah tiket bantuan yang dikemukakan.') }}
                    </p>
                    <a href="{{ route('reports.helpdesk-tickets') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-3"><i class="bi bi-journal-text fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Permohonan Pinjaman') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Semak laporan status dan sejarah permohonan pinjaman peralatan ICT.') }}
                    </p>
                    <a href="{{ route('reports.loan-applications') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                     <div class="mb-3"><i class="bi bi-clock-history fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Sejarah Pinjaman') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Lihat sejarah terperinci transaksi pinjaman peralatan ICT (pengeluaran & pemulangan).') }}
                    </p>
                    <a href="{{ route('reports.loan-history') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-3"><i class="bi bi-person-check-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Aktiviti Pengguna') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Jana laporan aktiviti pengguna yang berkaitan dengan permohonan pinjaman dan kelulusan.') }}
                    </p>
                    <a href="{{ route('reports.user-activity') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
=======
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
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
