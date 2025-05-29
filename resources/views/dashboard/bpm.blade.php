{{-- resources/views/dashboard/bpm.blade.php --}}
@extends('layouts.app') {{-- Assuming layouts.app is your main application layout --}}

@section('title', __('Papan Pemuka Staf BPM'))

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-body">{{ __('Papan Pemuka Staf BPM (Pengurusan Peralatan)') }}</h1>
        {{-- Optional: Quick links for BPM staff --}}
        <div>
            {{-- @if(Route::has('resource-management.admin.equipment-admin.create'))
            <a href="{{ route('resource-management.admin.equipment-admin.create') }}" class="btn btn-primary btn-sm shadow-sm me-2">
                <i class="ti ti-plus ti-xs me-1"></i> {{ __('Tambah Peralatan Baharu') }}
            </a>
            @endif
            @if(Route::has('resource-management.admin.equipment-admin.index'))
            <a href="{{ route('resource-management.admin.equipment-admin.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="ti ti-list-details ti-xs me-1"></i> {{ __('Lihat Inventori Penuh') }}
            </a>
            @endif --}}
        </div>
    </div>

    {{-- Section for Outstanding Loan Applications (Pending Issuance/Action by BPM) --}}
    {{-- System Design 6.2: BPM Equipment Staff Interface (processing equipment issuance and returns) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Permohonan Pinjaman Menunggu Tindakan Pengeluaran') }}</h6>
                    {{-- Link to a page showing all such applications if applicable --}}
                </div>
                <div class="card-body p-0">
                    {{-- This Livewire component should list applications approved and ready for issuance. --}}
                    @livewire('resource-management.admin.bpm.outstanding-loans')
                </div>
            </div>
        </div>
    </div>

    {{-- Section for Currently Issued Loans (for tracking returns) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Status Peralatan ICT Sedang Dipinjam') }}</h6>
                     {{-- Link to a page showing all issued items if applicable --}}
                </div>
                <div class="card-body p-0">
                    {{-- This Livewire component lists items currently on loan and perhaps due for return. --}}
                    @livewire('resource-management.admin.bpm.issued-loans')
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for other relevant BPM sections --}}
    {{--
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">{{ __('Ringkasan Stok Inventori') }}</h6></div>
                <div class="card-body">
                    <p>{{ __('Laptop Tersedia:') }} <strong>XX</strong> {{ __('unit') }}</p>
                    <p>{{ __('Projektor Tersedia:') }} <strong>YY</strong> {{ __('unit') }}</p>
                    <a href="{{-- route('resource-management.admin.equipment-admin.index') --}}"{{-- class="btn btn-sm btn-info">{{ __('Lihat Inventori Terperinci') }}</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3"><h6 class="m-0 fw-bold text-primary">{{ __('Peralatan Perlu Selenggaraan') }}</h6></div>
                <div class="card-body">
                     <p>{{ __('Senarai peralatan yang ditanda untuk selenggaraan akan dipaparkan di sini.') }}</p>
                </div>
            </div>
        </div>
    </div>
    --}}
</div>
@endsection

@push('page-style')
    {{-- Page-specific styles for the BPM dashboard, if any --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/bpm-dashboard.css') }}"> --}}
@endpush

@push('page-script')
    {{-- Page-specific JavaScript for the BPM dashboard, if any --}}
    {{-- <script src="{{ asset('assets/js/bpm-dashboard.js') }}"></script> --}}
@endpush
