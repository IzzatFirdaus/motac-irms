{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')

@section('title', __('Papan Pemuka Pengguna'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Selamat Datang, :userName!', ['userName' => Auth::user()?->name ?? __('Pengguna')]) }}</h1>
    </div>

    {{-- Quick Links/Action Cards Section --}}
    <div class="row">
        {{-- Email & User ID Application Card --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 motac-card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4 motac-card-body">
                    <div class="mb-3">
                        <i class="bi bi-envelope-plus-fill fs-1 text-primary"></i> {{-- Bootstrap Icon, fs-1 for ti-3x equivalent --}}
                    </div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('Permohonan E-mel & ID Pengguna') }}</h5> {{-- h6 for card title --}}
                    <p class="card-text small text-muted mb-3">
                        {{ __('Mohon akaun e-mel rasmi MOTAC atau ID pengguna sistem. Anda juga boleh menyemak status permohonan sedia ada.') }}
                    </p>
                    <div class="mt-auto w-100"> {{-- Added w-100 for buttons to take width if .motac-card .btn styles apply display:block --}}
                        @can('create', App\Models\EmailApplication::class)
                        <a href="{{ route('email-applications.create') }}"
                           class="btn btn-primary btn-sm mb-2 motac-btn-primary w-100">{{ __('Mohon E-mel/ID Baharu') }}</a>
                        @endcan
                        <a href="{{ route('email-applications.index') }}"
                           class="btn btn-outline-primary btn-sm motac-btn-outline w-100">{{ __('Lihat Permohonan Saya') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ICT Equipment Loan Card --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 motac-card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4 motac-card-body">
                    <div class="mb-3">
                        <i class="bi bi-laptop-fill fs-1 text-success"></i> {{-- Bootstrap Icon --}}
                    </div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('Pinjaman Peralatan ICT') }}</h5>
                    <p class="card-text small text-muted mb-3">
                        {{ __('Mohon pinjaman peralatan ICT untuk kegunaan rasmi atau semak status permohonan pinjaman anda.') }}
                    </p>
                    <div class="mt-auto w-100">
                        @can('create', App\Models\LoanApplication::class)
                        <a href="{{ route('loan-applications.create') }}"
                           class="btn btn-success btn-sm mb-2 w-100">{{ __('Mohon Pinjaman ICT Baharu') }}</a>
                        @endcan
                        <a href="{{ route('loan-applications.index') }}"
                           class="btn btn-outline-success btn-sm w-100">{{ __('Lihat Permohonan Saya') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Notifications Card --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100 motac-card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4 motac-card-body">
                    <div class="mb-3">
                        <i class="bi bi-bell-fill fs-1 text-warning"></i> {{-- Bootstrap Icon --}}
                    </div>
                    <h5 class="card-title h6 fw-semibold mb-2">{{ __('Notifikasi Sistem') }}</h5>
                    <p class="card-text small text-muted mb-3">
                        {{ __('Semak notifikasi dan makluman terkini berkaitan permohonan anda dan pengumuman sistem.') }}
                    </p>
                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-warning mt-auto btn-sm w-100">{{ __('Lihat Semua Notifikasi') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm motac-card">
                <div class="card-header py-3 motac-card-header d-flex align-items-center">
                    <i class="bi bi-journals me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                    <h6 class="card-title mb-0 fw-bold">{{ __('Ringkasan Aktiviti Terkini Anda') }}</h6>
                </div>
                <div class="card-body motac-card-body">
                    <p class="text-center text-muted small py-4 my-4">
                        <i class="bi bi-info-circle-fill fs-2 d-block mb-2"></i> {{-- Bootstrap Icon --}}
                        {{ __('Untuk paparan ringkasan permohonan aktif dan terkini, sila rujuk Papan Pemuka Utama anda atau semak notifikasi.') }}
                        {{--
                        <br> <a href="{{ route('dashboard') }}">{{ __('Pergi ke Papan Pemuka Utama') }}</a>
                        --}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-style')
    <style>
        .motac-card .card-body .btn.btn-sm {
            min-width: 180px;
            /* display: block; Defaulting to inline-block from Bootstrap, w-100 handles width */
            /* width: 100%; Applied directly to buttons if full-width is desired */
        }
        /* Text color for icons, ensure these variables are defined in your MOTAC theme */
        .text-primary { color: var(--bs-primary) !important; }
        .text-success { color: var(--bs-success) !important; }
        .text-warning { color: var(--bs-warning) !important; }

        /* Ensure button styles use MOTAC theme colors (defined via Bootstrap CSS variables) */
        /* .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-success { background-color: var(--bs-success); border-color: var(--bs-success); }
        .btn-warning { background-color: var(--bs-warning); border-color: var(--bs-warning); color: var(--bs-dark) !important; } Ensures text readability on yellow */

        /* .btn-outline-primary { border-color: var(--bs-primary); color: var(--bs-primary); }
        .btn-outline-primary:hover { background-color: var(--bs-primary); color: var(--bs-white); }
        .btn-outline-success { border-color: var(--bs-success); color: var(--bs-success); }
        .btn-outline-success:hover { background-color: var(--bs-success); color: var(--bs-white); } */
        /* Warning outline might need specific attention for hover text color if background becomes yellow */
    </style>
@endpush
