{{-- resources/views/dashboard/approver.blade.php --}}
@extends('layouts.app') {{-- Assuming layouts.app is your main application layout --}}

@section('title', __('Papan Pemuka Kelulusan'))

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-body">{{ __('Tugasan Kelulusan Anda') }}</h1>
        {{-- Optional quick link for approvers
        <a href="{{ route('approvals.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-success">
            <i class="ti ti-history ti-xs me-1"></i> {{ __('Lihat Sejarah Kelulusan') }}
        </a>
        --}}
    </div>

    {{-- Main Content: Pending Approvals List --}}
    {{-- System Design 6.2: Approver dashboard has a consolidated list of pending approvals. --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Permohonan Menunggu Tindakan Anda') }}</h6>
                </div>
                <div class="card-body p-0"> {{-- Removed default padding to let Livewire component control it --}}
                    {{-- The ApprovalDashboard Livewire component handles fetching and displaying approvals. --}}
                    @livewire('approval-dashboard')
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for additional sections relevant to an Approver --}}
    {{-- For example, summary of approvals processed, quick guides, etc. --}}
    {{--
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Kelulusan Peribadi') }}</h6>
                </div>
                <div class="card-body">
                    <p>{{ __('Jumlah Permohonan Diluluskan (30 Hari Terakhir):') }} <strong>{{ $approved_last_30_days ?? 0 }}</strong></p>
                    <p>{{ __('Jumlah Permohonan Ditolak (30 Hari Terakhir):') }} <strong>{{ $rejected_last_30_days ?? 0 }}</strong></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Panduan Ringkas Kelulusan') }}</h6>
                </div>
                <div class="card-body">
                    <p class="small">{{ __('Sila pastikan semua butiran permohonan disemak dengan teliti sebelum membuat keputusan.') }}</p>
                    <a href="#" class="btn btn-sm btn-outline-info">{{ __('Baca Garis Panduan Penuh') }}</a>
                </div>
            </div>
        </div>
    </div>
    --}}
</div>
@endsection

@push('page-style')
    {{-- Page-specific styles for the approver dashboard, if any --}}
    {{-- Example: <link rel="stylesheet" href="{{ asset('assets/css/approver-dashboard.css') }}"> --}}
@endpush

@push('page-script')
    {{-- Page-specific JavaScript for the approver dashboard, if any --}}
    {{-- Example: <script src="{{ asset('assets/js/approver-dashboard.js') }}"></script> --}}
@endpush
