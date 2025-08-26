{{-- resources/views/loan-transactions/loan-transaction-issued.blade.php --}}
{{-- List of equipment currently on loan (issued out) --}}

@extends('layouts.app')

@section('title', __('Senarai Peralatan Sedang Dipinjam'))

@section('content')
    <div class="container py-4">
        <h2 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Peralatan Sedang Dipinjam') }}</h2>

        @include('_partials._alerts.alert-general')

        @if ($issuedTransactions->isEmpty())
            <div class="alert alert-info text-center shadow-sm rounded-3" role="alert">
                <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Tiada peralatan sedang dipinjam pada masa ini.') }}</span>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title fw-semibold mb-0">{{ __('Peralatan Aktif Dipinjam') }}</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peralatan (Tag ID)') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Dikeluarkan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Dijangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Transaksi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($issuedTransactions as $transaction)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium">
                                            @if($transaction->equipment)
                                                <a href="{{ route('equipment.show', $transaction->equipment->id) }}" title="{{__('Lihat Peralatan')}}">
                                                    {{ e(($transaction->equipment->brand ?? '').' '.($transaction->equipment->model ?? '')) }}
                                                    (Tag: {{ e($transaction->equipment->tag_id ?? 'N/A') }})
                                                </a>
                                            @else
                                                {{__('Maklumat Peralatan Tidak Sah')}}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            @if (optional($transaction->loanApplication)->user)
                                                <a href="{{ route('users.show', $transaction->loanApplication->user->id) }}" title="{{__('Lihat Profil Pemohon')}}">
                                                    {{ e(optional($transaction->loanApplication->user)->name ?? __('N/A')) }}
                                                </a>
                                            @else
                                                {{__('N/A')}}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional($transaction->issue_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional(optional($transaction->loanApplication)->loan_end_date)->translatedFormat('d M Y') ?? __('N/A') }}
                                        </td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transaction->status) }}">
                                                {{ Str::title(str_replace('_', ' ', $transaction->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-end">
                                            <a href="{{ route('loan-applications.return', $transaction) }}"
                                               class="btn btn-sm btn-success d-inline-flex align-items-center">
                                                <i class="bi bi-arrow-return-left me-1"></i>
                                                {{ __('Rekod Pulangan') }}
                                            </a>
                                            <a href="{{ route('loan-transactions.show', $transaction) }}"
                                               class="btn btn-sm btn-outline-secondary ms-1 d-inline-flex align-items-center" title="{{__('Lihat Transaksi')}}">
                                                <i class="bi bi-eye-fill"></i>
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
