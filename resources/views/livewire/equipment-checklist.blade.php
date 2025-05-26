<div>
    @if($loanApplication)
    <form wire:submit.prevent="processTransaction" class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">
                @if($isIssue)
                    {{ __('Senarai Semak Pengeluaran Peralatan') }}
                @elseif($isReturn)
                    {{ __('Senarai Semak Pemulangan Peralatan') }}
                @else
                    {{ __('Butiran Transaksi Peralatan') }}
                @endif
                - {{ __('Permohonan #') }}{{ $loanApplication->id }}
            </h4>
        </div>

        <div class="card-body">
            @if(session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Loan Application Details (Read-only) --}}
            <div class="mb-4 p-3 border rounded bg-light">
                <h6 class="text-muted">{{ __('Maklumat Permohonan Asal') }}</h6>
                <div class="row">
                    <div class="col-md-6"><p class="mb-1"><strong>{{ __('Pemohon:') }}</strong> {{ $loanApplication->user?->name }}</p></div>
                    <div class="col-md-6"><p class="mb-1"><strong>{{ __('Tujuan:') }}</strong> {{ $loanApplication->purpose }}</p></div>
                    <div class="col-md-6"><p class="mb-1"><strong>{{ __('Tarikh Pinjam:') }}</strong> {{ $loanApplication->loan_start_date?->format('d/m/Y') }}</p></div>
                    <div class="col-md-6"><p class="mb-1"><strong>{{ __('Tarikh Pulang:') }}</strong> {{ $loanApplication->loan_end_date?->format('d/m/Y') }}</p></div>
                </div>
            </div>

            {{-- Transaction Type Specific Fields --}}
            @if($isIssue)
                {{-- ISSUING EQUIPMENT --}}
                <h5 class="card-subtitle mb-3 text-primary">{{ __('MAKLUMAT PENGELUARAN') }}</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="selectedEquipmentId_issue" class="form-label">{{ __('Pilih Peralatan Untuk Dikeluarkan') }} <span class="text-danger">*</span></label>
                        <select wire:model.live="selectedEquipmentId" id="selectedEquipmentId_issue" class="form-select form-select-sm @error('selectedEquipmentId') is-invalid @enderror">
                            <option value="">-- {{ __('Pilih Peralatan') }} --</option>
                            @foreach($availableEquipmentOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedEquipmentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="officerId" class="form-label">{{ __('Pegawai Pengeluar (BPM)') }} <span class="text-danger">*</span></label>
                        <select wire:model="officerId" id="officerId" class="form-select form-select-sm @error('officerId') is-invalid @enderror">
                             @foreach($this->viewData['officerOptions'] ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('officerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="receivingOfficerId" class="form-label">{{ __('Pegawai Penerima (Pemohon)') }} <span class="text-danger">*</span></label>
                         <select wire:model="receivingOfficerId" id="receivingOfficerId" class="form-select form-select-sm @error('receivingOfficerId') is-invalid @enderror">
                            {{-- Assuming applicant is the receiver. If others, load all users. --}}
                             @if($loanApplication && $loanApplication->user)
                                <option value="{{ $loanApplication->user->id }}">{{ $loanApplication->user->name }}</option>
                             @endif
                             {{-- Add other users if applicable --}}
                        </select>
                        @error('receivingOfficerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            @elseif($isReturn)
                {{-- RETURNING EQUIPMENT --}}
                <h5 class="card-subtitle mb-3 text-primary">{{ __('MAKLUMAT PEMULANGAN') }}</h5>
                 <div class="row g-3">
                    <div class="col-md-6">
                        <label for="selectedEquipmentId_return" class="form-label">{{ __('Pilih Peralatan Untuk Dipulangkan') }} <span class="text-danger">*</span></label>
                        <select wire:model.live="selectedEquipmentId" id="selectedEquipmentId_return" class="form-select form-select-sm @error('selectedEquipmentId') is-invalid @enderror">
                            <option value="">-- {{ __('Pilih Peralatan Yang Dipinjam') }} --</option>
                            @foreach($this->viewData['onLoanEquipmentOptions'] ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedEquipmentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="equipmentConditionOnReturn" class="form-label">{{ __('Keadaan Peralatan Semasa Pemulangan') }} <span class="text-danger">*</span></label>
                        <select wire:model="equipmentConditionOnReturn" id="equipmentConditionOnReturn" class="form-select form-select-sm @error('equipmentConditionOnReturn') is-invalid @enderror">
                            <option value="">-- {{ __('Pilih Keadaan') }} --</option>
                            @foreach($this->viewData['conditionStatusOptions'] ?? [] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('equipmentConditionOnReturn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                     <div class="col-md-6">
                        <label for="returningOfficerId" class="form-label">{{ __('Pegawai Yang Memulangkan') }} <span class="text-danger">*</span></label>
                         <select wire:model="returningOfficerId" id="returningOfficerId" class="form-select form-select-sm @error('returningOfficerId') is-invalid @enderror">
                             @foreach($this->viewData['returningOfficerOptions'] ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('returningOfficerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="returnAcceptingOfficerId" class="form-label">{{ __('Pegawai Terima Pulangan (BPM)') }} <span class="text-danger">*</span></label>
                        <select wire:model="returnAcceptingOfficerId" id="returnAcceptingOfficerId" class="form-select form-select-sm @error('returnAcceptingOfficerId') is-invalid @enderror">
                            @foreach($this->viewData['officerOptions'] ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('returnAcceptingOfficerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            {{-- Selected Equipment Details (Read-only, shown after selection) --}}
            @if($equipment)
            <div class="mt-4 p-3 border rounded bg-light-info">
                <h6 class="text-muted">{{ __('Butiran Peralatan Dipilih') }}</h6>
                <p class="mb-1"><strong>{{ __('Jenis Aset:') }}</strong> {{ $equipment->asset_type_label }}</p>
                <p class="mb-1"><strong>{{ __('Jenama & Model:') }}</strong> {{ $equipment->brand }} {{ $equipment->model }}</p>
                <p class="mb-1"><strong>{{ __('No. Siri:') }}</strong> {{ $equipment->serial_number }}</p>
                <p class="mb-1"><strong>{{ __('ID Tag:') }}</strong> {{ $equipment->tag_id }}</p>
            </div>
            @endif

            {{-- Accessories Checklist --}}
            @if($equipment && ($isIssue || $isReturn))
                <div class="mt-4">
                    <h6 class="text-muted">{{ __('Senarai Semak Aksesori') }}</h6>
                    @forelse($allAccessoriesList as $accessoryKey => $accessoryLabel)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $accessoryKey }}" id="accessory_{{ $accessoryKey }}" wire:model.defer="accessories">
                            <label class="form-check-label" for="accessory_{{ $accessoryKey }}">
                                {{ $accessoryLabel }}
                            </label>
                        </div>
                    @empty
                        <p class="text-muted small">{{__('Tiada senarai aksesori asas ditetapkan.')}}</p>
                    @endforelse
                    @error('accessories') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('accessories.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            @endif

            {{-- Notes --}}
            @if($isIssue)
            <div class="mt-4">
                <label for="notes_issue" class="form-label">{{ __('Catatan Pengeluaran') }}</label>
                <textarea wire:model.defer="notes" id="notes_issue" rows="3" class="form-control form-control-sm @error('notes') is-invalid @enderror"></textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @elseif($isReturn)
            <div class="mt-4">
                <label for="returnNotes" class="form-label">{{ __('Catatan Pemulangan') }}</label>
                <textarea wire:model.defer="returnNotes" id="returnNotes" rows="3" class="form-control form-control-sm @error('returnNotes') is-invalid @enderror"></textarea>
                @error('returnNotes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif

        </div>

        @if(!$isViewingOnly)
        <div class="card-footer text-end">
            <div wire:loading wire:target="processTransaction">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Memproses...') }}
            </div>
            <button type="button" wire:click="resetForm" class="btn btn-outline-secondary me-2" wire:loading.attr="disabled">{{ __('Set Semula') }}</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                @if($isIssue)
                <i class="fas fa-sign-out-alt me-1"></i> {{ __('Keluarkan Peralatan') }}
                @elseif($isReturn)
                <i class="fas fa-undo-alt me-1"></i> {{ __('Proses Pemulangan') }}
                @endif
            </button>
        </div>
        @endif
    </form>
    @else
        <div class="alert alert-warning">{{ __('Permohonan pinjaman tidak dijumpai atau tidak dimuatkan.') }}</div>
    @endif
</div>
