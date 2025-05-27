<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
        {{ $isEditing ? 'Edit Peralatan ICT #' . $equipmentInstance->id . ' (Tag: ' . ($equipmentInstance->tag_id ?? 'N/A') . ')' : 'Tambah Peralatan ICT Baru' }}
    </h2>

    {{-- Session messages will be handled by the layout or a dedicated alert component if flashed from redirect --}}
    {{-- Livewire flash messages can be displayed directly --}}
    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    {{-- Display all validation errors --}}
    @if ($errors->any())
        <x-alert type="danger" class="mb-4">
            <p class="font-semibold">Sila perbetulkan ralat berikut:</p>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form wire:submit.prevent="saveEquipment">
        <x-card title="Butiran Peralatan">
            {{-- Asset Type (Select) --}}
            <div class="form-group">
                <label for="asset_type" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Jenis Aset*:</label>
                <select wire:model.defer="asset_type" id="asset_type" class="form-control @error('asset_type') border-red-500 @enderror" required>
                    <option value="">- Pilih Jenis Aset -</option>
                    @foreach($assetTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('asset_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Brand --}}
            <div class="form-group">
                <label for="brand" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Jenama*:</label>
                <input type="text" wire:model.defer="brand" id="brand" class="form-control @error('brand') border-red-500 @enderror" required>
                @error('brand') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Model Name --}}
            <div class="form-group">
                <label for="model_name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Model*:</label>
                <input type="text" wire:model.defer="model_name" id="model_name" class="form-control @error('model_name') border-red-500 @enderror" required>
                @error('model_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Serial Number --}}
            <div class="form-group">
                <label for="serial_number" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nombor Siri*:</label>
                <input type="text" wire:model.defer="serial_number" id="serial_number" class="form-control @error('serial_number') border-red-500 @enderror" required>
                @error('serial_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Tag ID --}}
            <div class="form-group">
                <label for="tag_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tag ID MOTAC*:</label>
                <input type="text" wire:model.defer="tag_id" id="tag_id" class="form-control @error('tag_id') border-red-500 @enderror" required>
                @error('tag_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Purchase Date --}}
            <div class="form-group">
                <label for="purchase_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tarikh Pembelian:</label>
                <input type="date" wire:model.defer="purchase_date" id="purchase_date" class="form-control @error('purchase_date') border-red-500 @enderror">
                @error('purchase_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Warranty Expiry Date --}}
            <div class="form-group">
                <label for="warranty_expiry_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tarikh Tamat Waranti:</label>
                <input type="date" wire:model.defer="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') border-red-500 @enderror">
                @error('warranty_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Status (Select) --}}
            <div class="form-group">
                <label for="status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status Operasi*:</label>
                <select wire:model.defer="status" id="status" class="form-control @error('status') border-red-500 @enderror" required>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Condition Status (Select) --}}
            <div class="form-group">
                <label for="condition_status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status Keadaan Fizikal*:</label>
                <select wire:model.defer="condition_status" id="condition_status" class="form-control @error('condition_status') border-red-500 @enderror" required>
                     @foreach($conditionStatusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('condition_status') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Current Location --}}
            <div class="form-group">
                <label for="current_location" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Lokasi Semasa:</label>
                <input type="text" wire:model.defer="current_location" id="current_location" class="form-control @error('current_location') border-red-500 @enderror">
                @error('current_location') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label for="notes" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Catatan:</label>
                <textarea wire:model.defer="notes" id="notes" class="form-control @error('notes') border-red-500 @enderror" rows="3"></textarea>
                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </x-card>

        <div class="flex justify-center mt-6">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveEquipment">
                <span wire:loading.remove wire:target="saveEquipment">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $isEditing ? 'Kemaskini Peralatan' : 'Simpan Peralatan' }}
                </span>
                <span wire:loading wire:target="saveEquipment" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <x-back-button :route="route('resource-management.admin.equipment-admin.index')" text="Kembali ke Senarai Peralatan" />
    </div>
</div>
