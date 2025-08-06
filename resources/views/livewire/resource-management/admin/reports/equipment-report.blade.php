{{-- resources/views/livewire/resource-management/admin/reports/equipment-report.blade.php --}}
<div>
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold text-dark d-flex align-items-center">
                    <i class="bi bi-laptop-fill me-2"></i>
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
        <div class="card-body p-3 p-md-4 motac-card-body">
            <x-alert-bootstrap />

            {{-- Filters --}}
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-3 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Cari Tag/Siri/Jenama/Model/Kod Item') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="search"
                        class="form-control form-control-sm" placeholder="{{ __('Cth: MOTAC/LPT/001') }}">
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Jenis Aset') }}</label>
                    <select wire:model.live="filterAssetType" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        @foreach ($assetTypeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-1">
                    <label class="form-label form-label-sm">{{ __('Status Operasi') }}</label>
                    <select wire:model.live="filterStatus" class="form-select form-select-sm">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Status Keadaan') }}</label>
                    <select wire:model.live="filterCondition" class="form-select form-select-sm">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach ($conditionStatusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Jabatan Pemilik') }}</label>
                    <select wire:model.live="filterDepartmentId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach ($departmentOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Lokasi Fizikal') }}</label>
                    <select wire:model.live="filterLocationId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Lokasi') }}</option>
                        @foreach ($locationOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label form-label-sm">{{ __('Kategori Peralatan') }}</label>
                    <select wire:model.live="filterCategoryId" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Kategori') }}</option>
                        @foreach ($categoryOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 col-lg-1">
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100"
                        type="button">{{ __('Reset') }}</button>
                </div>
            </div>

            <div class="table-responsive">
                @if ($reportData->isNotEmpty())
                    <table class="table table-hover table-striped table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="setSortBy('tag_id')" style="cursor:pointer;">{{ __('Tag ID') }}</th>
                                <th wire:click="setSortBy('item_code')" style="cursor:pointer;">{{ __('Kod Item') }}</th>
                                <th wire:click="setSortBy('asset_type')" style="cursor:pointer;">{{ __('Jenis Aset') }}</th>
                                <th wire:click="setSortBy('brand')" style="cursor:pointer;">{{ __('Jenama') }}</th>
                                <th wire:click="setSortBy('model')" style="cursor:pointer;">{{ __('Model') }}</th>
                                <th wire:click="setSortBy('serial_number')" style="cursor:pointer;">{{ __('No. Siri') }}</th>
                                <th wire:click="setSortBy('status')" style="cursor:pointer;">{{ __('Status Operasi') }}</th>
                                <th wire:click="setSortBy('condition_status')" style="cursor:pointer;">{{ __('Status Keadaan') }}</th>
                                <th wire:click="setSortBy('location_id')" style="cursor:pointer;">{{ __('Lokasi') }}</th>
                                <th wire:click="setSortBy('department_id')" style="cursor:pointer;">{{ __('Jabatan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50">
                                <td colspan="10" class="p-0">
                                    <div wire:loading.flex class="progress" style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($reportData as $item)
                                <tr wire:key="equip-report-{{ $item->id }}">
                                    <td class="small align-middle font-monospace">{{ $item->tag_id ?? 'N/A' }}</td>
                                    <td class="small align-middle font-monospace">{{ $item->item_code ?? 'N/A' }}</td>
                                    <td class="small align-middle">{{ $item->asset_type_label }}</td>
                                    <td class="small align-middle">{{ $item->brand ?? 'N/A' }}</td>
                                    <td class="small align-middle">{{ $item->model ?? 'N/A' }}</td>
                                    <td class="small align-middle font-monospace">{{ $item->serial_number ?? 'N/A' }}</td>
                                    <td class="small align-middle">
                                        <span
                                            class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->status) }} px-2 py-1">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">
                                        <span
                                            class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status) }} px-2 py-1">
                                            {{ $item->condition_status_label }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">
                                        {{ $item->location?->name ?? ($item->current_location ?? 'N/A') }}
                                    </td>
                                    <td class="small align-middle">{{ $item->department?->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($reportData->hasPages())
                        <div class="card-footer bg-light border-top-0 d-flex justify-content-center pt-3 pb-2">
                            {{ $reportData->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('Tiada peralatan ICT ditemui untuk kriteria laporan ini.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
