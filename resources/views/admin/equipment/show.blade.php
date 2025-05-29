@extends('layouts.app')

@section('title', __('Butiran Peralatan') . ': #' . ($equipment->tag_id ?? $equipment->id))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

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
                            </dl>
                        </div>
                    </div>
                @endif

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
                </div>
            </div>
        </div>
    </div>
