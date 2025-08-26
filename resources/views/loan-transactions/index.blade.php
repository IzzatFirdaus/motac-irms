{{-- resources/views/loan-transactions/index.blade.php --}}
{{-- List all ICT loan transactions --}}

@extends('layouts.app')

@section('title', __('Senarai Transaksi Pinjaman ICT'))

@section('content')
    <div class="container py-4">
        <h1 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Transaksi Pinjaman ICT') }}</h1>

        @include('_partials._alerts.alert-general')

        @if ($transactions->isEmpty())
            <div class="alert alert-info text-center shadow-sm rounded-3" role="alert">
                <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Tiada transaksi pinjaman ditemui.') }}</span>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-uppercase text-muted fw-medium">{{ __('ID Transaksi') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium">{{ __('Jenis') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium">{{ __('Permohonan ID') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium">{{ __('Tarikh') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium text-end">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="align-middle">#{{ $transaction->id }}</td>
                                        <td class="align-middle">
                                            <span class="badge rounded-pill {{ $transaction->type_color_class ?? '' }}">
                                                {{ $transaction->type_label ?? Str::title(str_replace('_', ' ', $transaction->type)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($transaction->loanApplication)
                                                <a href="{{ route('loan-applications.show', $transaction->loanApplication->id) }}">#{{ $transaction->loan_application_id }}</a>
                                            @else
                                                {{ $transaction->loan_application_id ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $transaction->transaction_date?->translatedFormat('d M Y, H:i A') ?? $transaction->created_at?->translatedFormat('d M Y, H:i A') }}</td>
                                        <td class="text-end align-middle">
                                            <a href="{{ route('loan-transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> {{ __('Lihat') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($transactions->hasPages())
                        <div class="mt-3 d-flex justify-content-center">{{ $transactions->links() }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
