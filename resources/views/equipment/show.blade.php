<<<<<<< HEAD
{{-- resources/views/equipment/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Peralatan ICT #') . $equipment->id)

@section('content')
    <div class="container py-4"> {{-- Changed to container for consistent width, use container-fluid if full width needed --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
            <h1 class="fs-2 fw-bold text-dark mb-2 mb-sm-0">
                {{ __('Butiran Peralatan ICT #') }}{{ $equipment->id }}
                <span class="text-muted fs-5">({{ __('Tag') }}: {{ e($equipment->tag_id ?? __('N/A')) }})</span>
            </h1>
            <div>
                @can('update', $equipment)
                    {{-- Policy check from Admin context --}}
                    <a href="{{ route('resource-management.equipment-admin.edit', $equipment) }}"
                        class="btn btn-outline-primary btn-sm me-2 d-inline-flex align-items-center">
                        <i class="bi bi-pencil-fill me-1"></i> {{ __('Edit Peralatan (Pentadbir)') }}
                    </a>
                @endcan
                <x-back-button :route="route('equipment.index')" :text="__('Kembali ke Senarai Awam')" classProperty="btn-sm btn-outline-secondary" />
            </div>
        </div>

        @include('equipment.partials.session-messages')

        <div class="row g-4">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Maklumat Asas Peralatan') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Jenis Aset') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">
                                {{ e($equipment->asset_type_label ?? ($equipment->asset_type ?? __('N/A'))) }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Jenama') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">{{ e($equipment->brand ?? __('N/A')) }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Model') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">{{ e($equipment->model ?? __('N/A')) }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Tag ID MOTAC') }}</dt>
                            <dd class="col-sm-7 col-md-8 small font-monospace">{{ e($equipment->tag_id ?? __('N/A')) }}
                            </dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Nombor Siri') }}</dt>
                            <dd class="col-sm-7 col-md-8 small font-monospace">
                                {{ e($equipment->serial_number ?? __('N/A')) }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Kod Item') }}</dt>
                            <dd class="col-sm-7 col-md-8 small font-monospace">{{ e($equipment->item_code ?? __('N/A')) }}
                            </dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Tarikh Pembelian') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">
                                {{ $equipment->purchase_date?->translatedFormat('d M Y') ?? __('N/A') }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Tarikh Tamat Waranti') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">
                                {{ $equipment->warranty_expiry_date?->translatedFormat('d M Y') ?? __('N/A') }}</dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Status Operasi') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">
                                <x-equipment-status-badge :status="$equipment->status" :type="'operasi'" />
                            </dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Status Keadaan Fizikal') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">
                                <x-equipment-status-badge :status="$equipment->condition_status" :type="'keadaan'" />
                            </dd>

                            <dt class="col-sm-5 col-md-4 text-muted small">{{ __('Lokasi Semasa') }}</dt>
                            <dd class="col-sm-7 col-md-8 small">{{ e($equipment->current_location ?? __('N/A')) }}</dd>

                            @if ($equipment->notes)
                                <dt class="col-sm-5 col-md-4 text-muted small mt-2">{{ __('Catatan') }}</dt>
                                <dd class="col-sm-7 col-md-8 small mt-2" style="white-space: pre-wrap;">
                                    {{ e($equipment->notes) }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Maklumat Tambahan') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted small">{{ __('Dicipta Oleh') }}</dt>
                            <dd class="col-sm-7 small">{{ optional($equipment->creator)->name ?? __('Sistem') }}</dd>

                            <dt class="col-sm-5 text-muted small">{{ __('Dicipta Pada') }}</dt>
                            <dd class="col-sm-7 small">
                                {{ $equipment->created_at?->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>

                            <dt class="col-sm-5 text-muted small mt-2">{{ __('Dikemaskini Terakhir Oleh') }}</dt>
                            <dd class="col-sm-7 small mt-2">{{ optional($equipment->updater)->name ?? __('Sistem') }}</dd>

                            <dt class="col-sm-5 text-muted small">{{ __('Dikemaskini Pada') }}</dt>
                            <dd class="col-sm-7 small">
                                {{ $equipment->updated_at?->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        @if ($equipment->loanTransactionItems->isNotEmpty())
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title fw-semibold mb-0">{{ __('Sejarah Pinjaman Peralatan Ini') }}</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                        {{ __('ID Transaksi') }}</th>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                        {{ __('Permohonan #') }}</th>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Peminjam') }}
                                    </th>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                        {{ __('Jenis Transaksi') }}</th>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                        {{ __('Status Item Dalam Transaksi Ini') }}</th>
                                    <th class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                        {{ __('Tarikh Transaksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipment->loanTransactionItems->sortByDesc('loanTransaction.transaction_date') as $transactionItem)
                                    <tr>
                                        <td class="py-2 px-3 small">
                                            {{-- Ensure this route exists and is appropriate for public view or admin view --}}
                                            <a href="{{ route('resource-management.bpm.loan-transactions.show', $transactionItem->loanTransaction->id) }}"
                                                class="text-decoration-none" title="{{ __('Lihat Transaksi') }}">
                                                #{{ $transactionItem->loanTransaction->id }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-3 small">
                                            {{-- Ensure this route exists for viewing the loan application --}}
                                            <a href="{{ route('loan-applications.show', $transactionItem->loanTransaction->loan_application_id) }}"
                                                class="text-decoration-none" title="{{ __('Lihat Permohonan') }}">
                                                #{{ $transactionItem->loanTransaction->loan_application_id }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-3 small text-muted">
                                            {{ optional(optional($transactionItem->loanTransaction->loanApplication)->user)->name ?? __('N/A') }}
                                        </td>
                                        <td class="py-2 px-3 small">
                                            <span
                                                class="badge rounded-pill {{ $transactionItem->loanTransaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info text-dark' : 'bg-primary' }}">
                                                {{ __(Str::ucfirst($transactionItem->loanTransaction->type_label)) }}
                                                {{-- Assuming type_label accessor --}}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 small">
                                            <x-equipment-status-badge :status="$transactionItem->status" :type="'transaksi_item'" />
                                        </td>
                                        <td class="py-2 px-3 small">
                                            {{ $transactionItem->loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> {{-- This is the closing div for the card, not an x-card component --}}
        @else
            <div class="alert alert-info mt-4 text-center">
                <i class="bi bi-info-circle-fill me-2"></i>
                {{ __('Tiada sejarah pinjaman direkodkan untuk peralatan ini.') }}
            </div>
        @endif
    </div>
@endsection
=======
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
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
