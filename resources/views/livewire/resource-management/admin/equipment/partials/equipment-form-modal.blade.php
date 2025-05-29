{{-- resources/views/livewire/resource-management/admin/equipment/partials/equipment-form-modal.blade.php --}}
<div wire:ignore.self class="modal fade" id="equipmentFormModal" tabindex="-1" aria-labelledby="equipmentFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="{{ $editingEquipment && $editingEquipment->exists ? 'updateEquipment' : 'createEquipment' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentFormModalLabel">
                        @if ($editingEquipment && $editingEquipment->exists)
                            {{ __('Kemaskini Peralatan ICT') }}: #{{ $editingEquipment->tag_id ?? $editingEquipment->id }}
                        @else
                            {{ __('Tambah Peralatan ICT Baru') }}
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Display validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <p class="fw-semibold">{{ __('Sila perbetulkan ralat berikut:') }}</p>
                            <ul class="mt-1 mb-0 ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- Asset Type --}}
                        <div class="col-md-6">
                            <label for="modal_asset_type" class="form-label fw-medium">{{ __('Jenis Aset') }} <span class="text-danger">*</span></label>
                            <select wire:model.defer="asset_type" id="modal_asset_type" class="form-select form-select-sm @error('asset_type') is-invalid @enderror" required>
                                <option value="">-- {{ __('Pilih Jenis Aset') }} --</option>
                                @foreach ($assetTypeOptions as $value => $label) {{-- From AdminEquipmentIndexLW component --}}
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                            @error('asset_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Brand --}}
                        <div class="col-md-6">
                            <label for="modal_brand" class="form-label fw-medium">{{ __('Jenama') }}</label>
                            <input type="text" wire:model.defer="brand" id="modal_brand" class="form-control form-control-sm @error('brand') is-invalid @enderror" placeholder="cth: Dell, HP">
                            @error('brand') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Model Name --}}
                        <div class="col-md-6">
                            <label for="modal_model_name" class="form-label fw-medium">{{ __('Model') }}</label>
                            <input type="text" wire:model.defer="model_name" id="modal_model_name" class="form-control form-control-sm @error('model_name') is-invalid @enderror" placeholder="cth: Latitude 5420">
                            @error('model_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Serial Number --}}
                        <div class="col-md-6">
                            <label for="modal_serial_number" class="form-label fw-medium">{{ __('Nombor Siri') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="serial_number" id="modal_serial_number" class="form-control form-control-sm @error('serial_number') is-invalid @enderror" required placeholder="cth: CNU123XYZ">
                            @error('serial_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tag ID --}}
                        <div class="col-md-6">
                            <label for="modal_tag_id" class="form-label fw-medium">{{ __('Tag ID MOTAC') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="tag_id" id="modal_tag_id" class="form-control form-control-sm @error('tag_id') is-invalid @enderror" required placeholder="cth: MOTAC.ICT.LPT.001">
                            @error('tag_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6">
                            <label for="modal_department_id" class="form-label fw-medium">{{ __('Bahagian Pemilik/Penempatan') }}</label>
                            <select wire:model.defer="department_id" id="modal_department_id" class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                <option value="">-- {{ __('Pilih Bahagian') }} --</option>
                                @foreach ($departmentOptions as $id => $name) {{-- From AdminEquipmentIndexLW component --}}
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-2">

                        {{-- Purchase Date --}}
                        <div class="col-md-6">
                            <label for="modal_purchase_date" class="form-label fw-medium">{{ __('Tarikh Pembelian') }}</label>
                            <input type="date" wire:model.defer="purchase_date" id="modal_purchase_date" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror">
                            @error('purchase_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Warranty Expiry Date --}}
                        <div class="col-md-6">
                            <label for="modal_warranty_expiry_date" class="form-label fw-medium">{{ __('Tarikh Tamat Waranti') }}</label>
                            <input type="date" wire:model.defer="warranty_expiry_date" id="modal_warranty_expiry_date" class="form-control form-control-sm @error('warranty_expiry_date') is-invalid @enderror">
                            @error('warranty_expiry_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Status (Operational) --}}
                        <div class="col-md-6">
                            <label for="modal_status" class="form-label fw-medium">{{ __('Status Operasi') }} <span class="text-danger">*</span></label>
                            <select wire:model.defer="status" id="modal_status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                                <option value="">-- {{ __('Pilih Status Operasi') }} --</option>
                                @foreach ($statusOptions as $value => $label) {{-- From AdminEquipmentIndexLW component --}}
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Condition Status --}}
                        <div class="col-md-6">
                            <label for="modal_condition_status" class="form-label fw-medium">{{ __('Status Keadaan Fizikal') }} <span class="text-danger">*</span></label>
                            <select wire:model.defer="condition_status" id="modal_condition_status" class="form-select form-select-sm @error('condition_status') is-invalid @enderror" required>
                                <option value="">-- {{ __('Pilih Status Keadaan') }} --</option>
                                @foreach ($conditionStatusOptions as $value => $label) {{-- From AdminEquipmentIndexLW component --}}
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                            @error('condition_status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Current Location --}}
                        <div class="col-12">
                            <label for="modal_current_location" class="form-label fw-medium">{{ __('Lokasi Semasa Fizikal') }}</label>
                            <input type="text" wire:model.defer="current_location" id="modal_current_location" class="form-control form-control-sm @error('current_location') is-invalid @enderror" placeholder="cth: Aras 10, Bilik Mesyuarat Utama">
                            @error('current_location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label for="modal_notes" class="form-label fw-medium">{{ __('Nota Tambahan') }}</label>
                            <textarea wire:model.defer="notes" id="modal_notes" class="form-control form-control-sm @error('notes') is-invalid @enderror" rows="3" placeholder="Sebarang catatan tambahan..."></textarea>
                            @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeModal" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="{{ ($editingEquipment && $editingEquipment->exists) ? 'updateEquipment' : 'createEquipment' }}" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <i wire:loading.remove class="bi {{ ($editingEquipment && $editingEquipment->exists) ? 'bi-save-fill' : 'bi-plus-circle-fill' }} me-1"></i>
                        @if ($editingEquipment && $editingEquipment->exists)
                            {{ __('Kemaskini Peralatan') }}
                        @else
                            {{ __('Simpan Peralatan Baru') }}
                        @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
