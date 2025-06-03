{{-- resources/views/dashboard/bpm.blade.php --}}
@extends('layouts.app')

@section('title', __('Papan Pemuka Staf BPM'))

@section('content')
    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Papan Pemuka Staf BPM (Pengurusan Peralatan ICT)') }}</h1>
            <div>
                @if (Route::has('admin.equipment.create'))
                    <a href="{{ route('admin.equipment.create') }}"
                        class="btn btn-primary btn-sm shadow-sm me-2 motac-btn-primary d-inline-flex align-items-center">
                        <i class="bi bi-plus-circle-fill me-1"></i> {{-- Bootstrap Icon --}}
                        {{ __('Tambah Peralatan Baharu') }}
                    </a>
                @endif
                @if (Route::has('admin.equipment.index'))
                    <a href="{{ route('admin.equipment.index') }}"
                        class="btn btn-outline-secondary btn-sm shadow-sm motac-btn-outline d-inline-flex align-items-center">
                        <i class="bi bi-list-ul me-1"></i> {{-- Bootstrap Icon --}}
                        {{ __('Lihat Inventori Penuh') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Section for Outstanding Loan Applications (Pending Issuance/Action by BPM) --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm motac-card">
                    <div
                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                        <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                           <i class="bi bi-box-arrow-up-right me-2"></i> {{-- Bootstrap Icon --}}
                           {{ __('Permohonan Pinjaman Menunggu Tindakan Pengeluaran') }}
                        </h6>
                        @if (Route::has('admin.loans.pending-issuance'))
                            <a href="{{ route('admin.loans.pending-issuance') }}"
                                class="btn btn-sm btn-outline-primary motac-btn-outline">
                                {{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @livewire('resource-management.admin.bpm.outstanding-loans', [
                            'itemsPerPage' => 10,
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Section for Currently Issued Loans (for tracking returns) --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm motac-card">
                    <div
                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                        <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                            <i class="bi bi-box-arrow-in-left me-2"></i> {{-- Bootstrap Icon --}}
                            {{ __('Status Peralatan ICT Sedang Dipinjam (Menunggu Pemulangan)') }}</h6>
                        @if (Route::has('admin.loans.issued'))
                            <a href="{{ route('admin.loans.issued') }}"
                                class="btn btn-sm btn-outline-primary motac-btn-outline">
                                {{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @livewire('resource-management.admin.bpm.issued-loans', [
                            'itemsPerPage' => 10,
                            'highlightOverdue' => true,
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Optional: BPM Staff Specific Summary Widgets --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100 motac-card">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-archive-fill me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                        <h6 class="m-0 fw-bold text-primary">{{ __('Ringkasan Stok Inventori') }}</h6>
                    </div>
                    <div class="card-body motac-card-body">
                        <p class="mb-2 d-flex align-items-center"><i
                                class="bi bi-laptop-fill me-2 text-success"></i>{{ __('Laptop Tersedia:') }}
                            <strong class="text-dark ms-1">{{ $availableLaptopsCount ?? __('N/A') }}</strong>
                            <span class="ms-1">{{ __('unit') }}</span></p>
                        <p class="mb-2 d-flex align-items-center"><i
                                class="bi bi-projector-fill me-2 text-success"></i>{{ __('Projektor Tersedia:') }} <strong
                                class="text-dark ms-1">{{ $availableProjectorsCount ?? __('N/A') }}</strong>
                            <span class="ms-1">{{ __('unit') }}</span></p>
                        <p class="mb-3 d-flex align-items-center"><i
                                class="bi bi-printer-fill me-2 text-success"></i>{{ __('Pencetak Tersedia:') }} <strong
                                class="text-dark ms-1">{{ $availablePrintersCount ?? __('N/A') }}</strong>
                            <span class="ms-1">{{ __('unit') }}</span></p>
                        @if (Route::has('admin.equipment.index'))
                            <a href="{{ route('admin.equipment.index') }}" class="btn btn-sm btn-outline-info motac-btn-outline d-inline-flex align-items-center"><i
                                    class="bi bi-search me-1"></i>{{ __('Lihat Inventori Terperinci') }}</a> {{-- Changed icon --}}
                        @endif
                        @if (!isset($availableLaptopsCount))
                            <p class="text-muted small mt-3">
                                {{ __('Data stok akan dipaparkan di sini.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100 motac-card">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-tools me-2 text-primary"></i> {{-- Bootstrap Icon --}}
                        <h6 class="m-0 fw-bold text-primary">{{ __('Peralatan Perlu Selenggaraan') }}</h6>
                    </div>
                    <div class="card-body motac-card-body d-flex align-items-center justify-content-center" style="min-height: 150px;"> {{-- Centering placeholder --}}
                        <p class="text-muted small text-center">
                            <i class="bi bi-tools fs-2 d-block mb-2"></i>
                            {{ __('Senarai peralatan yang ditanda untuk selenggaraan atau kerosakan akan dipaparkan di sini.') }}
                        </p>
                        {{-- @livewire('resource-management.admin.bpm.maintenance-list') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @push('page-style') --}}
{{-- @endpush --}}

{{-- @push('page-script') --}}
    {{-- <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     console.log("BPM Staff dashboard scripts initialized.");
        // });
    // </script> --}}
{{-- @endpush --}}
