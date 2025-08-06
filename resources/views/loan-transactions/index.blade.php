{{-- resources/views/loan-transactions/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Semua Transaksi Pinjaman ICT'))

@section('content')
    <div class="container py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Semua Transaksi Pinjaman ICT') }}</h1>
        </div>

        {{-- General alert for system messages --}}
        @include('_partials._alerts.alert-general') {{-- CORRECTED INCLUDE PATH --}}

        @isset($loanTransactions)
            {{-- If there are no transactions, show info alert --}}
            @if ($loanTransactions->isEmpty())
                <div class="alert alert-info text-center shadow-sm rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                    <span class="align-middle">{{ __('Tiada sebarang transaksi pinjaman ditemui dalam sistem.') }}</span>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Transaksi') }}</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Transaksi') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Transaksi') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Transaksi') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">{{ __('Tindakan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanTransactions as $transaction)
                                        <tr>
                                            <td class="px-3 py-2 small text-dark fw-medium">#{{ $transaction->id }}</td>
                                            <td class="px-3 py-2 small text-dark">
                                                <span class="badge rounded-pill {{ $transaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}">
                                                    {{ e($transaction->type_label) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 small">
                                                @if ($transaction->loanApplication)
                                                    <a href="{{ route('loan-applications.show', $transaction->loanApplication->id) }}" class="text-decoration-none fw-medium" title="{{__('Lihat Permohonan')}} #{{ $transaction->loan_application_id }}">
                                                        #{{ $transaction->loan_application_id }}
                                                    </a>
                                                @else
                                                    {{ $transaction->loan_application_id ?? __('N/A') }}
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 small text-muted">{{ optional($transaction->transaction_date)->translatedFormat('d M Y, H:i A') ?? optional($transaction->created_at)->translatedFormat('d M Y, H:i A') }}</td>
                                            <td class="px-3 py-2 small">
                                                <x-loan-transaction-status-badge :status="$transaction->status" />
                                            </td>
                                            <td class="px-3 py-2 text-end">
                                                {{-- CORRECTED ROUTE NAME --}}
                                                <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction) }}"
                                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                    <i class="bi bi-eye-fill me-1"></i> {{ __('Lihat') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- Pagination: Only show if $loanTransactions is paginated --}}
                @if(method_exists($loanTransactions, 'links') && $loanTransactions->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{-- Use the default pagination view or specify a custom view if needed --}}
                        {!! $loanTransactions->links() !!}
                    </div>
                @endif
            @endif
        @else
            <div class="alert alert-danger text-center shadow-sm rounded-3" role="alert">
                 <i class="bi bi-exclamation-triangle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Ralat: Data transaksi pinjaman tidak dihantar ke paparan ini.') }}</span>
            </div>
        @endisset
    </div>
@endsection
