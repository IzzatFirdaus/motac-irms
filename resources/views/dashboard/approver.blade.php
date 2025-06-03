{{-- resources/views/dashboard/approver.blade.php --}}
@extends('layouts.app')

@section('title', __('Papan Pemuka Kelulusan'))

@section('content')
    <div class="container-fluid py-4"> {{-- Added py-4 --}}
        {{-- Page Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Tugasan Kelulusan Anda') }}</h1>
            @if (Route::has('approvals.history'))
                <a href="{{ route('approvals.history', ['status' => 'all']) }}" {{-- Changed default to all, or remove status filter --}}
                    class="btn btn-sm btn-outline-secondary motac-btn-outline d-inline-flex align-items-center">
                    <i class="bi bi-clock-history me-1"></i> {{-- Bootstrap Icon --}}
                    {{ __('Lihat Sejarah Kelulusan Saya') }}
                </a>
            @endif
        </div>

        {{-- Main Content: Pending Approvals List --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4 motac-card">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-card-checklist me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                        <h6 class="m-0 fw-bold text-primary">{{ __('Permohonan Menunggu Tindakan Anda') }}</h6>
                    </div>
                    <div class="card-body p-0">
                        @livewire('approval-dashboard', [
                            'userId' => Auth::id(),
                            'defaultStatusFilter' => 'pending', // Keep default filter to 'pending'
                            'showFilters' => true // Typically approvers might want to filter their own list
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Optional Sections for Approver Dashboard --}}
        <div class="row mt-2">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100 motac-card">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-graph-up me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                        <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Kelulusan Peribadi (30 Hari Terakhir)') }}</h6>
                    </div>
                    <div class="card-body motac-card-body">
                        <p class="mb-2 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ __('Jumlah Permohonan Diluluskan:') }}
                            <strong class="text-dark ms-1">{{ $approved_last_30_days ?? __('N/A') }}</strong>
                        </p>
                        <p class="mb-0 d-flex align-items-center">
                            <i class="bi bi-x-circle-fill me-2 text-danger"></i>{{ __('Jumlah Permohonan Ditolak:') }}
                            <strong class="text-dark ms-1">{{ $rejected_last_30_days ?? __('N/A') }}</strong>
                        </p>
                        @if (!isset($approved_last_30_days) && !isset($rejected_last_30_days))
                            <p class="text-muted small mt-3">
                                {{ __('Data statistik akan dipaparkan di sini apabila tersedia.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100 motac-card">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                         <i class="bi bi-info-circle-fill me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                        <h6 class="m-0 fw-bold text-primary">{{ __('Panduan Ringkas Kelulusan') }}</h6>
                    </div>
                    <div class="card-body motac-card-body">
                        <p class="small text-muted mb-3">
                            {{ __('Sila pastikan semua butiran dan dokumen sokongan (jika ada) bagi setiap permohonan disemak dengan teliti sebelum membuat sebarang keputusan kelulusan atau penolakan.') }}
                        </p>
                        @if (config('system_links.approval_guidelines_url'))
                            <a href="{{ config('system_links.approval_guidelines_url') }}" target="_blank"
                                rel="noopener noreferrer" class="btn btn-sm btn-outline-info motac-btn-outline d-inline-flex align-items-center">
                                <i class="bi bi-book-half me-1"></i>{{-- Bootstrap Icon --}}
                                {{ __('Baca Garis Panduan Penuh') }}
                            </a>
                        @else
                            <p class="small text-muted"><em>{{ __('Garis panduan penuh akan disediakan.') }}</em></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @push('page-style') --}}
{{-- Add any page-specific styles for the approver dashboard here. --}}
{{-- @endpush --}}

{{-- @push('page-script') --}}
    {{-- Add any page-specific JavaScript for the approver dashboard here. --}}
    {{-- <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     console.log("Approver dashboard scripts initialized.");
        // });
    // </script> --}}
{{-- @endpush --}}
