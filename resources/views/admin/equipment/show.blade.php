{{-- resources/views/admin/equipment/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Peralatan') . ': #' . ($equipment->tag_id ?? $equipment->id))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">
                        {{ __('Butiran Peralatan') }}: <span class="text-primary">#{{ $equipment->tag_id ?? $equipment->id }}</span>
                    </h1>
                    <div>
                        {{-- Corrected route name if it wasn't already --}}
                        <a href="{{ route('resource-management.equipment-admin.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center me-2 motac-btn-outline">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Pentadbiran') }}
                        </a>
                        @can('update', $equipment)
                            {{-- Corrected route name if it wasn't already --}}
                            <a href="{{ route('resource-management.equipment-admin.edit', $equipment) }}"
                                class="btn btn-sm btn-primary d-inline-flex align-items-center motac-btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>{{ __('Kemaskini') }}
                            </a>
                        @endcan
                    </div>
                </div>

                @include('_partials._alerts.alert-general') {{-- For session messages, ensure path is correct --}}

                <div class="card shadow-sm mb-4 motac-card">
                    <div class="card-header bg-light py-3 motac-card-header">
                        <h2 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                           <i class="bi bi-info-circle-fill me-2 text-primary"></i> {{ __('Maklumat Am Peralatan') }}
                        </h2>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Tag Aset') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark font-monospace">{{ $equipment->tag_id ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jenis Aset') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->asset_type_label ?? ($equipment->asset_type ? __(Str::title(str_replace('_', ' ', $equipment->asset_type))) : 'N/A') }}
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jenama') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->brand ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Model') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->model ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Siri') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark font-monospace">{{ $equipment->serial_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Kod Item') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark font-monospace">{{ $equipment->item_code ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Operasi') }}:</dt>
                            <dd class="col-sm-8 col-lg-9">
                                <x-equipment-status-badge :status="$equipment->status" />
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Kondisi Fizikal') }}:</dt>
                            <dd class="col-sm-8 col-lg-9">
                                 <x-equipment-status-badge :status="$equipment->condition_status" />
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh Pembelian') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->purchase_date ? $equipment->purchase_date->translatedFormat('d M Y') : 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh Tamat Waranti') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->warranty_expiry_date ? $equipment->warranty_expiry_date->translatedFormat('d M Y') : 'N/A' }}
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jabatan Pemilik') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->department->name ?? __('Umum') }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Lokasi Semasa') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">{{ $equipment->current_location ?? 'N/A' }}</dd>

                            @if($equipment->description)
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Keterangan') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">{{ $equipment->description ?? '-' }}</dd>
                            @endif

                            @if($equipment->notes)
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nota Tambahan (Admin)') }}:</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">{{ $equipment->notes ?? '-' }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                @if ($equipment->activeLoanTransactionItem)
                    <div class="card shadow-sm mb-4 motac-card">
                        <div class="card-header bg-light py-3 motac-card-header">
                            <h2 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                                <i class="bi bi-person-check-fill me-2 text-primary"></i>{{ __('Maklumat Pinjaman Aktif') }}
                            </h2>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <dl class="row g-2 small">
                                <dt class="col-sm-4 fw-medium text-muted">{{ __('Dipinjam Oleh') }}:</dt>
                                <dd class="col-sm-8 text-dark">
                                    @if ($equipment->activeLoanTransactionItem->loanTransaction?->loanApplication?->user)
                                        <a href="{{ route('settings.users.show', $equipment->activeLoanTransactionItem->loanTransaction->loanApplication->user) }}" class="text-decoration-none">
                                            {{ $equipment->activeLoanTransactionItem->loanTransaction->loanApplication->user->name }}
                                        </a>
                                    @else
                                        {{__('N/A')}}
                                    @endif
                                </dd>
                                <dt class="col-sm-4 fw-medium text-muted">{{ __('No. Permohonan Pinjaman') }}:</dt>
                                <dd class="col-sm-8 text-dark">
                                    @if ($equipment->activeLoanTransactionItem->loanTransaction?->loanApplication)
                                        <a href="{{ route('loan-applications.show', $equipment->activeLoanTransactionItem->loanTransaction->loanApplication) }}" class="text-decoration-none">
                                            #{{ $equipment->activeLoanTransactionItem->loanTransaction->loanApplication->id }}
                                        </a>
                                    @else
                                        {{__('N/A')}}
                                    @endif
                                </dd>
                                <dt class="col-sm-4 fw-medium text-muted">{{ __('Tarikh Dijangka Pulang') }}:</dt>
                                <dd class="col-sm-8 text-dark {{ optional($equipment->activeLoanTransactionItem->loanTransaction->loanApplication)->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                    {{ $equipment->activeLoanTransactionItem->loanTransaction?->loanApplication->loan_end_date?->translatedFormat('d M Y, h:i A') ?? '-' }}
                                    @if(optional($equipment->activeLoanTransactionItem->loanTransaction->loanApplication)->isOverdue())
                                        ({{__('Lewat Tempoh')}})
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm motac-card">
                    <div class="card-header bg-light py-3 motac-card-header">
                        <h2 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                            <i class="bi bi-list-ol me-2 text-primary"></i>{{ __('Sejarah Transaksi Pinjaman Peralatan Ini') }}
                        </h2>
                    </div>
                    <div class="card-body p-0">
                        @if ($equipment->loanTransactionItems->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small px-3 py-2">{{ __('ID Transaksi') }}</th>
                                            <th class="small px-3 py-2">{{ __('Permohonan') }}</th>
                                            <th class="small px-3 py-2">{{ __('Peminjam') }}</th>
                                            <th class="small px-3 py-2">{{ __('Jenis') }}</th>
                                            <th class="small px-3 py-2">{{ __('Status Item') }}</th>
                                            <th class="small px-3 py-2">{{ __('Tarikh Transaksi') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipment->loanTransactionItems->sortByDesc('loanTransaction.transaction_date') as $transactionItem)
                                            <tr>
                                                <td class="small px-3 py-2">
                                                     <a href="{{ route('resource-management.bpm.loan-transactions.show', $transactionItem->loanTransaction->id) }}" class="text-decoration-none" title="{{__('Lihat Transaksi')}}">
                                                        #{{ $transactionItem->loanTransaction->id }}
                                                    </a>
                                                </td>
                                                <td class="small px-3 py-2">
                                                    <a href="{{ route('loan-applications.show', $transactionItem->loanTransaction->loan_application_id) }}" class="text-decoration-none" title="{{__('Lihat Permohonan')}}">
                                                        #{{ $transactionItem->loanTransaction->loan_application_id }}
                                                    </a>
                                                </td>
                                                 <td class="small px-3 py-2 text-muted">{{ optional(optional($transactionItem->loanTransaction->loanApplication)->user)->name ?? __('N/A')}}</td>
                                                <td class="small px-3 py-2">
                                                    <span class="badge rounded-pill {{ $transactionItem->loanTransaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary-emphasis' }} fw-normal">
                                                        {{ __(optional($transactionItem->loanTransaction)->type_label ?? Str::ucfirst($transactionItem->loanTransaction->type)) }}
                                                    </span>
                                                </td>
                                                <td class="small px-3 py-2">
                                                     <x-equipment-status-badge :status="$transactionItem->status" />
                                                </td>
                                                <td class="small px-3 py-2">{{ $transactionItem->loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                             <div class="p-3">
                                <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>{{ __('Tiada sejarah pinjaman untuk peralatan ini.') }}</p>
                            </div>
                        @endif
                    </div>
                     @can('delete', $equipment)
                        <div class="card-footer bg-light text-end py-3 border-top">
                            {{-- Delete form with corrected route name --}}
                            <form action="{{ route('resource-management.equipment-admin.destroy', $equipment) }}" method="POST"
                                onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam peralatan ini: :tagId? Tindakan ini tidak boleh diundur.', ['tagId' => $equipment->tag_id]) }}');"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Peralatan') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
