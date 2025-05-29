{{-- resources/views/livewire/resource-management/admin/bpm/process-issuance.blade.php --}}
<div>
    <h3 class="mb-4">{{ __('Rekod Pengeluaran Peralatan untuk Permohonan Pinjaman') }} #{{ $loanApplication->id }}</h3>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif
    @if ($errors->any())
        <x-alert type="danger" class="mb-4">
            <p class="fw-semibold">{{ __('Sila perbetulkan ralat berikut:') }}</p>
            <ul class="mt-1 list-disc ps-4"> {{-- Using ps-4 for Bootstrap-like list padding --}}
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Loan Application Details Card --}}
    <x-card card-title="{{ __('Butiran Permohonan Pinjaman') }}" class="mb-4">
        <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon') }}:</span> {{ $loanApplication->user->name ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan') }}:</span> {{ $loanApplication->purpose ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Lokasi Penggunaan') }}:</span> {{ $loanApplication->location ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Tarikh Pinjaman') }}:</span> {{ $loanApplication->loan_start_date ? $loanApplication->loan_start_date->translatedFormat(config('app.date_format_my', 'd/m/Y')) : __('N/A') }}</p>
        <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang') }}:</span> {{ $loanApplication->loan_end_date ? $loanApplication->loan_end_date->translatedFormat(config('app.date_format_my', 'd/m/Y')) : __('N/A') }}</p>

        @if ($loanApplication->applicationItems->isNotEmpty()) {{-- Changed from items to applicationItems for clarity if that's your relation name --}}
            <h6 class="mt-3 mb-2 fw-semibold">{{ __('Item Peralatan Dimohon:') }}</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small px-3 py-2">#</th>
                            <th class="small px-3 py-2">{{ __('Jenis Peralatan') }}</th>
                            <th class="small px-3 py-2">{{ __('Kuantiti Dimohon') }}</th>
                            <th class="small px-3 py-2">{{ __('Kuantiti Diluluskan') }}</th>
                            <th class="small px-3 py-2">{{ __('Catatan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanApplication->applicationItems as $item)
                        <tr>
                            <td class="small px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="small px-3 py-2">{{ $item->equipment_type ? (\App\Models\Equipment::$ASSET_TYPES_LABELS[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) : __('N/A') }}</td>
                            <td class="small px-3 py-2">{{ $item->quantity_requested ?? __('N/A') }}</td>
                            <td class="small px-3 py-2">{{ $item->quantity_approved ?? __('N/A') }}</td>
                            <td class="small px-3 py-2">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <form wire:submit.prevent="submitIssue">
        <x-card card-title="{{ __('Rekod Pengeluaran Peralatan Sebenar') }}">
            {{-- Select Equipment to Issue --}}
            <div class="mb-3">
                <label for="selectedEquipmentIds" class="form-label fw-semibold">{{ __('Pilih Peralatan untuk Dikeluarkan') }}*:</label>
                {{-- Consider using a more user-friendly multiple select component if you have one (e.g., TomSelect, Select2 via wrapper) --}}
                <select wire:model.defer="selectedEquipmentIds" id="selectedEquipmentIds" class="form-select @error('selectedEquipmentIds') is-invalid @enderror @error('selectedEquipmentIds.*') is-invalid @enderror" multiple required size="5">
                    @forelse ($availableEquipment as $equipment)
                        <option value="{{ $equipment->id }}">
                            {{ $equipment->asset_type_label }}: {{ $equipment->brand }} {{ $equipment->model }} (Tag: {{ $equipment->tag_id ?? __('N/A') }})
                        </option>
                    @empty
                        <option value="" disabled>{{ __('Tiada peralatan tersedia yang sepadan dengan jenis yang diluluskan.') }}</option>
                    @endforelse
                </select>
                @error('selectedEquipmentIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @error('selectedEquipmentIds.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Accessories Checklist --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dikeluarkan') }}:</label>
                <div class="row">
                    @foreach ($allAccessoriesList as $accessory)
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model.defer="accessories" value="{{ $accessory }}" id="accessory-{{ Str::slug($accessory) }}" class="form-check-input">
                                <label class="form-check-label" for="accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('accessories') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Issue Notes --}}
            <div class="mb-3">
                <label for="issue_notes" class="form-label fw-semibold">{{ __('Catatan Pengeluaran') }}:</label>
                <textarea wire:model.defer="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror" rows="3" placeholder="cth: Beg bercalar sedikit"></textarea>
                @error('issue_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Processed By --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Diproses Oleh') }}:</label>
                <p class="form-control-static">{{ Auth::user()->name ?? __('N/A') }}</p>
            </div>
        </x-card>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitIssue">
                <span wire:loading wire:target="submitIssue" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span wire:loading.remove wire:target="submitIssue"><i class="ti ti-check me-1"></i></span>
                {{ __('Rekod Pengeluaran Peralatan') }}
            </button>
        </div>
    </form>

     <div class="mt-4 text-center">
         <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i>
            {{ __('Kembali ke Butiran Permohonan') }}
         </a>
     </div>
</div>
