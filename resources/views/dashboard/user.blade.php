{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app') {{-- Assuming layouts.app is your main application layout --}}

@section('title', __('Papan Pemuka Pengguna'))

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-body">{{ __('Selamat Datang, :userName!', ['userName' => Auth::user()?->name ?? __('Pengguna')]) }}</h1>
        {{-- Design Language: User-Centricity & Clarity --}}
    </div>

    {{-- Quick Links/Cards Section --}}
    {{-- Design Language: Quick Access to Common Tasks --}}
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3">
                        <i class="ti ti-mail-forward ti-3x text-primary"></i>
                    </div>
                    <h5 class="card-title mb-2">{{ __('Permohonan E-mel & ID Pengguna') }}</h5>
                    <p class="card-text small mb-3">
                        {{ __('Mohon akaun e-mel rasmi MOTAC atau ID pengguna sistem. Anda juga boleh menyemak status permohonan sedia ada.') }}
                    </p>
                    <div class="mt-auto">
                        @can('create', App\Models\EmailApplication::class) {{-- Check permission before showing create button --}}
                        <a href="{{ route('resource-management.application-forms.email.create') }}"
                           class="btn btn-primary btn-sm mb-2">{{ __('Mohon E-mel/ID Baharu') }}</a>
                        @endcan
                        <a href="{{ route('resource-management.my-applications.email.index') }}"
                           class="btn btn-outline-primary btn-sm">{{ __('Lihat Permohonan Saya') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3">
                        <i class="ti ti-device-laptop ti-3x text-success"></i>
                    </div>
                    <h5 class="card-title mb-2">{{ __('Pinjaman Peralatan ICT') }}</h5>
                    <p class="card-text small mb-3">
                        {{ __('Mohon pinjaman peralatan ICT untuk kegunaan rasmi atau semak status permohonan pinjaman anda.') }}
                    </p>
                    <div class="mt-auto">
                        @can('create', App\Models\LoanApplication::class) {{-- Check permission --}}
                        <a href="{{ route('resource-management.application-forms.loan.create') }}"
                           class="btn btn-success btn-sm mb-2">{{ __('Mohon Pinjaman ICT Baharu') }}</a>
                        @endcan
                        <a href="{{ route('resource-management.my-applications.loan.index') }}"
                           class="btn btn-outline-success btn-sm">{{ __('Lihat Permohonan Saya') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="mb-3">
                        <i class="ti ti-bell ti-3x text-warning"></i>
                    </div>
                    <h5 class="card-title mb-2">{{ __('Notifikasi Sistem') }}</h5>
                    <p class="card-text small mb-3">
                        {{ __('Semak notifikasi dan makluman terkini berkaitan permohonan anda dan pengumuman sistem.') }}
                    </p>
                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-warning mt-auto btn-sm">{{ __('Lihat Semua Notifikasi') }}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for User's Active Loans or Recent Applications Summary --}}
    {{-- This section would be similar to what was in App/Livewire/Dashboard.php if that level of detail is desired here too --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">{{ __('Ringkasan Aktiviti Terkini Anda') }}</h5>
                </div>
                <div class="card-body">
                    {{--
                        You could embed a specific Livewire component here for a summary,
                        e.g., @livewire('user-activity-summary-widget')
                        Or, if App/Livewire/Dashboard provides the main user view, this user.blade.php might be a simpler alternative
                        or a page linked FROM the main user dashboard for quick actions.
                    --}}
                    <p class="text-center text-muted small py-4">{{ __('Paparan ringkasan aktiviti dan permohonan terkini anda akan muncul di sini tidak lama lagi.') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('page-style')
    <style>
        .card-body .btn {
            min-width: 180px; /* Ensure buttons have a decent width */
        }
    </style>
@endpush
