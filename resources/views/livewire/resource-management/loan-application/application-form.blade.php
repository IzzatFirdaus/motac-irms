<div>
    @php
        // Construct the full title including the reference number if available and editing
        $pageTitle = $this->generatePageTitle();
        if (
            $this->editing_application_id &&
            $this->loanApplicationInstance &&
            $this->loanApplicationInstance->reference_number
        ) {
            $pageTitle .= ' (' . $this->loanApplicationInstance->reference_number . ')';
        }
        $pageTitle .= ' - ' . __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
    @endphp
    @section('title', $pageTitle)

    <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
        <div class="d-flex align-items-center">
            <div class="d-flex flex-column align-items-center me-3">
                <img src="{{ asset('assets/img/logo/logo_bpm.png') }}" alt="BPM Logo"
                    style="height: 60px; width: 60px; object-fit: contain;">
                <small class="text-muted text-center mt-1">Bahagian<br>Pengurusan Maklumat</small>
            </div>
            <h1 class="h4 fw-bold text-dark mb-0">{{ $this->generatePageTitle() }}</h1>
        </div>
        <small class="text-muted">{{ __('No. Rujukan Borang: PK.(S).MOTAC.07.(L3)') }}</small>
    </div>

    @if ($isEditMode && $completedSubmissionDate)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 border rounded bg-light-subtle">
                    <span class="fw-medium">{{ __('Tarikh Permohonan Lengkap Diterima') }}:</span>
                    <span>{{ $completedSubmissionDate }}</span>
                </div>
            </div>
        </div>
    @endif

    @include('_partials._alerts.alert-general')

    <form wire:submit.prevent="submitLoanApplication" x-data="{
        termsScrolled: @entangle('termsScrolled').live,
        applicantConfirmation: @entangle('applicant_confirmation').live
    }">

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                        {{ __('BAHAGIAN 1: MAKLUMAT PEMOHON') }}
                    </h2>
                    <small class="text-muted">{{ __('* WAJIB diisi') }}</small>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Nama Penuh') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $applicantName }}</p>
                    </div>
                    <div class="col-md-6">
                        <label for="applicant_phone" class="form-label fw-medium">{{ __('No.Telefon') }}<span class="text-danger">*</span></label>
                        <input type="text" id="applicant_phone" wire:model.defer="applicant_phone" class="form-control @error('applicant_phone') is-invalid @enderror" placeholder="{{ __('Cth: 012-3456789') }}">
                        @error('applicant_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Jawatan & Gred') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $applicantPositionAndGrade }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Bahagian/Unit') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $applicantDepartment }}</p>
                    </div>
                    <div class="col-md-12">
                        <label for="purpose" class="form-label fw-medium">{{ __('Tujuan Permohonan') }}<span class="text-danger">*</span></label>
                        <textarea id="purpose" wire:model.defer="purpose" rows="3" class="form-control @error('purpose') is-invalid @enderror" placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label fw-medium">{{ __('Lokasi Penggunaan Peralatan') }}<span class="text-danger">*</span></label>
                        <input type="text" id="location" wire:model.defer="location" class="form-control @error('location') is-invalid @enderror" placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="return_location" class="form-label fw-medium">{{ __('Lokasi Dijangka Pulang') }}</label>
                        <input type="text" id="return_location" wire:model.defer="return_location" class="form-control @error('return_location') is-invalid @enderror" placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza)') }}">
                        @error('return_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_start_date" class="form-label fw-medium">{{ __('Tarikh Pinjaman') }}<span class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date" class="form-control @error('loan_start_date') is-invalid @enderror" min="{{ now()->toDateTimeLocalString('minute') }}">
                        @error('loan_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_end_date" class="form-label fw-medium">{{ __('Tarikh Dijangka Pulang') }}<span class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date" class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check mb-3">
                    <input id="applicant_is_responsible_officer" wire:model.live="applicant_is_responsible_officer" type="checkbox" class="form-check-input">
                    <label for="applicant_is_responsible_officer" class="form-check-label fw-medium">{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                </div>
                @if ($applicant_is_responsible_officer)
                    {{-- Details are inferred from the applicant --}}
                @else
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="responsible_officer_id" class="form-label fw-medium">{{ __('Nama Penuh Pegawai Bertanggungjawab') }}<span class="text-danger">*</span></label>
                            <select id="responsible_officer_id" wire:model.defer="responsible_officer_id" class="form-select @error('responsible_officer_id') is-invalid @enderror">
                                <option value="">-- {{ __('Pilih Pegawai Bertanggungjawab') }} --</option>
                                @foreach ($responsibleOfficerOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('responsible_officer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ADJUSTMENT: This section is now enabled to enforce the workflow. --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-2 fs-5"></i>
                    {{ __('MAKLUMAT PEGAWAI PENYOKONG') }} <span class="text-danger">*</span>
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="mb-3">
                    <label for="supporting_officer_id" class="form-label fw-medium">{{ __('Nama Penuh Pegawai Penyokong') }}</label>
                    <select id="supporting_officer_id" wire:model.defer="supporting_officer_id" class="form-select @error('supporting_officer_id') is-invalid @enderror">
                        <option value="">-- {{ __('Pilih Pegawai Penyokong') }} --</option>
                        @foreach ($supportingOfficerOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{__('Pegawai Penyokong mestilah sekurang-kurangnya Gred :grade atau setara.', ['grade' => config('motac.approval.min_loan_support_grade_level', 41)])}}</small>
                    @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-tools me-2 fs-5"></i>
                        {{ __('BAHAGIAN 3: MAKLUMAT PERALATAN') }} <span class="text-danger">*</span>
                    </h2>
                    <button type="button" wire:click="addLoanItem" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                @forelse ($loan_application_items as $index => $item)
                    <div wire:key="loan_item_{{ $index }}" class="list-group-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0 fw-medium">{{ __('Peralatan #') }}{{ $index + 1 }}</h3>
                            @if (count(array_filter($loan_application_items, fn($i) => empty($i['_delete']))) > 1)
                                <button type="button" wire:click="removeLoanItem({{ $index }})" title="{{ __('Buang Peralatan') }}" class="btn btn-sm btn-icon btn-text-danger p-0">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            @endif
                        </div>
                        @if (empty($item['_delete']))
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="item_{{ $index }}_equipment_type" class="form-label">{{ __('Jenis Peralatan') }}</label>
                                <select id="item_{{ $index }}_equipment_type" wire:model.defer="loan_application_items.{{ $index }}.equipment_type" class="form-select @error('loan_application_items.' . $index . '.equipment_type') is-invalid @enderror">
                                    <option value="">-- {{ __('Pilih Jenis') }} --</option>
                                    @foreach ($equipmentTypeOptions as $key => $label)
                                        <option value="{{ $key }}">{{ __($label) }}</option>
                                    @endforeach
                                </select>
                                @error('loan_application_items.' . $index . '.equipment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="item_{{ $index }}_quantity_requested" class="form-label">{{ __('Kuantiti') }}</label>
                                <input type="number" id="item_{{ $index }}_quantity_requested" wire:model.defer="loan_application_items.{{ $index }}.quantity_requested" min="1" class="form-control @error('loan_application_items.' . $index . '.quantity_requested') is-invalid @enderror">
                                @error('loan_application_items.' . $index . '.quantity_requested')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label for="item_{{ $index }}_notes" class="form-label">{{ __('Catatan') }}</label>
                                <input type="text" id="item_{{ $index }}_notes" wire:model.defer="loan_application_items.{{ $index }}.notes" class="form-control @error('loan_application_items.' . $index . '.notes') is-invalid @enderror" placeholder="{{ __('Cth: Model spesifik, perisian khas, dll.') }}">
                                @error('loan_application_items.' . $index . '.notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @else
                            <div class="alert alert-warning small p-2">{{ __('Item ini akan dipadam semasa simpan.') }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-3 border rounded bg-light-subtle">
                        <p class="text-muted mb-0">{{ __('Sila tambah sekurang-kurangnya satu item peralatan.') }}</p>
                    </div>
                @endforelse
                @error('loan_application_items')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Syarat-Syarat Permohonan --}}
        <div class="card motac-card mb-4">
            {{-- ... content of terms and conditions ... --}}
        </div>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 4: PENGESAHAN PEMOHON') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check">
                    <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox" value="1" class="form-check-input @error('applicant_confirmation') is-invalid @enderror" x-model="applicantConfirmation">
                    <label for="applicant_confirmation" class="form-check-label fw-medium">
                        {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.') }}
                        <span class="text-danger">*</span>
                    </label>
                    @error('applicant_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 pt-3">
            <button type="button" wire:click="saveAsDraft" wire:loading.attr="disabled" class="btn btn-secondary text-uppercase">
                <span wire:loading.remove wire:target="saveAsDraft"><i class="bi bi-save-fill me-1"></i> {{ __('Simpan Draf') }}</span>
                <span wire:loading wire:target="saveAsDraft">{{ __('Menyimpan...') }}</span>
            </button>
            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary text-uppercase">
                <span wire:loading.remove wire:target="submitLoanApplication"><i class="bi bi-send-check-fill me-1"></i> {{ __('Hantar Permohonan') }}</span>
                <span wire:loading wire:target="submitLoanApplication">{{ __('Memproses...') }}</span>
            </button>
        </div>
    </form>
</div>
