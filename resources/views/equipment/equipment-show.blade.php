{{-- resources/views/equipment/equipment-show.blade.php --}}
{{-- Equipment details view. Renamed from show.blade.php for clarity and consistency. --}}
@extends('layouts.app')

@section('title', __('Butiran Peralatan ICT #') . $equipment->id)

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
            <h1 class="fs-2 fw-bold text-dark mb-2 mb-sm-0">
                {{ __('Butiran Peralatan ICT #') }}{{ $equipment->id }}
                <span class="text-muted fs-5">({{ __('Tag') }}: {{ e($equipment->tag_id ?? __('N/A')) }})</span>
            </h1>
            <div>
                @can('update', $equipment)
                    <a href="{{ route('resource-management.equipment-admin.edit', $equipment) }}"
                        class="btn btn-outline-primary btn-sm me-2 d-inline-flex align-items-center">
                        <i class="bi bi-pencil-fill me-1"></i> {{ __('Edit Peralatan (Pentadbir)') }}
                    </a>
                @endcan
                <x-back-button :route="route('equipment.index')" :text="__('Kembali ke Senarai Awam')" classProperty="btn-sm btn-outline-secondary" />
            </div>
        </div>

        {{-- Show session messages --}}
        @include('equipment.partials.equipment-session-messages')

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

        {{-- Equipment loan transaction history --}}
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
                                            <a href="{{ route('resource-management.bpm.loan-transactions.show', $transactionItem->loanTransaction->id) }}"
                                                class="text-decoration-none" title="{{ __('Lihat Transaksi') }}">
                                                #{{ $transactionItem->loanTransaction->id }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-3 small">
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
            </div>
        @else
            <div class="alert alert-info mt-4 text-center">
                <i class="bi bi-info-circle-fill me-2"></i>
                {{ __('Tiada sejarah pinjaman direkodkan untuk peralatan ini.') }}
            </div>
        @endif
    </div>
@endsection
