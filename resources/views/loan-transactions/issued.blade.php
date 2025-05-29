{{-- resources/views/loan-transactions/issued.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Peralatan Sedang Dipinjam'))

@section('content')
    <div class="container py-4">
        <h2 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Peralatan Sedang Dipinjam') }}</h2>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($issuedTransactions->isEmpty())
            <div class="alert alert-info" role="alert">
                {{ __('Tiada peralatan sedang dipinjam pada masa ini.') }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Peralatan (Tag ID)') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Dipinjam Oleh') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Tarikh Dikeluarkan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Tarikh Dijangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Status Transaksi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium text-end">
                                        {{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($issuedTransactions as $transaction)
                                    <tr>
                                        <td class="align-middle">
                                            {{ $transaction->equipment->brand ?? '' }}
                                            {{ $transaction->equipment->model ?? '' }}
                                            (Tag: <a
                                                href="{{ route('equipment.show', $transaction->equipment->id) }}">{{ $transaction->equipment->tag_id ?? 'N/A' }}</a>)
                                        </td>
                                        <td class="align-middle">
                                            @if ($transaction->loanApplication && $transaction->loanApplication->user)
                                                <a
                                                    href="{{ route('users.show', $transaction->loanApplication->user->id) }}">
                                                    {{ $transaction->loanApplication->user->name ?? 'N/A' }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            {{ $transaction->issue_timestamp?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td class="align-middle">
                                            {{ $transaction->loanApplication->loan_end_date?->format('d/m/Y') ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle">
                                            <span
                                                class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transaction->status) }}">
                                                {{ Str::title(str_replace('_', ' ', $transaction->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end align-middle">
                                            <a href="{{ route('loan-transactions.return.form', $transaction) }}"
                                                class="btn btn-sm btn-success d-inline-flex align-items-center">
                                                <i class="bi bi-arrow-return-left me-1"></i>
                                                {{ __('Rekod Pulangan') }}
                                            </a>
                                            <a href="{{ route('loan-transactions.show', $transaction) }}"
                                                class="btn btn-sm btn-outline-secondary ms-1">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($issuedTransactions->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $issuedTransactions->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
