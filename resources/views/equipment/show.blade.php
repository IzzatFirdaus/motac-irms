@extends('layouts.app')

@section('title', __('Butiran Peralatan ICT #') . $equipment->id)

@section('content')
<div class="container-fluid py-4"> {{-- container-fluid if full width is desired, or container for standard width --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h3 mb-2 mb-sm-0 text-body">
            {{ __('Butiran Peralatan ICT #') }}{{ $equipment->id }}
            <span class="text-muted fs-5">({{ __('Tag') }}: {{ $equipment->tag_id ?? __('N/A') }})</span>
        </h1>
        <div>
            @can('update', $equipment)
                <a href="{{ route('resource-management.admin.equipment-admin.edit', $equipment) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                </a>
            @endcan
             {{-- Assuming x-back-button is Bootstrap compatible or you replace it --}}
            <x-back-button :route="route('equipment.index')" :text="__('Kembali ke Senarai')" class="btn-sm" />
        </div>
    </div>

    @if (session()->has('success'))
        {{-- Assuming x-alert component generates Bootstrap alert --}}
        <x-alert type="success" :message="session('success')" class="mb-4" :dismissible="true"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4" :dismissible="true"/>
    @endif

    <div class="row">
        <div class="col-lg-7 mb-4 mb-lg-0">
            {{-- Assuming x-card component generates Bootstrap card structure --}}
            <x-card :title="__('Maklumat Asas Peralatan')" :collapsible="false" :collapsed="false">
                 <x-slot name="content">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Jenis Aset') }}</label>
                            <p class="mb-0">{{ $equipment->asset_type_label ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Jenama') }}</label>
                            <p class="mb-0">{{ $equipment->brand ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Model') }}</label>
                            <p class="mb-0">{{ $equipment->model ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Tag ID MOTAC') }}</label>
                            <p class="mb-0">{{ $equipment->tag_id ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Nombor Siri') }}</label>
                            <p class="mb-0">{{ $equipment->serial_number ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Tarikh Pembelian') }}</label>
                            <p class="mb-0">{{ $equipment->purchase_date?->translatedFormat('d M Y') ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-0">{{ __('Tarikh Tamat Waranti') }}</label>
                            <p class="mb-0">{{ $equipment->warranty_expiry_date?->translatedFormat('d M Y') ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">{{ __('Status Operasi') }}</label>
                            <p class="mb-0">
                                {{-- Ensure Helpers::getStatusColorClass returns Bootstrap 5 badge classes like 'bg-success', 'text-dark bg-warning' --}}
                                <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->status, 'bootstrap_badge') }}">
                                    {{ $equipment->status_label ?? __('N/A') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">{{ __('Status Keadaan Fizikal') }}</label>
                            <p class="mb-0">
                                 <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->condition_status, 'bootstrap_badge_condition') }}">
                                    {{ $equipment->condition_status_label ?? __('N/A') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6"> {{-- Was col-md-6, ensuring it fits layout --}}
                            <label class="form-label small text-muted mb-0">{{ __('Lokasi Semasa') }}</label>
                            <p class="mb-0">{{ $equipment->current_location ?? __('N/A') }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-muted mb-0">{{ __('Catatan') }}</label>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $equipment->notes ?? '-' }}</p>
                        </div>
                    </div>
                </x-slot>
            </x-card>
        </div>
        <div class="col-lg-5">
             <x-card :title="__('Maklumat Tambahan')" :collapsible="false" :collapsed="false">
                <x-slot name="content">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small text-muted mb-0">{{ __('Dicipta Oleh') }}</label>
                            <p class="mb-0">{{ $equipment->creator->name ?? __('N/A') }} <br><small class="text-muted">{{ __('pada') }} {{ $equipment->created_at?->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</small></p>
                        </div>
                         <div class="col-12">
                            <label class="form-label small text-muted mb-0">{{ __('Dikemaskini Terakhir Oleh') }}</label>
                            <p class="mb-0">{{ $equipment->updater->name ?? __('N/A') }} <br><small class="text-muted">{{ __('pada') }} {{ $equipment->updated_at?->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</small></p>
                        </div>
                    </div>
                </x-slot>
            </x-card>
        </div>
    </div>

    @if ($equipment->loanTransactionItems->isNotEmpty())
        <x-card :title="__('Sejarah Pinjaman (Item Transaksi Berkaitan Peralatan Ini)')" class="mt-4" :collapsible="true" :collapsed="false">
            <x-slot name="content">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="small">{{ __('ID Transaksi') }}</th>
                                <th class="small">{{ __('Permohonan #') }}</th>
                                <th class="small">{{ __('Jenis Transaksi') }}</th>
                                <th class="small">{{ __('Status Item') }}</th>
                                <th class="small">{{ __('Tarikh Transaksi') }}</th>
                                <th class="small">{{ __('Catatan Item') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipment->loanTransactionItems->sortByDesc('loanTransaction.transaction_date') as $transactionItem)
                                <tr>
                                    <td class="small">
                                         <a href="{{ route('resource-management.admin.loan-transactions.show', $transactionItem->loanTransaction->id) }}" class="text-decoration-none" title="{{__('Lihat Transaksi')}}">
                                            #{{ $transactionItem->loanTransaction->id }}
                                        </a>
                                    </td>
                                    <td class="small">
                                        <a href="{{ route('resource-management.my-applications.loan-applications.show', $transactionItem->loanTransaction->loan_application_id) }}" class="text-decoration-none" title="{{__('Lihat Permohonan')}}">
                                            #{{ $transactionItem->loanTransaction->loan_application_id }}
                                        </a>
                                    </td>
                                    <td class="small">
                                        <span class="badge rounded-pill {{ $transactionItem->loanTransaction->type === 'issue' ? 'bg-info text-dark' : 'bg-primary' }}">
                                            {{ __(Str::ucfirst($transactionItem->loanTransaction->type)) }}
                                        </span>
                                    </td>
                                    <td class="small">
                                         <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transactionItem->status, 'bootstrap_badge') }}">
                                            {{ $transactionItem->status_translated ?? __(Str::title(str_replace('_', ' ', $transactionItem->status))) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $transactionItem->loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}</td>
                                    <td class="small">{{ $transactionItem->item_notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-slot>
        </x-card>
    @else
         <x-alert type="info" :message="__('Tiada sejarah pinjaman untuk peralatan ini.')" class="mt-4"/>
    @endif
</div>
@endsection
