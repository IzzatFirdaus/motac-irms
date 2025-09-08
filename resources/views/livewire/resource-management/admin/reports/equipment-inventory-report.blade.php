<<<<<<< HEAD
{{-- resources/views/reports/equipment-inventory.blade.php --}}
@extends('layouts.app')

@section('title', __('Laporan Inventori Peralatan ICT'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-archive-fill me-2"></i>{{ __('Laporan Inventori Peralatan ICT') }}
            </h1>
            @if (Route::has('reports.index'))
                <a href="{{ route('reports.index') }}"
                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                    <i class="bi bi-arrow-left me-1"></i>
                    {{ __('Kembali ke Senarai Laporan') }}
                </a>
            @endif
        </div>

        @include('partials._alerts.alert-general')

        @if ($equipment->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada peralatan ICT ditemui untuk laporan ini.') }}
            </div>
        @else
            <div class="card shadow-sm motac-card">
                <div class="card-header bg-light py-3 motac-card-header">
                    <h3 class="h5 card-title fw-semibold mb-0">{{ __('Senarai Peralatan') }}</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tag ID Aset') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Jenis Aset') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Jenama') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Model') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('No. Siri') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Status Operasi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Status Kondisi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Jabatan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Pengguna Semasa') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tarikh Pinjam') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipment as $item)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium">
                                            {{-- Public equipment show route is fine for a report link --}}
                                            <a href="{{ route('equipment.show', $item->id) }}">
                                                {{ optional($item)->tag_id ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 small">
                                            {{ $item->asset_type_translated ?? (optional($item)->asset_type ? __(Str::title(str_replace('_', ' ', optional($item)->asset_type))) : 'N/A') }}
                                        </td>
                                        <td class="px-3 py-2 small">{{ optional($item)->brand ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">{{ optional($item)->model ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small font-monospace">
                                            {{ optional($item)->serial_number ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">
                                            <span
                                                class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->status ?? '', 'equipment_status') }} fw-normal">
                                                {{ $item->status_translated ?? (optional($item)->status ? __(Str::title(str_replace('_', ' ', optional($item)->status))) : 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small">
                                            <span
                                                class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status ?? '', 'equipment_condition') }} fw-normal">
                                                {{ $item->condition_status_translated ?? (optional($item)->condition_status ? __(Str::title(str_replace('_', ' ', optional($item)->condition_status))) : 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small">
                                            {{ optional(optional($item)->department)->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">
                                            @if (optional($item)->activeLoanTransaction && optional(optional($item)->activeLoanTransaction->loanApplication)->user)
                                                {{ optional(optional($item)->activeLoanTransaction->loanApplication->user)->name ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small">
                                            @if (optional($item)->activeLoanTransaction)
                                                {{ optional(optional($item)->activeLoanTransaction)->issue_timestamp?->translatedFormat('d M Y') ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($equipment instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipment->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                        {{ $equipment->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
=======
<div>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold text-dark">
                    {{ __('Laporan Inventori Peralatan ICT') }}
                </h3>
                @if (Route::has('reports.index'))
                    <a href="{{ route('reports.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Kembali ke Senarai Laporan') }}
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <x-alert-bootstrap />

            {{-- Filters --}}
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="searchTagSerial"
                        class="form-label small text-muted">{{ __('Cari Tag/Siri/Jenama/Model') }}</label>
                    <input wire:model.live.debounce.300ms="searchTagSerial" type="search" id="searchTagSerial"
                        class="form-control form-control-sm" placeholder="{{ __('Cth: MOTAC/LPT/001') }}">
                </div>
                <div class="col-md-2">
                    <label for="filterAssetType" class="form-label small text-muted">{{ __('Jenis Aset') }}</label>
                    <select wire:model.live="filterAssetType" id="filterAssetType" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        @foreach ($assetTypeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterStatus" class="form-label small text-muted">{{ __('Status Operasi') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterCondition" class="form-label small text-muted">{{ __('Status Keadaan') }}</label>
                    <select wire:model.live="filterCondition" id="filterCondition" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Keadaan') }}</option>
                        @foreach ($conditionStatusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterLocation" class="form-label small text-muted">{{ __('Lokasi') }}</label>
                    <select wire:model.live="filterLocation" id="filterLocation" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Lokasi') }}</option>
                        @foreach ($locationOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100"
                        type="button">Reset</button>
                </div>
            </div>

            <div class="table-responsive">
                @if ($equipment->isNotEmpty())
                    <table class="table table-hover table-striped table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small">{{ __('Tag ID') }}</th>
                                <th class="small">{{ __('Jenis Aset') }}</th>
                                <th class="small">{{ __('Jenama') }}</th>
                                <th class="small">{{ __('Model') }}</th>
                                <th class="small">{{ __('No. Siri') }}</th>
                                <th class="small">{{ __('Status Operasi') }}</th>
                                <th class="small">{{ __('Status Keadaan') }}</th>
                                <th class="small">{{ __('Lokasi') }}</th>
                                <th class="small">{{ __('Pengguna Semasa') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50">
                                <td colspan="9" class="p-0">
                                    <div wire:loading.flex class="progress" style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($equipment as $item)
                                <tr wire:key="equip-{{ $item->id }}">
                                    <td class="small align-middle font-monospace">{{ $item->tag_id ?? 'N/A' }}</td>
                                    <td class="small align-middle">{{ $item->asset_type_label }}</td>
                                    <td class="small align-middle">{{ $item->brand ?? 'N/A' }}</td>
                                    <td class="small align-middle">{{ $item->model ?? 'N/A' }}</td>
                                    <td class="small align-middle font-monospace">{{ $item->serial_number ?? 'N/A' }}
                                    </td>
                                    <td class="small align-middle">
                                        <span
                                            class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->status, 'bootstrap_badge') }} px-2 py-1">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">
                                        <span
                                            class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status, 'bootstrap_badge_condition') }} px-2 py-1">
                                            {{ $item->condition_status_label }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">
                                        {{ $item->definedLocation->name ?? ($item->current_location ?? 'N/A') }}</td>
                                    <td class="small align-middle">
                                        @if ($item->status === \App\Models\Equipment::STATUS_ON_LOAN && $item->activeLoanTransactionItem)
                                            {{ $item->activeLoanTransactionItem->loanTransaction->loanApplication->user->name ?? __('Dipinjam') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($equipment->hasPages())
                        <div class="card-footer bg-light border-top-0 d-flex justify-content-center pt-3 pb-2">
                            {{ $equipment->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('Tiada peralatan ICT ditemui untuk laporan ini.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
