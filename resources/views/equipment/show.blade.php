@extends('layouts.app')

@section('title', 'Butiran Peralatan ICT #' . $equipment->id)

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 dark:text-gray-100">
            Butiran Peralatan ICT #{{ $equipment->id }}
            <span class="text-muted fs-5">(Tag: {{ $equipment->tag_id ?? 'N/A' }})</span>
        </h1>
        <div>
            @can('update', $equipment)
                <a href="{{ route('resource-management.admin.equipment-admin.edit', $equipment) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="ti ti-pencil me-1"></i> Edit
                </a>
            @endcan
            <x-back-button :route="route('equipment.index')" text="Kembali ke Senarai" />
        </div>
    </div>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <x-card title="Maklumat Asas Peralatan">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Jenis Aset') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->asset_type_label }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Jenama') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->brand ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Model') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->model ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Tag ID MOTAC') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->tag_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Nombor Siri') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->serial_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Tarikh Pembelian') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->purchase_date?->translatedFormat('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Tarikh Tamat Waranti') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->warranty_expiry_date?->translatedFormat('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Status Operasi') }}</label>
                        <p>
                            <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($equipment->status, 'bootstrap_badge') }} px-2 py-1">
                                {{ $equipment->status_label }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Status Keadaan Fizikal') }}</label>
                        <p>
                             <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($equipment->condition_status, 'bootstrap_badge_condition') }} px-2 py-1">
                                {{ $equipment->condition_status_label }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Lokasi Semasa') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->current_location ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small text-muted">{{ __('Catatan') }}</label>
                        <p class="form-control-plaintext" style="white-space: pre-wrap;">{{ $equipment->notes ?? '-' }}</p>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-lg-5">
             <x-card title="Maklumat Tambahan">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Dicipta Oleh') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->creator->name ?? 'N/A' }} pada {{ $equipment->created_at?->translatedFormat('d M Y, h:i A') ?? 'N/A' }}</p>
                    </div>
                     <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Dikemaskini Terakhir Oleh') }}</label>
                        <p class="form-control-plaintext">{{ $equipment->updater->name ?? 'N/A' }} pada {{ $equipment->updated_at?->translatedFormat('d M Y, h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>


    @if ($equipment->loanTransactionItems->isNotEmpty())
        <x-card title="Sejarah Pinjaman (Item Transaksi Berkaitan Peralatan Ini)" class="mt-4">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th class="small">ID Transaksi</th>
                            <th class="small">Permohonan #</th>
                            <th class="small">Jenis Transaksi</th>
                            <th class="small">Status Item</th>
                            <th class="small">Tarikh Transaksi</th>
                            <th class="small">Catatan Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment->loanTransactionItems->sortByDesc('loanTransaction.transaction_date') as $transactionItem)
                            <tr>
                                <td class="small">
                                     <a href="{{ route('resource-management.admin.loan-transactions.show', $transactionItem->loanTransaction->id) }}" class="text-primary hover:underline" title="Lihat Transaksi">
                                        #{{ $transactionItem->loanTransaction->id }}
                                    </a>
                                </td>
                                <td class="small">
                                    <a href="{{ route('resource-management.my-applications.loan-applications.show', $transactionItem->loanTransaction->loan_application_id) }}" class="text-primary hover:underline" title="Lihat Permohonan">
                                        #{{ $transactionItem->loanTransaction->loan_application_id }}
                                    </a>
                                </td>
                                <td class="small">
                                    <span class="badge bg-{{ $transactionItem->loanTransaction->type === 'issue' ? 'info' : 'purple' }} text-white px-2 py-1">
                                        {{ Str::ucfirst($transactionItem->loanTransaction->type) }}
                                    </span>
                                </td>
                                <td class="small">
                                     <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($transactionItem->status, 'bootstrap_badge') }} px-2 py-1">
                                        {{ $transactionItem->status_translated ?? Str::title(str_replace('_', ' ', $transactionItem->status)) }}
                                    </span>
                                </td>
                                <td class="small">{{ $transactionItem->loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}</td>
                                <td class="small">{{ $transactionItem->item_notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @else
         <x-alert type="info" message="Tiada sejarah pinjaman untuk peralatan ini." class="mt-4"/>
    @endif
</div>
@endsection

@push('styles')
{{-- Add specific styles for this page if needed, for example, to ensure form-control-plaintext is styled if not in global css --}}
<style>
    .form-control-plaintext {
        padding-top: .375rem;
        padding-bottom: .375rem;
        margin-bottom: 0;
        line-height: 1.5;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
        color: #212529; /* Adjust for dark mode if necessary */
    }
    .table-sm th, .table-sm td {
        padding: 0.4rem;
    }
</style>
@endpush
