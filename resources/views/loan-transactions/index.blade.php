{{-- resources/views/loan-transactions/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Transaksi Pinjaman ICT'))

@section('content')
    <div class="container py-4">

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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanTransactions as $transaction)
                                        <tr>
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
            <div class="alert alert-danger" role="alert">
                {{ __('Error: Loan transactions data not passed to the view.') }}
            </div>
        @endisset

    </div>
@endsection
