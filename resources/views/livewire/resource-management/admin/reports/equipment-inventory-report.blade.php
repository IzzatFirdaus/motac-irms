{{-- resources/views/livewire/resource-management/admin/reports/equipment-inventory-report.blade.php --}}
<div>
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

    {{-- Filters --}}
    <div class="card mb-4 motac-card">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Cari Tag/Siri/Jenama/Model/Kod Item') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="search"
                        class="form-control form-control-sm" placeholder="{{ __('Cth: MOTAC/LPT/001') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Jenis Aset') }}</label>
                    <select wire:model.live="filterAssetType" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        @foreach ($assetTypeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Status Operasi') }}</label>
                    <select wire:model.live="filterStatus" class="form-select form-select-sm">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Status Keadaan') }}</label>
                    <select wire:model.live="filterCondition" class="form-select form-select-sm">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach ($conditionStatusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Jabatan Pemilik') }}</label>
                    <select wire:model.live="filterDepartmentId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach ($departmentOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Lokasi Fizikal') }}</label>
                    <select wire:model.live="filterLocationId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Lokasi') }}</option>
                        @foreach ($locationOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Kategori Peralatan') }}</label>
                    <select wire:model.live="filterCategoryId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Kategori') }}</option>
                        @foreach ($categoryOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm w-100">{{ __('Reset') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 motac-card-header">
            <h3 class="h5 card-title fw-semibold mb-0">{{ __('Senarai Peralatan') }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if ($reportData->isNotEmpty())
                    <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="setSortBy('tag_id')" style="cursor:pointer;">{{ __('Tag ID Aset') }}</th>
                                <th>{{ __('Jenis Aset') }}</th>
                                <th>{{ __('Jenama') }}</th>
                                <th>{{ __('Model') }}</th>
                                <th>{{ __('No. Siri') }}</th>
                                <th>{{ __('Status Operasi') }}</th>
                                <th>{{ __('Status Kondisi') }}</th>
                                <th>{{ __('Jabatan') }}</th>
                                <th>{{ __('Lokasi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData as $item)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">{{ $item->tag_id ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">{{ $item->asset_type_label ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">{{ $item->brand ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">{{ $item->model ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small font-monospace">{{ $item->serial_number ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->status ?? '') }} fw-normal">
                                            {{ $item->status_label ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status ?? '') }} fw-normal">
                                            {{ $item->condition_status_label ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $item->department?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">{{ $item->location?->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($reportData->hasPages())
                        <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                            {{ $reportData->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada peralatan ICT ditemui untuk laporan ini.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
