{{-- resources/views/livewire/resource-management/admin/bpm/process-issuance.blade.php --}}
<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Rekod Pengeluaran Peralatan untuk Permohonan Pinjaman #{{ $loanApplication->id }}</h2>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
     @if ($errors->any())
         <div class="alert alert-danger mb-4">
             <p class="font-semibold">Sila perbetulkan ralat berikut:</p>
             <ul class="list-disc list-inside">
                 @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                 @endforeach
             </ul>
         </div>
     @endif


    {{-- Loan Application Details Card (similar to issue.blade.php) --}}
    <div class="card mb-6">
        <h3 class="card-title">Butiran Permohonan Pinjaman</h3>
        <p class="mb-2"><span class="font-semibold">Pemohon:</span> {{ $loanApplication->user->name ?? 'N/A' }}</p>
        <p class="mb-2"><span class="font-semibold">Tujuan Permohonan:</span> {{ $loanApplication->purpose ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Lokasi Penggunaan:</span> {{ $loanApplication->location ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Tarikh Pinjaman:</span> {{ $loanApplication->loan_start_date?->format('Y-m-d') ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Tarikh Dijangka Pulang:</span> {{ $loanApplication->loan_end_date?->format('Y-m-d') ?? 'N/A' }}</p>

        @if ($loanApplication->items->isNotEmpty())
            <h4 class="text-lg font-semibold mt-4 mb-2 text-gray-700">Item Peralatan Dimohon:</h4>
            <div class="overflow-x-auto shadow-sm rounded-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Bil.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Jenis Peralatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kuantiti Dimohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kuantiti Diluluskan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($loanApplication->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->equipment_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->quantity_requested ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->quantity_approved ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-b">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <form wire:submit.prevent="submitIssue">
        <div class="card">
            <h3 class="card-title">Rekod Pengeluaran Peralatan</h3>

            <div class="form-group">
                <label for="selectedEquipmentIds" class="block text-gray-700 text-sm font-bold mb-2">Pilih Peralatan untuk Dikeluarkan*:</label>
                <select wire:model.defer="selectedEquipmentIds" id="selectedEquipmentIds" class="form-control @error('selectedEquipmentIds') border-red-500 @enderror" multiple required>
                    @forelse ($availableEquipment as $equipment)
                        <option value="{{ $equipment->id }}">
                            {{ $equipment->brand }} {{ $equipment->model }} (Tag: {{ $equipment->tag_id ?? 'N/A' }}) - {{ $equipment->asset_type_label }}  {{-- --}}
                        </option>
                    @empty
                        <option value="" disabled>Tiada peralatan tersedia yang sepadan.</option>
                    @endforelse
                </select>
                @error('selectedEquipmentIds') <span class="text-danger">{{ $message }}</span> @enderror
                @error('selectedEquipmentIds.*') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-gray-700 text-sm font-bold mb-2">Senarai Semak Aksesori Dikeluarkan:</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($allAccessoriesList as $accessory)
                        <div class="flex items-center">
                            <input type="checkbox" wire:model.defer="accessories" value="{{ $accessory }}" id="accessory-{{ Str::slug($accessory) }}" class="form-check-input h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700" for="accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                        </div>
                    @endforeach
                </div>
                @error('accessories') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="issue_notes" class="block text-gray-700 text-sm font-bold mb-2">Catatan Pengeluaran:</label>
                <textarea wire:model.defer="issue_notes" id="issue_notes" class="form-control @error('issue_notes') border-red-500 @enderror" rows="3"></textarea>
                @error('issue_notes') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-gray-700 text-sm font-bold mb-1">Diproses Oleh:</label>
                <p class="text-gray-800">{{ Auth::user()->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="flex justify-center mt-6">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <svg wire:loading wire:target="submitIssue" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="submitIssue">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                Rekod Pengeluaran Peralatan
            </button>
        </div>
    </form>

     <div class="mt-6 text-center">
         <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApplication) }}" class="btn btn-secondary"> {{-- --}}
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
             </svg>
             Kembali ke Butiran Permohonan
         </a>
     </div>
</div>
