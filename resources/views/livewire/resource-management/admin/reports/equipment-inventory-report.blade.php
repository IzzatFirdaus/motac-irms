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
