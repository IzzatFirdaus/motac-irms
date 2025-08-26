{{-- resources/views/loan-transactions/loan-transaction-issued-list.blade.php --}}
{{-- List of all issued loan transactions (equipment issued out) --}}

@extends('layouts.app')

@section('title', __('Senarai Transaksi Pengeluaran Pinjaman'))

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                     <h1 class="h2 fw-bold text-dark mb-0">@yield('title')</h1>
                </div>

                @include('_partials._alerts.alert-general')

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
                                                        <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction) }}"
                                                            class="btn btn-outline-primary" title="{{ __('Lihat Butiran Transaksi') }}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        @if ($transaction->loanApplication && $transaction->loanApplication->canBeReturned() && !$transaction->isFullyClosedOrReturned())
                                                            @can('createReturn', $transaction->loanApplication)
                                                                <a href="{{ route('loan-applications.return', $transaction) }}"
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
                </div>
            </div>
        </div>
    </div>
@endsection
