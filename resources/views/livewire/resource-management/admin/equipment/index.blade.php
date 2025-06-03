{{-- resources/views/livewire/resource-management/admin/equipment/index.blade.php --}}
<div>
    {{-- Page title is now handled by the #[Title] attribute in the Livewire component --}}

    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">{{ __('Senarai Keseluruhan Peralatan ICT') }}</h1>
            @can('create', App\Models\Equipment::class)
                <button wire:click="openCreateModal" class="btn btn-primary d-inline-flex align-items-center motac-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan Baharu') }}
                </button>
            @endcan
        </div>

        {{-- Livewire compatible session messages --}}
        @if (session()->has('message'))
            <x-alert type="success" :message="session('message')" class="mb-3" :dismissible="true" />
        @endif
        @if (session()->has('error'))
            <x-alert type="danger" :message="session('error')" class="mb-3" :dismissible="true" />
        @endif

        {{-- Search and Filter Card --}}
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-funnel-fill me-2"></i>
                    {{ __('Carian dan Saringan Peralatan') }}
                </h3>
            </div>
            <div class="card-body p-3 motac-card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="livewireSearchTerm" class="form-label form-label-sm">{{ __('Carian (Tag, Jenama, Model, Siri, Kod Item)') }}</label>
                        <input wire:model.live.debounce.300ms="searchTerm" type="text" id="livewireSearchTerm"
                            class="form-control form-control-sm" placeholder="{{ __('Taip kata kunci...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="livewireFilterAssetType" class="form-label form-label-sm">{{ __('Jenis Aset') }}</label>
                        <select wire:model.live="filterAssetType" id="livewireFilterAssetType" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jenis') }}</option>
                            @foreach ($assetTypeOptions as $key => $label)
                                <option value="{{ $key }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="livewireFilterStatus" class="form-label form-label-sm">{{ __('Status Operasi') }}</label>
                        <select wire:model.live="filterStatus" id="livewireFilterStatus" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Status') }}</option>
                             @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline" type="button" title="{{__('Set Semula Carian & Saringan')}}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Set Semula') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-hdd-stack me-2"></i>{{ __('Peralatan Sedia Ada') }}
                </h3>
                @if (isset($equipmentList) && $equipmentList->total() > 0)
                    <span class="text-muted small">
                        {{ __('Memaparkan :start - :end daripada :total rekod', [
                            'start' => $equipmentList->firstItem(),
                            'end' => $equipmentList->lastItem(),
                            'total' => $equipmentList->total(),
                        ]) }}
                    </span>
                @endif
            </div>
            <div class="card-body p-0 motac-card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="cursor:pointer;" wire:click="sortBy('tag_id')">
                                    {{ __('No. Tag Aset') }} @if($sortField === 'tag_id') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="cursor:pointer;" wire:click="sortBy('asset_type')">
                                    {{ __('Jenis Aset') }} @if($sortField === 'asset_type') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="cursor:pointer;" wire:click="sortBy('brand')">
                                    {{ __('Jenama & Model') }} @if($sortField === 'brand' || $sortField === 'model') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center" style="cursor:pointer;" wire:click="sortBy('status')">
                                    {{ __('Status Operasi') }} @if($sortField === 'status') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center" style="cursor:pointer;" wire:click="sortBy('condition_status')">
                                    {{ __('Kondisi') }} @if($sortField === 'condition_status') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="8" class="p-0 border-0">
                                    <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @forelse ($equipmentList as $item)
                                <tr wire:key="equipment-item-{{ $item->id }}">
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="#" wire:click.prevent="openViewModal({{ $item->id }})"
                                            class="text-decoration-none text-primary-emphasis motac-table-link">
                                            {{ $item->tag_id ?? __('N/A') }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $item->asset_type_label ?? ($item->asset_type ? __(Str::title(str_replace('_', ' ', $item->asset_type))) : __('N/A')) }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->brand ?? '' }} {{ $item->model ?? __('N/A') }}</td>
                                    <td class="px-3 py-2 small text-center">
                                        <x-equipment-status-badge :status="$item->status" />
                                    </td>
                                    <td class="px-3 py-2 small text-center">
                                        <x-equipment-status-badge :status="$item->condition_status" :type="'condition'" />
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? __('Umum') }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($item->activeLoanTransactionItem?->loanTransaction?->loanApplication?->user)
                                            <a href="{{ route('settings.users.show', $item->activeLoanTransactionItem->loanTransaction->loanApplication->user) }}" wire:navigate class="text-decoration-none motac-table-link">
                                                {{ $item->activeLoanTransactionItem->loanTransaction->loanApplication->user->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $item)
                                                <button wire:click="openViewModal({{ $item->id }})" type="button"
                                                    class="btn btn-sm btn-outline-info border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Lihat Butiran') }}"><i class="bi bi-eye-fill"></i></button>
                                            @endcan
                                            @can('update', $item)
                                                 <button wire:click="openEditModal({{ $item->id }})" type="button"
                                                    class="btn btn-sm btn-outline-primary border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Kemaskini') }}"><i class="bi bi-pencil-fill"></i></button>
                                            @endcan
                                            @can('delete', $item)
                                                <button wire:click="openDeleteModal({{ $item->id }})" type="button"
                                                    class="btn btn-sm btn-outline-danger border-0 p-1 motac-btn-icon"
                                                    title="{{ __('Padam') }}"><i class="bi bi-trash3-fill"></i></button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-5 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted small">
                                            <i class="bi bi-hdd-stack-fill fs-1 text-secondary mb-2"></i>
                                            <p class="mb-1">{{ __('Tiada rekod peralatan ICT ditemui.') }}</p>
                                            @if ($searchTerm || $filterAssetType || $filterStatus || $filterCondition || $filterDepartmentId)
                                                <p>{{ __('Sila cuba kata kunci carian/saringan yang berbeza atau set semula.') }}</p>
                                            @else
                                                @can('create', App\Models\Equipment::class)
                                                     <p>{{ __('Sila mula dengan') }} <button wire:click="openCreateModal" class="btn btn-link p-0 m-0 align-baseline">{{ __('menambah peralatan baharu') }}</button>.
                                                    </p>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (isset($equipmentList) && $equipmentList instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipmentList->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3 motac-card-footer d-flex justify-content-center">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal for Delete Confirmation --}}
        {{-- CORRECTED: Using $showDeleteModal and $deletingEquipment --}}
        @if($showDeleteModal)
        <div class="modal fade show" id="deleteConfirmationModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Sahkan Padam Peralatan') }}</h5>
                        {{-- CORRECTED: wire:click to use $showDeleteModal --}}
                        <button wire:click="$set('showDeleteModal', false)" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Anda pasti ingin memadam peralatan ini?') }}</p>
                        {{-- CORRECTED: Use $deletingEquipment --}}
                        @if($deletingEquipment)
                            <p><strong>{{ __('No. Tag Aset:') }}</strong> {{ $deletingEquipment->tag_id }}<br>
                               <strong>{{ __('Jenis:') }}</strong> {{ $deletingEquipment->asset_type_label ?? $deletingEquipment->asset_type }}<br>
                               <strong>{{ __('Model:') }}</strong> {{ $deletingEquipment->model ?? 'N/A' }}
                            </p>
                        @endif
                        <p class="text-danger fw-bold">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                         <p class="text-warning small">{{ __('Memadam peralatan ini juga akan memadam rekod berkaitan yang tidak kritikal. Rekod transaksi pinjaman lampau akan dikekalkan untuk tujuan audit.') }}</p>
                    </div>
                    <div class="modal-footer">
                        {{-- CORRECTED: wire:click to use $showDeleteModal --}}
                        <button wire:click="$set('showDeleteModal', false)" type="button" class="btn btn-secondary">{{ __('Batal') }}</button>
                        <button wire:click="deleteEquipment()" type="button" class="btn btn-danger">{{ __('Ya, Padam') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Create/Edit Modal --}}
        @if($showCreateModal || $showEditModal)
             <div class="modal fade show" id="equipmentFormModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form wire:submit.prevent="{{ $showEditModal ? 'updateEquipment' : 'createEquipment' }}">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $showEditModal ? __('Kemaskini Peralatan') : __('Tambah Peralatan Baharu') }}</h5>
                                <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="asset_type" class="form-label">{{ __('Jenis Aset') }} <span class="text-danger">*</span></label>
                                        <select wire:model.defer="asset_type" id="asset_type" class="form-select @error('asset_type') is-invalid @enderror">
                                            <option value="">{{ __('Pilih Jenis Aset') }}</option>
                                            @foreach(App\Models\Equipment::getAssetTypeOptions() as $key => $label)
                                                <option value="{{ $key }}">{{ __($label) }}</option>
                                            @endforeach
                                        </select>
                                        @error('asset_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tag_id" class="form-label">{{ __('No. Tag Aset') }} <span class="text-danger">*</span></label>
                                        <input type="text" wire:model.defer="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror">
                                        @error('tag_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-md-6 mb-3">
                                        <label for="brand" class="form-label">{{ __('Jenama') }}</label>
                                        <input type="text" wire:model.defer="brand" id="brand" class="form-control @error('brand') is-invalid @enderror">
                                        @error('brand') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="model_name" class="form-label">{{ __('Model') }}</label>
                                        <input type="text" wire:model.defer="model_name" id="model_name" class="form-control @error('model_name') is-invalid @enderror">
                                        @error('model_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="serial_number" class="form-label">{{ __('No. Siri') }}</label>
                                        <input type="text" wire:model.defer="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror">
                                        @error('serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="item_code" class="form-label">{{ __('Kod Item') }}</label>
                                        <input type="text" wire:model.defer="item_code" id="item_code" class="form-control @error('item_code') is-invalid @enderror">
                                        @error('item_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                 <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Deskripsi Terperinci') }}</label>
                                    <textarea wire:model.defer="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"></textarea>
                                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_date" class="form-label">{{ __('Tarikh Pembelian') }}</label>
                                        <input type="date" wire:model.defer="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror">
                                        @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_price" class="form-label">{{ __('Harga Belian (RM)') }}</label>
                                        <input type="number" step="0.01" wire:model.defer="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror">
                                        @error('purchase_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="warranty_expiry_date" class="form-label">{{ __('Tarikh Tamat Waranti') }}</label>
                                        <input type="date" wire:model.defer="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror">
                                        @error('warranty_expiry_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">{{ __('Status Operasi') }} <span class="text-danger">*</span></label>
                                        <select wire:model.defer="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                            @foreach(App\Models\Equipment::getStatusOptions() as $key => $label)
                                                <option value="{{ $key }}">{{ __($label) }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-md-6 mb-3">
                                        <label for="condition_status" class="form-label">{{ __('Status Keadaan Fizikal') }} <span class="text-danger">*</span></label>
                                        <select wire:model.defer="condition_status" id="condition_status" class="form-select @error('condition_status') is-invalid @enderror">
                                            @foreach(App\Models\Equipment::getConditionStatusOptions() as $key => $label)
                                                <option value="{{ $key }}">{{ __($label) }}</option>
                                            @endforeach
                                        </select>
                                        @error('condition_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="current_location" class="form-label">{{ __('Lokasi Semasa') }}</label>
                                        <input type="text" wire:model.defer="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror">
                                        @error('current_location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="acquisition_type" class="form-label">{{ __('Jenis Perolehan') }}</label>
                                        <select wire:model.defer="acquisition_type" id="acquisition_type" class="form-select @error('acquisition_type') is-invalid @enderror">
                                            <option value="">{{ __('Pilih Jenis Perolehan') }}</option>
                                            @foreach(App\Models\Equipment::getAcquisitionTypeOptions() as $key => $label)
                                                <option value="{{ $key }}">{{ __($label) }}</option>
                                            @endforeach
                                        </select>
                                        @error('acquisition_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="classification" class="form-label">{{ __('Klasifikasi') }}</label>
                                        <select wire:model.defer="classification" id="classification" class="form-select @error('classification') is-invalid @enderror">
                                             <option value="">{{ __('Pilih Klasifikasi') }}</option>
                                            @foreach(App\Models\Equipment::getClassificationOptions() as $key => $label)
                                                <option value="{{ $key }}">{{ __($label) }}</option>
                                            @endforeach
                                        </select>
                                        @error('classification') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="funded_by" class="form-label">{{ __('Sumber Dana') }}</label>
                                        <input type="text" wire:model.defer="funded_by" id="funded_by" class="form-control @error('funded_by') is-invalid @enderror" placeholder="Cth: Kerajaan, Pembelian Bahagian">
                                        @error('funded_by') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="supplier_name" class="form-label">{{ __('Nama Pembekal') }}</label>
                                        <input type="text" wire:model.defer="supplier_name" id="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror">
                                        @error('supplier_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">{{ __('Jabatan/Bahagian (Pemilik)') }}</label>
                                    <select wire:model.defer="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                        <option value="">{{ __('Pilih Jabatan') }}</option>
                                        @foreach($departmentOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('Nota Tambahan') }}</label>
                                    <textarea wire:model.defer="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"></textarea>
                                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button wire:click="closeModal" type="button" class="btn btn-secondary">{{ __('Batal') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    <span wire:loading wire:target="{{ $showEditModal ? 'updateEquipment' : 'createEquipment' }}" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    {{ $showEditModal ? __('Simpan Perubahan') : __('Tambah Peralatan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- View Modal --}}
        @if($showViewModal && $viewingEquipment)
            <div class="modal fade show" id="viewEquipmentModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Butiran Peralatan') }}: {{ $viewingEquipment->tag_id }}</h5>
                            <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('No. Tag Aset') }}:</strong> {{ $viewingEquipment->tag_id ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Jenis Aset') }}:</strong> {{ $viewingEquipment->asset_type_label ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Jenama') }}:</strong> {{ $viewingEquipment->brand ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Model') }}:</strong> {{ $viewingEquipment->model ?? 'N/A' }}</p>
                                    <p><strong>{{ __('No. Siri') }}:</strong> {{ $viewingEquipment->serial_number ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Kod Item') }}:</strong> {{ $viewingEquipment->item_code ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('Status Operasi') }}:</strong> <x-equipment-status-badge :status="$viewingEquipment->status" /></p>
                                    <p><strong>{{ __('Status Keadaan Fizikal') }}:</strong> <x-equipment-status-badge :status="$viewingEquipment->condition_status" :type="'condition'" /></p>
                                    <p><strong>{{ __('Jabatan Pemilik') }}:</strong> {{ $viewingEquipment->department->name ?? __('Umum') }}</p>
                                    <p><strong>{{ __('Lokasi Semasa') }}:</strong> {{ $viewingEquipment->current_location ?? 'N/A' }}</p>
                                     <p><strong>{{ __('Kategori') }}:</strong> {{ $viewingEquipment->equipmentCategory->name ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Sub-Kategori') }}:</strong> {{ $viewingEquipment->subCategory->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                             <hr>
                            <h6>{{__('Maklumat Perolehan')}}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('Tarikh Pembelian') }}:</strong> {{ $viewingEquipment->purchase_date ? $viewingEquipment->purchase_date->format('d M Y') : 'N/A' }}</p>
                                    <p><strong>{{ __('Harga Belian') }}:</strong> RM {{ $viewingEquipment->purchase_price !== null ? number_format($viewingEquipment->purchase_price, 2) : 'N/A' }}</p>
                                    <p><strong>{{ __('Tarikh Tamat Waranti') }}:</strong> {{ $viewingEquipment->warranty_expiry_date ? $viewingEquipment->warranty_expiry_date->format('d M Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                     <p><strong>{{ __('Jenis Perolehan') }}:</strong> {{ $viewingEquipment->acquisition_type_label ?? 'N/A' }}</p>
                                     <p><strong>{{ __('Klasifikasi') }}:</strong> {{ $viewingEquipment->classification_label ?? 'N/A' }}</p>
                                     <p><strong>{{ __('Nama Pembekal') }}:</strong> {{ $viewingEquipment->supplier_name ?? 'N/A' }}</p>
                                     <p><strong>{{ __('Sumber Dana') }}:</strong> {{ $viewingEquipment->funded_by ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if($viewingEquipment->description)
                                <hr><h6>{{__('Deskripsi')}}</h6>
                                <p>{{ $viewingEquipment->description }}</p>
                            @endif
                             @if($viewingEquipment->specifications && count((array)$viewingEquipment->specifications) > 0)
                                <hr><h6>{{__('Spesifikasi Tambahan')}}</h6>
                                <ul>
                                   @foreach((array)$viewingEquipment->specifications as $key => $value)
                                        <li><strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                                   @endforeach
                                </ul>
                            @endif
                            @if($viewingEquipment->notes)
                                <hr><h6>{{__('Nota')}}</h6>
                                <pre class="small bg-light p-2 rounded" style="white-space: pre-wrap;">{{ $viewingEquipment->notes }}</pre>
                            @endif
                            <hr>
                             <div class="row small text-muted">
                                <div class="col-md-6">
                                    <p><strong>{{ __('Dicipta Oleh') }}:</strong> {{ $viewingEquipment->creator->name ?? 'N/A' }} pada {{ $viewingEquipment->created_at ? $viewingEquipment->created_at->translatedFormat('d M Y, h:i A') : 'N/A'}}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('Dikemaskini Oleh') }}:</strong> {{ $viewingEquipment->updater->name ?? 'N/A' }} pada {{ $viewingEquipment->updated_at ? $viewingEquipment->updated_at->translatedFormat('d M Y, h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                             @can('update', $viewingEquipment)
                                <button wire:click="openEditModal({{ $viewingEquipment->id }})" type="button" class="btn btn-primary">
                                    <i class="bi bi-pencil-fill me-1"></i>{{ __('Kemaskini') }}
                                </button>
                            @endcan
                            <button wire:click="closeModal" type="button" class="btn btn-secondary">{{ __('Tutup') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
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
             if(modalId) openModal(modalId);
        });

        Livewire.on('close-modal', (event) => {
             let modalId = Array.isArray(event) ? event[0].modalId : event.modalId;
             if(modalId) closeModal(modalId);
        });

         Livewire.on('toastr', event => {
            let message = Array.isArray(event) ? event[0].message : event.message;
            let type = Array.isArray(event) ? event[0].type : event.type;
            if(window.toastr && message && type) {
                window.toastr[type](message);
            } else {
                console.warn('Toastr not available or event data missing. Message:', type, message);
                alert(message); // Fallback
            }
        });
    });
</script>
@endpush
