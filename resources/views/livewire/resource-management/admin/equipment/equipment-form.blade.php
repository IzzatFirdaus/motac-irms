{{-- resources/views/livewire/resource-management/admin/equipment/equipment-form.blade.php --}}
<div>
    <h3 class="mb-4">
        {{ $isEditing ? __('Edit Peralatan ICT') . ' #' . $equipmentInstance->id . ' (Tag: ' . ($equipmentInstance->tag_id ?? 'N/A') . ')' : __('Tambah Peralatan ICT Baru') }}
    </h3>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    @if ($errors->any())
        <x-alert type="danger" class="mb-4">
            <p class="fw-semibold">{{ __('Sila perbetulkan ralat berikut:') }}</p>
            <ul class="mt-1 list-disc ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form wire:submit.prevent="saveEquipment">
        <x-card card-title="{{ __('Butiran Peralatan') }}">
            <div class="row">
                {{-- Asset Type (Select) --}}
                <div class="col-md-6 mb-3">
                    <label for="asset_type" class="form-label fw-semibold">{{ __('Jenis Aset') }}*:</label>
                    <select wire:model.defer="asset_type" id="asset_type" class="form-select @error('asset_type') is-invalid @enderror" required>
                        <option value="">- {{ __('Pilih Jenis Aset') }} -</option>
                        @foreach($assetTypeOptions as $value => $label) {{-- Assuming $assetTypeOptions comes from component --}}
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('asset_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Brand --}}
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label fw-semibold">{{ __('Jenama') }}*:</label>
                    <input type="text" wire:model.defer="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" required placeholder="cth: Dell, HP, Acer">
                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                {{-- Model Name --}}
                <div class="col-md-6 mb-3">
                    <label for="model_name" class="form-label fw-semibold">{{ __('Model') }}*:</label>
                    <input type="text" wire:model.defer="model_name" id="model_name" class="form-control @error('model_name') is-invalid @enderror" required placeholder="cth: Latitude 5420, ProBook 440 G8">
                    @error('model_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Serial Number --}}
                <div class="col-md-6 mb-3">
                    <label for="serial_number" class="form-label fw-semibold">{{ __('Nombor Siri') }}*:</label>
                    <input type="text" wire:model.defer="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required placeholder="cth: CNU12345XYZ">
                    @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                {{-- Tag ID --}}
                <div class="col-md-6 mb-3">
                    <label for="tag_id" class="form-label fw-semibold">{{ __('Tag ID MOTAC') }}*:</label>
                    <input type="text" wire:model.defer="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required placeholder="cth: MOTAC.BPM.ICT.LPT.001">
                    @error('tag_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Item Code (New field from design document) --}}
                <div class="col-md-6 mb-3">
                    <label for="item_code" class="form-label fw-semibold">{{ __('Kod Item (Jika Ada)') }}:</label>
                    <input type="text" wire:model.defer="item_code" id="item_code" class="form-control @error('item_code') is-invalid @enderror" placeholder="cth: KEW.PA-XX">
                    @error('item_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                 {{-- Purchase Date --}}
                <div class="col-md-6 mb-3">
                    <label for="purchase_date" class="form-label fw-semibold">{{ __('Tarikh Pembelian') }}:</label>
                    <input type="date" wire:model.defer="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror">
                    @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Warranty Expiry Date --}}
                <div class="col-md-6 mb-3">
                    <label for="warranty_expiry_date" class="form-label fw-semibold">{{ __('Tarikh Tamat Waranti') }}:</label>
                    <input type="date" wire:model.defer="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror">
                    @error('warranty_expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                {{-- Status (Operasi) --}}
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label fw-semibold">{{ __('Status Operasi') }}*:</label>
                    <select wire:model.defer="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                         <option value="">- {{ __('Pilih Status Operasi') }} -</option>
                        @foreach($statusOptions as $value => $label) {{-- Assuming $statusOptions from component (Equipment model constants) --}}
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Condition Status (Fizikal) --}}
                <div class="col-md-6 mb-3">
                    <label for="condition_status" class="form-label fw-semibold">{{ __('Status Keadaan Fizikal') }}*:</label>
                    <select wire:model.defer="condition_status" id="condition_status" class="form-select @error('condition_status') is-invalid @enderror" required>
                        <option value="">- {{ __('Pilih Status Keadaan') }} -</option>
                         @foreach($conditionStatusOptions as $value => $label) {{-- Assuming $conditionStatusOptions from component (Equipment model constants) --}}
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('condition_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                 {{-- Department (Ownership/Assignment) - from Equipment model design --}}
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label fw-semibold">{{ __('Bahagian Pemilik/Penempatan') }}:</label>
                    <select wire:model.defer="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">- {{ __('Pilih Bahagian') }} -</option>
                        @foreach($departmentOptions as $id => $name) {{-- Assuming $departmentOptions from component --}}
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Current Location (Free text or linked to locations table from design) --}}
                <div class="col-md-6 mb-3">
                    <label for="current_location" class="form-label fw-semibold">{{ __('Lokasi Semasa Fizikal') }}:</label>
                    <input type="text" wire:model.defer="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror" placeholder="cth: Bilik Server, Aras 5 Blok D">
                    @error('current_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    {{-- Or if using a structured locations table:
                    <select wire:model.defer="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror">
                        <option value="">- Pilih Lokasi Berstruktur -</option>
                        @foreach($locationOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    --}}
                </div>
            </div>

            {{-- Notes --}}
            <div class="mb-3">
                <label for="notes" class="form-label fw-semibold">{{ __('Catatan Tambahan') }}:</label>
                <textarea wire:model.defer="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="cth: Spesifikasi khas, sejarah pembaikan major"></textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             {{-- Add other fields from Equipment model in Section 4.3 of design document as needed:
                 equipment_category_id, sub_category_id, purchase_price, acquisition_type, classification,
                 funded_by, supplier_name
             --}}
        </x-card>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveEquipment">
                <span wire:loading.remove wire:target="saveEquipment">
                    <i class="ti ti-{{ $isEditing ? 'device-floppy' : 'plus' }} me-1"></i>
                    {{ $isEditing ? __('Kemaskini Peralatan') : __('Simpan Peralatan Baru') }}
                </span>
                <span wire:loading wire:target="saveEquipment" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ __('Memproses...') }}
                </span>
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        {{-- Assuming 'resource-management.admin.equipment-admin.index' is the route for the equipment list page --}}
        <a href="{{ route('resource-management.admin.equipment-admin.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            {{ __('Kembali ke Senarai Peralatan') }}
        </a>
    </div>
</div>
