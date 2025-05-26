@extends('layouts.app') {{-- Assuming layouts.app is your main Bootstrap layout [cite: 8] --}}

@section('title', __('Dashboard Staf BPM'))

@section('content')
<div class="container-fluid"> {{-- Or 'container' for fixed width [cite: 8] --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard Staf BPM') }}</h1> {{-- [cite: 8] --}}
        {{-- Optional: Links to quick actions for BPM --}}
        {{-- <a href="{{ route('resource-management.admin.equipment-admin.index') }}" class="btn btn-sm btn-outline-primary">Lihat Inventori Peralatan</a> --}}
    </div>

    {{-- Section for Outstanding Loan Applications (Pending Issuance) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Permohonan Pinjaman Menunggu Tindakan (Untuk Dikeluarkan)') }}</h6> {{-- [cite: 8] --}}
                </div>
                <div class="card-body">
                    {{-- Include the Livewire component that displays outstanding loans for BPM action --}}
                    @livewire('resource-management.admin.bpm.outstanding-loans') {{-- [cite: 8] --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Section for Currently Issued Loans --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Peralatan ICT Sedang Dipinjam') }}</h6> {{-- [cite: 8] --}}
                </div>
                <div class="card-body">
                    {{-- Include the Livewire component that displays issued loans --}}
                    @livewire('resource-management.admin.bpm.issued-loans') {{-- [cite: 8] --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Other relevant sections for BPM --}}
    {{-- For example, a summary of equipment stock levels or maintenance schedules --}}
    {{--
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Ringkasan Inventori</h6></div>
                <div class="card-body">
                    <p>Laptop Tersedia: X unit</p>
                    <p>Projektor Tersedia: Y unit</p>
                </div>
            </div>
        </div>
    </div>
    --}}
</div>
@endsection

@push('page-style') {{-- Changed from styles to page-style to match layout --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/bpm-dashboard.css') }}"> --}} {{-- [cite: 8] --}}
@endpush

@push('page-script') {{-- Changed from scripts to page-script to match layout --}}
    {{-- <script src="{{ asset('js/bpm-dashboard.js') }}"></script> --}} {{-- [cite: 8] --}}
@endpush

