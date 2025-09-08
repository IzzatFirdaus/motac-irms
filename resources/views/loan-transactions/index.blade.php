{{-- resources/views/loan-transactions/index.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('Senarai Semua Transaksi Pinjaman ICT'))
=======
@section('title', __('Senarai Transaksi Pinjaman ICT'))
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
    <div class="container py-4">

<<<<<<< HEAD
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Semua Transaksi Pinjaman ICT') }}</h1>
        </div>

        @include('_partials._alerts.alert-general') {{-- CORRECTED INCLUDE PATH --}}

        @isset($loanTransactions)
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
=======
        <h1 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Transaksi Pinjaman ICT') }}</h1>

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

        @isset($loanTransactions)
            @if ($loanTransactions->isEmpty())
                <div class="alert alert-info" role="alert">
                    {{ __('Tiada transaksi pinjaman ditemui.') }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium">
                                            {{ __('ID Transaksi') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Jenis') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium">
                                            {{ __('Permohonan ID') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Tarikh') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium text-end">
                                            {{ __('Tindakan') }}</th>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanTransactions as $transaction)
                                        <tr>
<<<<<<< HEAD
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
=======
                                            <td class="align-middle">{{ $transaction->id }}</td>
                                            <td class="align-middle text-capitalize">
                                                {{ str_replace('_', ' ', $transaction->type) }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($transaction->loanApplication)
                                                    <a
                                                        href="{{ route('loan-applications.show', $transaction->loanApplication->id) }}">{{ $transaction->loan_application_id }}</a>
                                                @else
                                                    {{ $transaction->loan_application_id ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end align-middle">
                                                <a href="{{ route('loan-transactions.show', $transaction) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> {{ __('Lihat') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($loanTransactions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $loanTransactions->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $loanTransactions->links() }}
                    </div>
                @endif
            @endif
        @else
<<<<<<< HEAD
            <div class="alert alert-danger text-center shadow-sm rounded-3" role="alert">
                 <i class="bi bi-exclamation-triangle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Ralat: Data transaksi pinjaman tidak dihantar ke paparan ini.') }}</span>
            </div>
        @endisset
=======
            <div class="alert alert-danger" role="alert">
                {{ __('Error: Loan transactions data not passed to the view.') }}
            </div>
        @endisset

>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
    </div>
@endsection
