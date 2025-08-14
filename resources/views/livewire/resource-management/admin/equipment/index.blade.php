{{-- resources/views/livewire/resource-management/admin/equipment/index.blade.php --}}
<div>
    @section('title', __('Pengurusan Peralatan ICT'))

    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
                <i class="bi bi-hdd-stack-fill me-2"></i>
                {{ __('Pengurusan Peralatan ICT') }}
            </h1>
            @can('create', App\Models\Equipment::class)
                <button wire:click="$dispatch('open-modal', { modalId: 'equipmentFormModal', action: 'create' })"
                    class="btn btn-primary d-inline-flex align-items-center motac-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan Baharu') }}
                </button>
            @endcan
        </div>

        @include('_partials._alerts.alert-general')

        {{-- Search and Filter Card --}}
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3 motac-card-header">
                <h5 class="card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-funnel-fill me-2"></i>
                    {{ __('Carian dan Saringan Peralatan') }}
                </h5>
            </div>
            <div class="card-body p-3 motac-card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="searchTerm"
                            class="form-label form-label-sm">{{ __('Carian (Tag, Jenama, Model, Siri)') }}</label>
                        <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTerm"
                            class="form-control form-control-sm" placeholder="{{ __('Taip kata kunci...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterAssetType" class="form-label form-label-sm">{{ __('Jenis Aset') }}</label>
                        <select wire:model.live="filterAssetType" id="filterAssetType"
                            class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jenis') }}</option>
                            @foreach ($assetTypeOptions as $key => $label)
                                <option value="{{ $key }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label form-label-sm">{{ __('Status Operasi') }}</label>
                        <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Status') }}</option>
                            @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="resetFilters"
                            class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline" type="button"
                            title="{{ __('Set Semula Carian & Saringan') }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Set Semula') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Equipment Table Card --}}
        <div class="card shadow-sm motac-card">
            <div class="card-body p-0 motac-card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="cursor:pointer;"
                                    wire:click="sortBy('tag_id')">{{ __('No. Tag Aset') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Jenis & Model') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">
                                    {{ __('Status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Lokasi') }}
                                </th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="5" class="p-0 border-0">
                                    <div wire:loading.flex class="progress bg-transparent rounded-0"
                                        style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @forelse ($equipmentList as $item)
                                <tr wire:key="equipment-item-{{ $item->id }}">
                                    <td class="px-3 py-2 small fw-medium text-dark">{{ $item->tag_id ?? __('N/A') }}
                                    </td>
                                    <td class="px-3 py-2 small">
                                        <span
                                            class="fw-medium text-dark">{{ $item->asset_type_label ?? $item->asset_type }}</span>
                                        <div class="text-muted">{{ $item->brand ?? '' }} {{ $item->model ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 small text-center">
                                        <x-equipment-status-badge :status="$item->status" />
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? __('Umum') }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $item)
                                                <button
                                                    wire:click="$dispatch('open-modal', { modalId: 'viewEquipmentModal', action: 'view', equipmentId: {{ $item->id }} })"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-info border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Lihat Butiran') }}"><i
                                                        class="bi bi-eye-fill"></i></button>
                                            @endcan
                                            @can('update', $item)
                                                <button
                                                    wire:click="$dispatch('open-modal', { modalId: 'equipmentFormModal', action: 'edit', equipmentId: {{ $item->id }} })"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Kemaskini') }}"><i
                                                        class="bi bi-pencil-fill"></i></button>
                                            @endcan
                                            @can('delete', $item)
                                                <button
                                                    wire:click="$dispatch('open-delete-modal', { id: {{ $item->id }}, itemDescription: '{{ e(addslashes($item->tag_id)) }}', deleteMethod: 'deleteEquipment' })"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Padam') }}"><i class="bi bi-trash3-fill"></i></button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-5 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted small">
                                            <i class="bi bi-hdd-stack-fill fs-1 text-secondary mb-2"></i>
                                            <p>{{ __('Tiada rekod peralatan ICT ditemui.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($equipmentList->hasPages())
                    <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ADJUSTMENT: Include modal partials here --}}
    @include('livewire.resource-management.admin.equipment.partials.equipment-form-modal')
    @include('livewire.resource-management.admin.equipment.partials.view-equipment-modal')
    @include('livewire.resource-management.admin.equipment.partials.delete-confirmation-modal')
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const openModal = (modalId) => {
                let modalElement = document.getElementById(modalId);
                if (modalElement) {
                    // Ensure no lingering backdrops
                    const existingBackdrop = document.querySelector('.modal-backdrop.fade.show');
                    if (existingBackdrop) {
                        existingBackdrop.remove();
                    }
                    var myModal = new bootstrap.Modal(modalElement);
                    myModal.show();
                }
            };

            const closeModal = (modalId) => {
                let modalElement = document.getElementById(modalId);
                if (modalElement) {
                    var myModal = bootstrap.Modal.getInstance(modalElement);
                    if (myModal) {
                        myModal.hide();
                    }
                }
            };

            Livewire.on('open-modal', (event) => {
                // Check if event is an array and use first element if so (new LW3 syntax)
                let modalId = Array.isArray(event) ? event[0].modalId : event.modalId;
                if (modalId) openModal(modalId);
            });

            Livewire.on('close-modal', (event) => {
                let modalId = Array.isArray(event) ? event[0].modalId : event.modalId;
                if (modalId) closeModal(modalId);
            });

            Livewire.on('toastr', event => {
                let message = Array.isArray(event) ? event[0].message : event.message;
                let type = Array.isArray(event) ? event[0].type : event.type;
                if (window.toastr && message && type) {
                    window.toastr[type](message);
                } else {
                    console.warn('Toastr not available or event data missing. Message:', type, message);
                    alert(message); // Fallback
                }
            });
        });
    </script>
@endpush
