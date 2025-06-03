{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Laporan Sistem'))

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="bi bi-file-earmark-text-fill me-2"></i>{{ __('Laporan Sistem Yang Tersedia') }}
        </h1>
        {{-- Optional: Link back to Admin Dashboard --}}
        {{-- <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{__('Ke Papan Pemuka Admin')}}</a> --}}
    </div>


    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-tools fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Inventori Peralatan ICT') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Jana dan lihat laporan terperinci mengenai inventori semasa peralatan ICT.') }}
                    </p>
                    {{-- CORRECTED ROUTE NAME --}}
                    <a href="{{ route('reports.equipment-inventory') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                     <div class="mb-2"><i class="bi bi-envelope-at-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Permohonan E-mel') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Analisa status dan trend permohonan akaun e-mel dan ID pengguna.') }}
                    </p>
                    {{-- CORRECTED ROUTE NAME --}}
                    <a href="{{ route('reports.email-accounts') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-person-lines-fill fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Aktiviti Pengguna') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Pantau aktiviti pengguna dalam sistem termasuk jumlah permohonan dan kelulusan.') }}
                    </p>
                    {{-- CORRECTED ROUTE NAME (Note: 'activity-log' as per web.php) --}}
                    <a href="{{ route('reports.activity-log') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="mb-2"><i class="bi bi-journal-arrow-down fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Permohonan Pinjaman') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Semak laporan status dan sejarah permohonan pinjaman peralatan ICT.') }}
                    </p>
                    {{-- CORRECTED ROUTE NAME --}}
                    <a href="{{ route('reports.loan-applications') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                         <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm motac-card">
                <div class="card-body d-flex flex-column p-4">
                     <div class="mb-2"><i class="bi bi-clock-history fs-2 text-primary"></i></div>
                    <h3 class="h5 card-title fw-semibold mb-2">{{ __('Laporan Sejarah Pinjaman') }}</h3>
                    <p class="card-text small text-muted mb-4">
                        {{ __('Lihat sejarah terperinci transaksi pinjaman peralatan ICT (pengeluaran & pemulangan).') }}
                    </p>
                    {{-- CORRECTED ROUTE NAME --}}
                    <a href="{{ route('reports.loan-history') }}" class="btn btn-primary btn-sm mt-auto d-inline-flex align-items-center motac-btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Laporan') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Add more report links here as needed, following the card structure above --}}

    </div>
</div>
@endsection
