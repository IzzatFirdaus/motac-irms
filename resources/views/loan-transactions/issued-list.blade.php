{{-- resources/views/loan-transactions/issued-list.blade.php --}}
<<<<<<< HEAD
@extends('layouts.app')

@section('title', __('Senarai Transaksi Pengeluaran Pinjaman'))

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                     <h1 class="h2 fw-bold text-dark mb-0">@yield('title')</h1>
                </div>

                @include('_partials._alerts.alert-general') {{-- CORRECTED INCLUDE PATH --}}

                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Transaksi Pengeluaran') }}</h2>
                    </div>
                    <div class="card-body p-0">
                        @if ($issuedTransactions->isEmpty())
                            <div class="alert alert-info text-center m-4" role="alert">
                                <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                                <span class="align-middle">{{ __('Tiada rekod transaksi pengeluaran ditemui.') }}</span>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Transaksi') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peminjam') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pegawai Bertanggungjawab') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Keluar Sebenar') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Transaksi') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Bil. Item') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Tindakan') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($issuedTransactions as $transaction)
                                            <tr>
                                                <td class="px-3 py-2 small text-dark fw-medium">#{{ $transaction->id }}</td>
                                                <td class="px-3 py-2 small">
                                                    {{-- This route 'loan-applications.show' is global and correct --}}
                                                    <a href="{{ route('loan-applications.show', $transaction->loanApplication) }}" class="text-decoration-none fw-medium" title="{{__('Lihat Permohonan')}}">
                                                        #{{ $transaction->loan_application_id }}
                                                    </a>
                                                </td>
                                                <td class="px-3 py-2 small text-muted">{{ e(optional(optional($transaction->loanApplication)->user)->name ?? __('N/A')) }}</td>
                                                <td class="px-3 py-2 small text-muted">{{ e(optional(optional($transaction->loanApplication)->responsibleOfficer)->name ?? optional(optional($transaction->loanApplication)->user)->name ?? __('N/A')) }}</td>
                                                <td class="px-3 py-2 small text-muted">{{ optional($transaction->issue_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</td>
                                                <td class="px-3 py-2 small">
                                                    <x-loan-transaction-status-badge :status="$transaction->status" />
                                                </td>
                                                <td class="px-3 py-2 small text-muted text-center">{{ $transaction->loanTransactionItems->count() }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="{{__('Tindakan untuk Transaksi #')}}{{$transaction->id}}">
                                                        {{-- CORRECTED ROUTE NAME --}}
                                                        <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction) }}"
                                                            class="btn btn-outline-primary" title="{{ __('Lihat Butiran Transaksi') }}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        @if ($transaction->loanApplication && $transaction->loanApplication->canBeReturned() && !$transaction->isFullyClosedOrReturned())
                                                            @can('createReturn', $transaction->loanApplication)
                                                                {{-- CORRECTED ROUTE NAME --}}
                                                                <a href="{{ route('resource-management.bpm.loan-transactions.return.form', $transaction) }}"
                                                                    class="btn btn-success" title="{{ __('Proses Pulangan') }}">
                                                                    <i class="bi bi-arrow-return-left"></i>
                                                                </a>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($issuedTransactions->hasPages())
                                <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                                    {{ $issuedTransactions->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
=======
@extends('layouts.app') {{-- Or your main layout file --}}

@section('title', 'Senarai Pinjaman Telah Dikeluarkan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('title')</h3>
                    {{-- Add any filter or search forms here if needed --}}
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($issuedTransactions->isEmpty())
                        <div class="alert alert-info">
                            Tiada rekod pinjaman yang telah dikeluarkan ditemui.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>ID Permohonan</th>
                                        <th>Peminjam</th>
                                        <th>Pegawai Bertanggungjawab</th>
                                        <th>Tarikh Keluar</th>
                                        <th>Tarikh Jangka Pulang</th>
                                        <th>Status Transaksi</th>
                                        <th>Bil. Item</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($issuedTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $transaction->loanApplication) }}">
                                                    {{ $transaction->loan_application_id }}
                                                </a>
                                            </td>
                                            <td>{{ $transaction->loanApplication->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $transaction->loanApplication->responsibleOfficer->full_name ?? ($transaction->loanApplication->user->full_name ?? 'N/A') }}</td>
                                            <td>{{ $transaction->issue_timestamp ? $transaction->issue_timestamp->format('d/m/Y H:i A') : 'N/A' }}</td>
                                            <td>{{ $transaction->due_date ? \Carbon\Carbon::parse($transaction->due_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->status === \App\Models\LoanTransaction::STATUS_ISSUED ? 'success' : 'secondary' }}">
                                                    {{ Str::title(str_replace('_', ' ', $transaction->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->loanTransactionItems->count() }}</td>
                                            <td>
                                                <a href="{{ route('loan-transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                                @can('createReturn', $transaction) {{-- Assuming LoanTransactionPolicy has createReturn --}}
                                                    @if(!$transaction->isFullyClosedOrReturned())
                                                        <a href="{{ route('loan-transactions.return.form', $transaction) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-undo"></i> Proses Pulangan
                                                        </a>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $issuedTransactions->links() }}
                        </div>
                    @endif
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
@endsection
=======
</div>
@endsection

@push('scripts')
{{-- Add any page-specific JavaScript here --}}
@endpush
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
