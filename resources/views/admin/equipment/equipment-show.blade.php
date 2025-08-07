{{-- resources/views/admin/equipment/equipment-show.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('transaction.show_title') . ' #' . $loanTransaction->id)
=======
@section('title', __('Butiran Peralatan') . ': #' . ($equipment->tag_id ?? $equipment->id))
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
<<<<<<< HEAD
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('transaction.show_title') }} #{{ $loanTransaction->id }}
                    </h1>
                    <a href="{{ route('resource-management.bpm.loan-transactions.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-list-ul me-1"></i> {{ __('transaction.back_to_list') }}
                    </a>
                </div>

                @include('_partials._alerts.alert-general')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('transaction.basic_info') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 text-muted">{{ __('transaction.related_loan_app_id') }}</dt>
                            <dd class="col-sm-8"><a
                                    href="{{ route('loan-applications.show', $loanTransaction->loan_application_id) }}"
                                    class="text-decoration-none fw-medium">#{{ $loanTransaction->loan_application_id }}</a>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_type') }}</dt>
                            <dd class="col-sm-8">
                                {{-- Using the new accessor for a high-contrast badge --}}
                                <span
                                    class="badge rounded-pill {{ $loanTransaction->type_color_class }}">{{ $loanTransaction->type_label }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_status') }}</dt>
                            <dd class="col-sm-8"><span
                                    class="badge rounded-pill {{ $loanTransaction->status_color_class }}">{{ $loanTransaction->status_label }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_datetime') }}</dt>
                            <dd class="col-sm-8">
                                {{ $loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}</dd>
                        </dl>

                        <h3 class="h6 fw-semibold mt-4 mb-2 pt-2 border-top">{{ __('transaction.involved_items') }}</h3>
                        <ul class="list-group list-group-flush">
                            @forelse ($loanTransaction->loanTransactionItems as $item)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-start py-2">
                                    <div>
                                        <a href="{{ route('resource-management.equipment-admin.show', $item->equipment_id) }}"
                                            class="text-decoration-none fw-medium">{{ $item->equipment->name ?? __('Item Tidak Dikenali') }}</a>
                                        <small class="d-block text-muted">Tag:
                                            {{ $item->equipment->tag_id ?? 'N/A' }}</small>
                                    </div>
                                    <span class="text-muted small">{{ __('transaction.quantity') }}:
                                        {{ $item->quantity_transacted }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Tiada item dalam transaksi ini.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @if ($loanTransaction->isIssue())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('transaction.issue_details') }}</h2>
                        </div>
                        <div class="card-body p-4 small">
                            <dl class="row g-3">
                                <dt class="col-sm-4 text-muted">{{ __('transaction.issuing_officer') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->issuingOfficer?->name ?? __('common.not_available') }}</dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.receiver') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->receivingOfficer?->name ?? __('common.not_available') }}</dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.actual_issue_datetime') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->issue_timestamp?->translatedFormat('d M Y, h:i A') ?? __('common.not_available') }}
                                </dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.accessories_issued') }}:</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->accessories_checklist_on_issue ? implode(', ', $loanTransaction->accessories_checklist_on_issue) : '-' }}
                                </dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.issue_notes') }}</dt>
                                <dd class="col-sm-8" style="white-space: pre-wrap;">
                                    {{ $loanTransaction->issue_notes ?? '-' }}</dd>
=======

                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">
                        {{ __('Butiran Peralatan') }}: <span
                            class="text-primary">#{{ $equipment->tag_id ?? $equipment->id }}</span>
                    </h1>
                    <div>
                        <a href="{{ route('resource-management.equipment-admin.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center me-2">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                        </a>
                        @can('update', $equipment)
                            <a href="{{ route('resource-management.equipment-admin.edit', $equipment) }}"
                                class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i>{{ __('Kemaskini') }}
                            </a>
                        @endcan
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Maklumat Am Peralatan') }}</h2>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Tag Aset') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->tag_id ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jenis Aset') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->asset_type_translated ?? ($equipment->asset_type ? __(Str::title(str_replace('_', ' ', $equipment->asset_type))) : 'N/A') }}
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jenama') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->brand ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Model') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->model ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Siri') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->serial_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Operasi') }}</dt>
                            <dd class="col-sm-8 col-lg-9">
                                <span
                                    class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->status ?? '') }}">
                                    {{ $equipment->status_translated ?? ($equipment->status ? __(Str::title(str_replace('_', ' ', $equipment->status))) : 'N/A') }}
                                </span>
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Kondisi Fizikal') }}</dt>
                            <dd class="col-sm-8 col-lg-9">
                                <span
                                    class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->condition_status ?? '') }}">
                                    {{ $equipment->condition_status_translated ?? ($equipment->condition_status ? __(Str::title(str_replace('_', ' ', $equipment->condition_status))) : 'N/A') }}
                                </span>
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh Pembelian') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->purchase_date ? $equipment->purchase_date->format('d M Y') : 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh Tamat Waranti') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $equipment->warranty_expiry_date ? $equipment->warranty_expiry_date->format('d M Y') : 'N/A' }}
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jabatan Pemilik') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->department->name ?? 'N/A' }}</dd>

                            {{-- <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Pusat (Jika berkaitan)') }}</dt>
                        <dd class="col-sm-8 col-lg-9 text-dark">{{ $equipment->center->name ?? 'N/A' }}</dd> --}}

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Keterangan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                {{ $equipment->description ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Lokasi Semasa') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                {{ $equipment->current_location ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nota Tambahan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                {{ $equipment->notes ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Active Loan Information (example structure) --}}
                @if ($equipment->activeLoanTransaction)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title fw-semibold mb-0">{{ __('Maklumat Pinjaman Aktif (Jika Ada)') }}</h2>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <dl class="row g-2 small">
                                <dt class="col-sm-4 fw-medium text-muted">{{ __('Dipinjam Oleh') }}</dt>
                                <dd class="col-sm-8 text-dark">
                                    @if ($equipment->activeLoanTransaction->loanApplication?->user)
                                        {{-- Use admin user view route: settings.users.show --}}
                                        <a href="{{ route('settings.users.show', $equipment->activeLoanTransaction->loanApplication->user) }}"
                                            class="text-decoration-none">
                                            {{ $equipment->activeLoanTransaction->loanApplication->user->name }}
                                        </a>
                                    @else
                                        'N/A'
                                    @endif
                                </dd>
                                <dt class="col-sm-4 fw-medium text-muted">{{ __('No. Permohonan Pinjaman') }}</dt>
                                <dd class="col-sm-8 text-dark">
                                    @if ($equipment->activeLoanTransaction->loanApplication)
                                        {{-- Use general loan application view route: loan-applications.show --}}
                                        <a href="{{ route('loan-applications.show', $equipment->activeLoanTransaction->loanApplication) }}"
                                            class="text-decoration-none">
                                            #{{ $equipment->activeLoanTransaction->loanApplication->id }}
                                        </a>
                                    @else
                                        'N/A'
                                    @endif
                                </dd>
                                {{-- Add other relevant active loan details --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                            </dl>
                        </div>
                    </div>
                @endif

<<<<<<< HEAD
                <div class="text-center mt-4">
                    <a href="{{ route('loan-applications.show', $loanTransaction->loan_application_id) }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle me-1"></i> {{ __('transaction.back_to_application') }}
                    </a>
=======
                {{-- Loan History --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Sejarah Pinjaman') }}</h2>
                    </div>
                    <div class="card-body p-0">
                        @if ($equipment->loanTransactions()->exists())
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small">Tarikh Keluar</th>
                                            <th class="small">Tarikh Pulang</th>
                                            <th class="small">Pemohon</th>
                                            {{-- Add more columns as needed --}}
                                            <th class="small">Status Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipment->loanTransactions()->orderByDesc('transaction_date')->get() as $transaction)
                                            <tr>
                                                <td class="small">
                                                    {{ $transaction->issue_timestamp ? $transaction->issue_timestamp->format('d M Y, H:i') : '-' }}
                                                </td>
                                                <td class="small">
                                                    {{ $transaction->return_timestamp ? $transaction->return_timestamp->format('d M Y, H:i') : '-' }}
                                                </td>
                                                <td class="small">
                                                    @if ($transaction->loanApplication?->user)
                                                        <a href="{{ route('settings.users.show', $transaction->loanApplication->user) }}"
                                                            class="text-decoration-none">
                                                            {{ $transaction->loanApplication->user->name }}
                                                        </a>
                                                    @else
                                                        'N/A'
                                                    @endif
                                                </td>
                                                <td class="small">
                                                    <span
                                                        class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transaction->status ?? '') }}">
                                                        {{ __(Str::title(str_replace('_', ' ', $transaction->status))) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-3">
                                <p class="small text-muted mb-0">{{ __('Tiada sejarah pinjaman untuk peralatan ini.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2 justify-content-end">
                    @can('update', $equipment)
                        <a href="{{ route('resource-management.equipment-admin.edit', $equipment) }}"
                            class="btn btn-primary d-inline-flex align-items-center">
                            <i class="bi bi-pencil-square me-1"></i>{{ __('Kemaskini Peralatan') }}
                        </a>
                    @endcan
                    @can('delete', $equipment)
                        {{-- This form will not work without a corresponding DELETE route and controller action --}}
                        {{-- For Livewire, this would be a button calling a wire:click method to open a delete confirmation modal --}}
                        <form action="{{ route('resource-management.equipment-admin.index') }}/{{ $equipment->id }}"
                            {{-- Placeholder - NO DESTROY ROUTE DEFINED IN WEB.PHP for equipment-admin --}} method="POST"
                            onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam peralatan ini: :tagId? Tindakan ini tidak boleh diundur.', ['tagId' => $equipment->tag_id]) }}');"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger d-inline-flex align-items-center">
                                <i class="bi bi-trash3-fill me-1"></i>{{ __('Padam Peralatan') }}
                            </button>
                        </form>
                    @endcan
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
@endsection
=======
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
