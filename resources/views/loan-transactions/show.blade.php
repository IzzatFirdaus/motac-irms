{{-- resources/views/loan-transactions/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Transaksi Pinjaman') . ' #' . $loanTransaction->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <h2 class="h2 fw-bold text-dark mb-4">{{ __('Butiran Transaksi Pinjaman Peralatan ICT') }}
                    #{{ $loanTransaction->id }}</h2>

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="h5 card-title mb-0">{{ __('Butiran Transaksi') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <div class="fw-semibold text-muted">{{ __('Permohonan Pinjaman:') }}</div>
                                <a href="{{ route('loan-applications.show', $loanTransaction->loanApplication) }}">
                                    #{{ $loanTransaction->loan_application_id ?? 'N/A' }}
                                </a>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold text-muted">{{ __('Jenis Transaksi:') }}</div>
                                <span
                                    class="text-capitalize">{{ Str::title(str_replace('_', ' ', $loanTransaction->type)) }}</span>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold text-muted">{{ __('Status Transaksi:') }}</div>
                                <span
                                    class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($loanTransaction->status) }}">
                                    {{ Str::title(str_replace('_', ' ', $loanTransaction->status)) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold text-muted">{{ __('Tarikh Transaksi:') }}</div>
                                {{ $loanTransaction->transaction_date?->format('d/m/Y H:i A') ?? 'N/A' }}
                            </div>
                        </div>

                        @if ($loanTransaction->loanTransactionItems->isNotEmpty())
                            <h4 class="h6 fw-semibold mt-3 mb-2 text-muted text-uppercase small">
                                {{ __('Item Peralatan Terlibat') }}</h4>
                            <ul class="list-group list-group-flush">
                                @foreach ($loanTransaction->loanTransactionItems as $item)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                        <div>
                                            <a href="{{ route('equipment.show', $item->equipment_id) }}">
                                                {{ $item->equipment->brand ?? '' }} {{ $item->equipment->model ?? '' }}
                                                (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                                            </a>
                                            <small class="d-block text-muted">Kuantiti:
                                                {{ $item->quantity_transacted }}</small>
                                        </div>
                                        @if ($loanTransaction->isReturn())
                                            <span class="badge bg-light text-dark border rounded-pill">
                                                {{ $item->condition_on_return ? Str::title(str_replace('_', ' ', $item->condition_on_return)) : __('Tidak Dinyatakan') }}
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
                        <div class="card-header">
                            <h3 class="h5 card-title mb-0">{{ __('Butiran Pengeluaran') }}</h3>
                        </div>
                        <div class="card-body small">
                            <p class="mb-1"><span class="fw-semibold text-muted">{{ __('Pegawai Pengeluar:') }}</span>
                                {{ $loanTransaction->issuingOfficer->name ?? 'N/A' }}</p>
                            <p class="mb-1"><span
                                    class="fw-semibold text-muted">{{ __('Tarikh & Masa Pengeluaran:') }}</span>
                                {{ $loanTransaction->issue_timestamp?->format('d/m/Y H:i A') ?? 'N/A' }}</p>
                            <p class="mb-1"><span class="fw-semibold text-muted">{{ __('Aksesori Dikeluarkan:') }}</span>
                                {{ implode(', ', json_decode($loanTransaction->accessories_checklist_on_issue, true) ?? []) ?: '-' }}
                            </p>
                            <p class="mb-0"><span class="fw-semibold text-muted">{{ __('Catatan Pengeluaran:') }}</span>
                                {{ $loanTransaction->issue_notes ?: '-' }}</p>
                        </div>
                    </div>
                @endif

                @if ($loanTransaction->isReturn())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="h5 card-title mb-0">{{ __('Butiran Pulangan') }}</h3>
                        </div>
                        <div class="card-body small">
                            <p class="mb-1"><span
                                    class="fw-semibold text-muted">{{ __('Pegawai Terima Pulangan:') }}</span>
                                {{ $loanTransaction->returnAcceptingOfficer->name ?? 'N/A' }}</p>
                            <p class="mb-1"><span
                                    class="fw-semibold text-muted">{{ __('Tarikh & Masa Pulangan:') }}</span>
                                {{ $loanTransaction->return_timestamp?->format('d/m/Y H:i A') ?? 'N/A' }}</p>
                            <p class="mb-1"><span class="fw-semibold text-muted">{{ __('Aksesori Dipulangkan:') }}</span>
                                {{ implode(', ', json_decode($loanTransaction->accessories_checklist_on_return, true) ?? []) ?: '-' }}
                            </p>
                            <p class="mb-0"><span class="fw-semibold text-muted">{{ __('Catatan Pulangan:') }}</span>
                                {{ $loanTransaction->return_notes ?: '-' }}</p>

                            @if ($loanTransaction->status === \App\Models\LoanTransaction::STATUS_RETURNED_DAMAGED)
                                <div class="alert alert-warning mt-3 small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    {{ __('Status Penemuan Semasa Pulangan: Peralatan Ditemui Rosak.') }}
                                </div>
                            @elseif ($loanTransaction->status === \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST)
                                {{-- Assuming 'lost' status constant --}}
                                <div class="alert alert-danger mt-3 small" role="alert">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                    {{ __('Status Penemuan Semasa Pulangan: Peralatan Dilaporkan Hilang.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <a href="{{ route('loan-applications.show', $loanTransaction->loanApplication) }}"
                        class="btn btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        {{ __('Kembali ke Butiran Permohonan') }}
                    </a>
                    <a href="{{ route('loan-transactions.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center ms-2">
                        <i class="bi bi-list-ul me-2"></i>
                        {{ __('Senarai Transaksi') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
