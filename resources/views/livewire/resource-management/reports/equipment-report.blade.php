{{-- resources/views/livewire/resource-management/reports/equipment-report.blade.php --}}
<div>
    {{-- Report Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-2 mb-md-0 d-flex align-items-center">
            <i class="bi bi-archive-fill me-2"></i>
            {{ __('Laporan Inventori Peralatan ICT') }}
        </h1>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i> {{ __('Penapisan & Carian') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Carian (Tag, Jenama, Model)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari...') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Status Operasi') }}</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Jabatan') }}</label>
                    <select wire:model.live="filterDepartment" class="form-select">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Equipment Table --}}
    <div class="card motac-card">
        <div class="card-header bg-light motac-card-header">
            <h6 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-table me-2"></i> {{ __('Senarai Peralatan') }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Tag Aset') }}</th>
                        <th>{{ __('Jenis Peralatan') }}</th>
                        <th>{{ __('Jenama') }}</th>
                        <th>{{ __('Model') }}</th>
                        <th>{{ __('Status Operasi') }}</th>
                        <th>{{ __('Jabatan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipmentList as $equipment)
                        <tr>
                            <td>{{ $equipment->asset_tag }}</td>
                            <td>{{ $equipment->asset_type }}</td>
                            <td>{{ $equipment->brand }}</td>
                            <td>{{ $equipment->model }}</td>
                            <td>{{ $equipment->operational_status }}</td>
                            <td>{{ $equipment->department?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">{{ __('Tiada rekod peralatan ditemui.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($equipmentList->hasPages())
            <div class="card-footer bg-light d-flex justify-content-center py-2">
                {{ $equipmentList->links() }}
            </div>
        @endif
    </div>
</div>
