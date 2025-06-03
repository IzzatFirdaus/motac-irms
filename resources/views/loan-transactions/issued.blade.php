{{-- resources/views/loan-transactions/issued.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Peralatan Sedang Dipinjam'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Peralatan Sedang Dipinjam') }}</h1>
        </div>

        @include('_partials._alerts.alert-general') {{-- CORRECTED INCLUDE PATH --}}

        @if ($issuedLoanItems->isEmpty())
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
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Permohonan #') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Dikeluarkan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Jangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($issuedLoanItems as $item)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium">
                                            @if($item->equipment)
                                                {{-- This route 'equipment.show' is global and correct --}}
                                                <a href="{{ route('equipment.show', $item->equipment_id) }}" title="{{__('Lihat Peralatan')}}">
                                                    {{ e(optional($item->equipment)->brand_model_serial ?? optional($item->equipment)->tag_id) }}
                                                </a>
                                            @else
                                                {{__('Maklumat Peralatan Tidak Sah')}}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ e(optional($item->equipment)->asset_type_label ?? '') }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            @if (optional($item->loanTransaction->loanApplication)->user)
                                                {{-- This route 'users.show' is global and correct --}}
                                                <a href="{{ route('users.show', $item->loanTransaction->loanApplication->user->id) }}" title="{{__('Lihat Profil Pemohon')}}">
                                                    {{ e(optional($item->loanTransaction->loanApplication->user)->name ?? __('N/A')) }}
                                                </a>
                                            @else
                                                {{__('N/A')}}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small">
                                            {{-- This route 'loan-applications.show' is global and correct --}}
                                             <a href="{{ route('loan-applications.show', $item->loanTransaction->loan_application_id) }}" title="{{__('Lihat Permohonan')}}">
                                                #{{ $item->loanTransaction->loan_application_id }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional(optional($item->loanTransaction)->issue_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional(optional($item->loanTransaction->loanApplication)->loan_end_date)->translatedFormat('d M Y') ?? __('N/A') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @can('createReturn', $item->loanTransaction->loanApplication)
                                            {{-- CORRECTED ROUTE NAME --}}
                                            <a href="{{ route('resource-management.bpm.loan-transactions.return.form', $item->loanTransaction) }}"
                                                class="btn btn-sm btn-success d-inline-flex align-items-center">
                                                <i class="bi bi-arrow-return-left me-1"></i>
                                                {{ __('Rekod Pulangan') }}
                                            </a>
                                            @endcan
                                            {{-- CORRECTED ROUTE NAME --}}
                                            <a href="{{ route('resource-management.bpm.loan-transactions.show', $item->loanTransaction) }}"
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

            @if ($issuedLoanItems->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $issuedLoanItems->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
