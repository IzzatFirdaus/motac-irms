@extends('layouts.app') {{-- Assuming layouts.app is your main Bootstrap layout [cite: 7] --}}

@section('title', __('Dashboard Kelulusan'))

@section('content')
    <div class="container-fluid"> {{-- Or 'container' for fixed width [cite: 7] --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard Kelulusan') }}</h1> {{-- [cite: 7] --}}
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Permohonan Menunggu Tindakan Anda') }}</h6>
                    </div>
                    <div class="card-body">
                        {{-- Include the ApprovalDashboard Livewire component --}}
                        {{-- This component (App\Livewire\ApprovalDashboard) will handle fetching and displaying pending approvals --}}
                        @livewire('approval-dashboard') {{-- [cite: 7] --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- You can add other sections or information relevant to the Approver dashboard here --}}
        {{-- For example, quick stats on approvals made, links to approval history, etc. --}}
        {{--
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistik Kelulusan</h5>
                    <p class="card-text">Diluluskan Minggu Ini: X</p>
                    <p class="card-text">Ditolak Minggu Ini: Y</p>
                </div>
            </div>
        </div>
    </div>
    --}}
    </div>
@endsection

@push('page-style')
    {{-- Changed from styles to page-style to match layout --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/approver-dashboard.css') }}"> --}} {{-- [cite: 7] --}}
@endpush

@push('page-script')
    {{-- Changed from scripts to page-script to match layout --}}
    {{-- <script src="{{ asset('js/approver-dashboard.js') }}"></script> --}} {{-- [cite: 7] --}}
@endpush
