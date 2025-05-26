<div>
    @section('title', __('Pengurusan Peralatan ICT'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Senarai Peralatan ICT MOTAC') }}</h1>
        @can('create', App\Models\Equipment::class)
            <button wire:click="openCreateModal" type="button"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="ti ti-plus {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Tambah Peralatan Baru') }}
            </button>
        @endcan
    </div>

    {{-- Alerts --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters and Search --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label for="eqSearchTerm" class="form-label">{{ __('Carian (Tag/Serial/Jenama/Model)') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="eqSearchTerm"
                        placeholder="{{ __('Masukkan kata kunci...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="eqFilterAssetType" class="form-label">{{ __('Jenis Aset') }}</label>
                    <select wire:model.live="filterAssetType" id="eqFilterAssetType" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        @foreach ($assetTypeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="eqFilterStatus" class="form-label">{{ __('Status Operasi') }}</label>
                    <select wire:model.live="filterStatus" id="eqFilterStatus" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="eqFilterCondition" class="form-label">{{ __('Status Kondisi') }}</label>
                    <select wire:model.live="filterCondition" id="eqFilterCondition" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Kondisi') }}</option>
                        @foreach ($conditionStatusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-12">
                    <label for="eqFilterDepartmentId" class="form-label">{{ __('Jabatan Pemilik') }}</label>
                    <select wire:model.live="filterDepartmentId" id="eqFilterDepartmentId"
                        class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach ($departmentOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Equipment Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Tag Aset') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis Aset') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenama & Model') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Siri') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Operasi') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Kondisi') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Lokasi Semasa') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span
                                class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="9" class="p-0">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        </td>
        </tr>
        @forelse ($equipmentList as $equipment)
            <tr wire:key="equipment-{{ $equipment->id }}">
                <td class="px-3 py-2 small text-dark fw-medium">{{ $equipment->tag_id }}</td>
                <td class="px-3 py-2 small text-muted">
                    {{ $assetTypeOptions[$equipment->asset_type] ?? $equipment->asset_type }}</td>
                <td class="px-3 py-2 small text-muted">{{ $equipment->brand }} {{ $equipment->model }}</td>
                <td class="px-3 py-2 small text-muted">{{ $equipment->serial_number }}</td>
                <td class="px-3 py-2 small">
                    <span
                        class="badge rounded-pill {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($equipment->status) }}">
                        {{ $statusOptions[$equipment->status] ?? $equipment->status }}
                    </span>
                </td>
                <td class="px-3 py-2 small">
                    <span
                        class="badge rounded-pill {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($equipment->condition_status) }}">
                        {{ $conditionStatusOptions[$equipment->condition_status] ?? $equipment->condition_status }}
                    </span>
                </td>
                <td class="px-3 py-2 small text-muted">
                    {{ $equipment->current_location ?: $equipment->department->name ?? '-' }}</td>
                <td class="px-3 py-2 small text-muted">{{ $equipment->department->name ?? '-' }}</td>
                <td class="px-3 py-2 text-end">
                    @can('update', $equipment)
                        <button wire:click="openEditModal({{ $equipment->id }})" type="button"
                            class="btn btn-sm btn-outline-secondary border-0 p-1" title="{{ __('Kemaskini') }}">
                            <i class="ti ti-pencil fs-6"></i>
                        </button>
                    @endcan
                    @can('delete', $equipment)
                        <button wire:click="openDeleteModal({{ $equipment->id }})" type="button"
                            class="btn btn-sm btn-outline-danger border-0 p-1 ms-1" title="{{ __('Padam') }}">
                            <i class="ti ti-trash fs-6"></i>
                        </button>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-3 py-5 text-center">
                    <div class="d-flex flex-column align-items-center text-muted small">
                        <i class="ti ti-mood-empty fs-1 mb-2 text-secondary"></i>
                        {{ __('Tiada rekod peralatan ICT ditemui.') }}
                        @if (empty($searchTerm) &&
                                empty($filterAssetType) &&
                                empty($filterStatus) &&
                                empty($filterCondition) &&
                                empty($filterDepartmentId))
                            @can('create', App\Models\Equipment::class)
                                <button wire:click="openCreateModal" type="button" class="btn btn-sm btn-primary mt-3">
                                    {{ __('Tambah Peralatan Pertama Anda') }}
                                </button>
                            @endcan
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if ($equipmentList->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $equipmentList->links() }}
    </div>
@endif

{{-- Create/Edit Equipment Modal --}}
<div class="modal fade @if ($showCreateModal || $showEditModal) show d-block @endif" id="equipmentFormModal" tabindex="-1"
    aria-labelledby="equipmentFormModalLabel" @if (!($showCreateModal || $showEditModal)) aria-hidden="true" @endif
    @if ($showCreateModal || $showEditModal) style="background-color: rgba(0,0,0,0.5);" @endif wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form wire:submit.prevent="{{ $showEditModal ? 'updateEquipment' : 'createEquipment' }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentFormModalLabel">
                        {{ $showEditModal ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_tag_id" class="form-label">{{ __('No. Tag Aset MOTAC') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="tag_id" id="modal_tag_id"
                                    class="form-control @error('tag_id') is-invalid @enderror">
                                @error('tag_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_asset_type" class="form-label">{{ __('Jenis Aset') }}<span
                                        class="text-danger">*</span></label>
                                <select wire:model.defer="asset_type" id="modal_asset_type"
                                    class="form-select @error('asset_type') is-invalid @enderror">
                                    <option value="">{{ __('- Sila Pilih -') }}</option>
                                    @foreach ($assetTypeOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('asset_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_brand" class="form-label">{{ __('Jenama') }}</label>
                                <input type="text" wire:model.defer="brand" id="modal_brand"
                                    class="form-control @error('brand') is-invalid @enderror">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_purchase_date"
                                    class="form-label">{{ __('Tarikh Pembelian') }}</label>
                                <input type="date" wire:model.defer="purchase_date" id="modal_purchase_date"
                                    class="form-control @error('purchase_date') is-invalid @enderror">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_status" class="form-label">{{ __('Status Operasi') }}<span
                                        class="text-danger">*</span></label>
                                <select wire:model.defer="status" id="modal_status"
                                    class="form-select @error('status') is-invalid @enderror">
                                    @foreach ($statusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_department_id"
                                    class="form-label">{{ __('Jabatan Pemilik (jika ada)') }}</label>
                                <select wire:model.defer="department_id" id="modal_department_id"
                                    class="form-select @error('department_id') is-invalid @enderror">
                                    <option value="">{{ __('- Tiada -') }}</option>
                                    @foreach ($departmentOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_serial_number" class="form-label">{{ __('No. Siri') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="serial_number" id="modal_serial_number"
                                    class="form-control @error('serial_number') is-invalid @enderror">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_model_name" class="form-label">{{ __('Model') }}</label>
                                <input type="text" wire:model.defer="model_name" id="modal_model_name"
                                    class="form-control @error('model_name') is-invalid @enderror">
                                @error('model_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_warranty_expiry_date"
                                    class="form-label">{{ __('Tarikh Tamat Waranti') }}</label>
                                <input type="date" wire:model.defer="warranty_expiry_date"
                                    id="modal_warranty_expiry_date"
                                    class="form-control @error('warranty_expiry_date') is-invalid @enderror">
                                @error('warranty_expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_condition_status"
                                    class="form-label">{{ __('Status Kondisi Fizikal') }}<span
                                        class="text-danger">*</span></label>
                                <select wire:model.defer="condition_status" id="modal_condition_status"
                                    class="form-select @error('condition_status') is-invalid @enderror">
                                    @foreach ($conditionStatusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('condition_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="modal_current_location"
                                    class="form-label">{{ __('Lokasi Semasa (Jika bukan di jabatan pemilik)') }}</label>
                                <input type="text" wire:model.defer="current_location" id="modal_current_location"
                                    class="form-control @error('current_location') is-invalid @enderror">
                                @error('current_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Full Width --}}
                        <div class="col-12">
                            <label for="modal_notes" class="form-label">{{ __('Nota Tambahan') }}</label>
                            <textarea wire:model.defer="notes" id="modal_notes" rows="3"
                                class="form-control @error('notes') is-invalid @enderror"></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal"
                        wire:loading.attr="disabled">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $showEditModal ? __('Kemaskini') : __('Simpan') }}</span>
                        <span wire:loading class="d-inline-flex align-items-center">
                            {{ __('Memproses...') }} <div class="spinner-border spinner-border-sm ms-1"
                                role="status"><span class="visually-hidden">Loading...</span></div>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade @if ($showDeleteModal && $deletingEquipment) show d-block @endif" id="deleteEquipmentModal"
    tabindex="-1" aria-labelledby="deleteEquipmentModalLabel"
    @if (!($showDeleteModal && $deletingEquipment)) aria-hidden="true" @endif
    @if ($showDeleteModal && $deletingEquipment) style="background-color: rgba(0,0,0,0.5);" @endif wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEquipmentModalLabel">{{ __('Padam Peralatan ICT') }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="ti ti-alert-triangle fs-2 text-danger me-3"></i>
                    <div>
                        <p class="mb-1">
                            {{ __('Adakah anda pasti ingin memadam peralatan ini?') }}<br>
                            @if ($deletingEquipment)
                                <strong class="d-block mt-1 fs-5">{{ $deletingEquipment->tag_id }} -
                                    {{ $deletingEquipment->brand }} {{ $deletingEquipment->model }}</strong>
                            @endif
                        </p>
                        <p class="small text-muted">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal"
                    wire:loading.attr="disabled">{{ __('Batal') }}</button>
                <button wire:click="deleteEquipment" type="button" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Padam') }}</span>
                    <span wire:loading class="d-inline-flex align-items-center">
                        {{ __('Memadam...') }} <div class="spinner-border spinner-border-sm ms-1" role="status">
                            <span class="visually-hidden">Loading...</span></div>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>

@push('custom-scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const equipmentFormModalEl = document.getElementById('equipmentFormModal');
            const deleteEquipmentModalEl = document.getElementById('deleteEquipmentModal');
            let equipmentFormModalInstance = null;
            let deleteEquipmentModalInstance = null;

            if (equipmentFormModalEl) {
                equipmentFormModalInstance = new bootstrap.Modal(equipmentFormModalEl);
            }
            if (deleteEquipmentModalEl) {
                deleteEquipmentModalInstance = new bootstrap.Modal(deleteEquipmentModalEl);
            }

            @this.on('show-create-edit-modal', () => {
                if (equipmentFormModalInstance) equipmentFormModalInstance.show();
            });

            @this.on('show-delete-modal', () => {
                if (deleteEquipmentModalInstance) deleteEquipmentModalInstance.show();
            });

            @this.on('hide-modal', () => {
                if (equipmentFormModalInstance) {
                    const livewireModalInstance = bootstrap.Modal.getInstance(equipmentFormModalEl);
                    if (livewireModalInstance && livewireModalInstance._isShown) {
                        livewireModalInstance.hide();
                    }
                }
                if (deleteEquipmentModalInstance) {
                    const livewireDeleteModalInstance = bootstrap.Modal.getInstance(deleteEquipmentModalEl);
                    if (livewireDeleteModalInstance && livewireDeleteModalInstance._isShown) {
                        livewireDeleteModalInstance.hide();
                    }
                }
            });

            if (equipmentFormModalEl) {
                equipmentFormModalEl.addEventListener('hidden.bs.modal', (event) => {
                    if (@this.get('showCreateModal') || @this.get('showEditModal')) {
                        @this.call('closeModal');
                    }
                });
            }
            if (deleteEquipmentModalEl) {
                deleteEquipmentModalEl.addEventListener('hidden.bs.modal', (event) => {
                    if (@this.get('showDeleteModal')) {
                        @this.call('closeModal');
                    }
                });
            }
        });
    </script>
@endpush
