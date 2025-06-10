{{-- resources/views/livewire/resource-management/admin/equipment/partials/equipment-form-modal.blade.php --}}
<div wire:ignore.self class="modal fade" id="equipmentFormModal" tabindex="-1" aria-labelledby="equipmentFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="saveEquipment">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title" id="equipmentFormModalLabel">
                        {{-- CORRECTED: Provide a default value for $isEditing --}}
                        {{ ($isEditing ?? false) ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="asset_type" class="form-label fw-medium">{{ __('Jenis Aset') }} <span class="text-danger">*</span></label>
                            <select wire:model.defer="asset_type" id="asset_type" class="form-select @error('asset_type') is-invalid @enderror" required>
                                <option value="">-- {{ __('Pilih Jenis Aset') }} --</option>
                                @foreach ($assetTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                            @error('asset_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="brand" class="form-label fw-medium">{{ __('Jenama') }}</label>
                            <input type="text" wire:model.defer="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="cth: Dell, HP">
                            @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="model_name" class="form-label fw-medium">{{ __('Model') }}</label>
                            <input type="text" wire:model.defer="model_name" id="model_name" class="form-control @error('model_name') is-invalid @enderror" placeholder="cth: Latitude 5420">
                            @error('model_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="serial_number" class="form-label fw-medium">{{ __('Nombor Siri') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required>
                            @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tag_id" class="form-label fw-medium">{{ __('Tag ID MOTAC') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required>
                            @error('tag_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="col-md-6">
                            <label for="department_id" class="form-label fw-medium">{{ __('Bahagian Pemilik') }}</label>
                            <select wire:model.defer="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">-- {{ __('Pilih Bahagian') }} --</option>
                                @foreach ($departmentOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        {{-- CORRECTED: Provide a default value for $isEditing --}}
                        <span wire:loading.remove>{{ ($isEditing ?? false) ? __('Kemaskini Peralatan') : __('Simpan Peralatan') }}</span>
                        <span wire:loading>{{ __('Menyimpan...') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
