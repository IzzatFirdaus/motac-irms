{{-- resources/views/loan-transactions/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Transaksi Pinjaman #') . $loanTransaction->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Butiran Transaksi Pinjaman Peralatan ICT') }} #{{ $loanTransaction->id }}
                    </h1>
                    {{-- This route name is already fully qualified and correct --}}
                    <a href="{{ route('resource-management.bpm.loan-transactions.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-list-ul me-1"></i> {{__('Senarai Semua Transaksi')}}
                    </a>
                </div>

                @include('_partials._alerts.alert-general') {{-- Ensuring consistent alert partial --}}

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Maklumat Asas Transaksi') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 text-muted">{{ __('ID Permohonan Pinjaman Berkaitan:') }}</dt>
                            <dd class="col-sm-8">
                                {{-- This route 'loan-applications.show' is global and correct --}}
                                <a href="{{ route('loan-applications.show', $loanTransaction->loanApplication) }}" class="text-decoration-none fw-medium">
                                    #{{ $loanTransaction->loan_application_id ?? __('N/A') }}
                                </a>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('Jenis Transaksi:') }}</dt>
                            <dd class="col-sm-8">
                                <span class="badge rounded-pill {{ $loanTransaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}">
                                    {{ e($loanTransaction->type_label ?? Str::title(str_replace('_', ' ', $loanTransaction->type))) }}
                                </span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('Status Transaksi:') }}</dt>
                            <dd class="col-sm-8">
                                <x-loan-transaction-status-badge :status="$loanTransaction->status" />
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tarikh & Masa Transaksi Dicatat:') }}</dt>
                            <dd class="col-sm-8">{{ optional($loanTransaction->transaction_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                        </dl>

                        @if ($loanTransaction->loanTransactionItems->isNotEmpty())
                            <h3 class="h6 fw-semibold mt-4 mb-2 pt-2 border-top">{{ __('Item Peralatan Terlibat Dalam Transaksi Ini') }}</h3>
                            <ul class="list-group list-group-flush">
                                @foreach ($loanTransaction->loanTransactionItems as $item)
                                    <li class="list-group-item px-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-start py-2">
                                        <div>
                                            {{-- This route 'equipment.show' is global and correct --}}
                                            <a href="{{ route('equipment.show', $item->equipment_id) }}" class="text-decoration-none fw-medium">
                                                {{ e(optional($item->equipment)->brand_model_serial ?? (optional($item->equipment)->tag_id ?? __('Peralatan ID: ') . $item->equipment_id)) }}
                                            </a>
                                            <small class="d-block text-muted">{{__('Kuantiti')}}: {{ $item->quantity_transacted }}</small>
                                        </div>
                                        @if ($loanTransaction->isReturn())
                                            <span class="badge bg-light text-dark border rounded-pill mt-1 mt-sm-0">
                                                {{-- Assuming 'condition_on_return_label' is an accessor on LoanTransactionItem model --}}
                                                {{__('Keadaan Semasa Dipulangkan')}}: {{ e($item->condition_on_return_label ?? __('Tidak Dinyatakan')) }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                @if ($loanTransaction->isIssue())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Pengeluaran') }}</h2>
                        </div>
                        <div class="card-body p-4 small">
                            <dl class="row g-3">
                                <dt class="col-sm-4 text-muted">{{ __('Pegawai Pengeluar (BPM):') }}</dt>
                                <dd class="col-sm-8">{{ e(optional($loanTransaction->issuingOfficer)->name ?? __('N/A')) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Peralatan Diterima Oleh:') }}</dt>
                                <dd class="col-sm-8">{{ e(optional($loanTransaction->receivingOfficer)->name ?? __('N/A')) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Tarikh & Masa Sebenar Pengeluaran:') }}</dt>
                                <dd class="col-sm-8">{{ optional($loanTransaction->issue_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Aksesori Dikeluarkan:') }}</dt>
                                <dd class="col-sm-8">{{ implode(', ', $loanTransaction->accessories_checklist_on_issue ?: ['-']) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Catatan Pengeluaran:') }}</dt>
                                <dd class="col-sm-8" style="white-space: pre-wrap;">{{ e($loanTransaction->issue_notes ?: '-') }}</dd>
                            </dl>
                        </div>
                    </div>
                @endif

                @if ($loanTransaction->isReturn())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Pemulangan') }}</h2>
                        </div>
                        <div class="card-body p-4 small">
                             <dl class="row g-3">
                                <dt class="col-sm-4 text-muted">{{ __('Peralatan Dipulangkan Oleh:') }}</dt>
                                <dd class="col-sm-8">{{ e(optional($loanTransaction->returningOfficer)->name ?? __('N/A')) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Pemulangan Diterima Oleh (Pegawai BPM):') }}</dt>
                                <dd class="col-sm-8">{{ e(optional($loanTransaction->returnAcceptingOfficer)->name ?? __('N/A')) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Tarikh & Masa Sebenar Pemulangan:') }}</dt>
                                <dd class="col-sm-8">{{ optional($loanTransaction->return_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Aksesori Dipulangkan:') }}</dt>
                                <dd class="col-sm-8">{{ implode(', ', $loanTransaction->accessories_checklist_on_return ?: ['-']) }}</dd>

                                <dt class="col-sm-4 text-muted">{{ __('Catatan Pemulangan:') }}</dt>
                                <dd class="col-sm-8" style="white-space: pre-wrap;">{{ e($loanTransaction->return_notes ?: '-') }}</dd>
                            </dl>

                            @if ($loanTransaction->status === \App\Models\LoanTransaction::STATUS_RETURNED_DAMAGED)
                                <div class="alert alert-warning mt-3 small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    {{ __('Status Penemuan Semasa Pulangan: Peralatan Ditemui Rosak.') }}
                                </div>
                            @elseif ($loanTransaction->status === \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST)
                                <div class="alert alert-danger mt-3 small" role="alert">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                    {{ __('Status Penemuan Semasa Pulangan: Peralatan Dilaporkan Hilang.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    {{-- This route 'loan-applications.show' is global and correct --}}
                    <a href="{{ route('loan-applications.show', $loanTransaction->loanApplication) }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle me-1"></i>
                        {{ __('Kembali ke Butiran Permohonan') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
