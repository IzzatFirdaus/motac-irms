{{-- ICT Equipment Loan Application Form: Refactored to Bootstrap 5 --}}
<div>
    {{-- @section('title', $this->isEdit ? __('Kemaskini Permohonan Pinjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT')) --}}

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <form wire:submit.prevent="{{ $isEdit && $loanApplication && $loanApplication->status === \App\Models\LoanApplication::STATUS_DRAFT ? 'saveAsDraft' : 'submitForApproval' }}">
                    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                        <h2 class="h4 fw-bold text-dark mb-0">
                            {{ $isEdit ? __('Kemaskini Borang Permohonan Peminjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT') }}
                            @if($isEdit && $loanApplication) <span class="badge bg-secondary ms-2">ID: #{{ $loanApplication->id }}</span> @endif
                        </h2>
                        <span class="text-xs text-danger">{{ __('messages.instruction_mandatory_fields') }}</span> {{-- Assuming this lang key exists --}}
                    </div>

                    <x-alert-manager />
                    {{-- Display all validation errors using Bootstrap alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <h5 class="alert-heading">{{__('Ralat Pengesahan')}}</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header"><h5 class="mb-0">{{ __('forms.section_applicant_info_ict') }}</h5></div>
                        <div class="card-body">
                            {{-- Assuming x-applicant-details-readonly is Bootstrap compatible or replace with direct HTML --}}
                            {{-- <x-applicant-details-readonly :user="$isEdit && $loanApplication ? $loanApplication->user : Auth::user()" :title="null" /> --}}
                             <div class="row g-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{__('Nama Penuh Pemohon')}}</label>
                                    <input type="text" class="form-control form-control-sm" value="{{ ($isEdit && $loanApplication ? $loanApplication->user->name : Auth::user()->name) ?? '' }}" readonly>
                                </div>
                                {{-- Add other readonly applicant details from User model if needed --}}
                                <div class="col-md-6 mb-3">
                                    <label for="applicant_mobile_number_loan" class="form-label">{{ __('No. Telefon Pemohon (Untuk Dihubungi)') }}<span class="text-danger">*</span></label>
                                    <input type="text" id="applicant_mobile_number_loan" wire:model.defer="applicant_mobile_number"
                                           class="form-control form-control-sm @error('applicant_mobile_number') is-invalid @enderror"
                                           placeholder="{{ __('Cth: 012-3456789') }}">
                                    @error('applicant_mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6"></div> {{-- Spacer --}}

                                <div class="col-12 mb-3">
                                    <label for="purpose" class="form-label">{{ __('forms.label_application_purpose') }}<span class="text-danger">*</span></label>
                                    <textarea id="purpose" wire:model.defer="purpose" rows="3"
                                              class="form-control form-control-sm @error('purpose') is-invalid @enderror"
                                              placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                                    @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">{{ __('forms.label_location_ict') }}<span class="text-danger">*</span></label>
                                    <input type="text" id="location" wire:model.defer="location"
                                           class="form-control form-control-sm @error('location') is-invalid @enderror"
                                           placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="return_location" class="form-label">{{ __('Lokasi Pemulangan Peralatan') }}</label>
                                    <input type="text" id="return_location" wire:model.defer="return_location"
                                           class="form-control form-control-sm @error('return_location') is-invalid @enderror"
                                           placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza daripada lokasi guna)') }}">
                                    @error('return_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="loan_start_date" class="form-label">{{ __('forms.label_loan_date') }}<span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date"
                                           class="form-control form-control-sm @error('loan_start_date') is-invalid @enderror">
                                    @error('loan_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="loan_end_date" class="form-label">{{ __('forms.label_expected_return_date') }}<span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date"
                                           class="form-control form-control-sm @error('loan_end_date') is-invalid @enderror">
                                    @error('loan_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header"><h5 class="mb-0">{{ __('forms.section_responsible_officer_info') }}</h5></div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input id="isApplicantResponsible" wire:model.live="isApplicantResponsible" type="checkbox"
                                       class="form-check-input">
                                <label for="isApplicantResponsible" class="form-check-label">{{ __('forms.instruction_responsible_officer_is_applicant') }}</label>
                            </div>

                            @if(!$isApplicantResponsible)
                                <p class="text-muted small fst-italic mb-3">
                                    {{ __('forms.instruction_responsible_officer_different') }}
                                </p>
                                <div class="mb-3">
                                    <label for="responsible_officer_id" class="form-label">{{ __('Pilih Pegawai Bertanggungjawab (dari sistem)') }}</label>
                                    <select id="responsible_officer_id" wire:model.live="responsible_officer_id"
                                           class="form-select form-select-sm @error('responsible_officer_id') is-invalid @enderror"
                                           @if(!empty($manual_responsible_officer_name)) disabled @endif>
                                        <option value="">- {{__('Pilih Pegawai atau Isi Manual')}} -</option>
                                        @foreach($systemUsersForResponsibleOfficer as $id => $name) {{-- Assumed this is populated in component --}}
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('responsible_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <p class="text-sm text-center my-2 text-muted">{{ __('ATAU masukkan butiran secara manual di bawah:') }}</p>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="manual_responsible_officer_name" class="form-label">{{ __('forms.label_full_name') }}<span class="text-danger">*</span></label>
                                        <input type="text" id="manual_responsible_officer_name" wire:model.defer="manual_responsible_officer_name"
                                               @if(!empty($responsible_officer_id)) readonly @endif
                                               class="form-control form-control-sm @error('manual_responsible_officer_name') is-invalid @enderror">
                                        @error('manual_responsible_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="manual_responsible_officer_jawatan_gred" class="form-label">{{ __('forms.label_position_grade') }}<span class="text-danger">*</span></label>
                                        <input type="text" id="manual_responsible_officer_jawatan_gred" wire:model.defer="manual_responsible_officer_jawatan_gred"
                                               @if(!empty($responsible_officer_id)) readonly @endif
                                               class="form-control form-control-sm @error('manual_responsible_officer_jawatan_gred') is-invalid @enderror">
                                        @error('manual_responsible_officer_jawatan_gred') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="manual_responsible_officer_mobile" class="form-label">{{ __('forms.label_phone_number') }}<span class="text-danger">*</span></label>
                                        <input type="text" id="manual_responsible_officer_mobile" wire:model.defer="manual_responsible_officer_mobile"
                                               @if(!empty($responsible_officer_id)) readonly @endif
                                               class="form-control form-control-sm @error('manual_responsible_officer_mobile') is-invalid @enderror">
                                        @error('manual_responsible_officer_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('forms.section_equipment_details_ict') }}</h5>
                            <button type="button" wire:click="addItem" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-plus me-1"></i> {{ __('Tambah Item Peralatan') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3"> {{-- Bootstrap equivalent for spacing between items --}}
                                @forelse ($items as $index => $item)
                                    <div wire:key="loan_item_{{ $index }}" class="p-3 border rounded bg-light mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                             <h6 class="mb-0 text-muted">{{ __('Peralatan #') }}{{ $index + 1 }}</h6>
                                            @if (count($items) > 1)
                                                <button type="button" wire:click="removeItem({{ $index }})" title="{{__('Buang Item Ini')}}"
                                                        class="btn btn-sm btn-icon btn-outline-danger border-0">
                                                    <i class="ti ti-circle-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="item_{{ $index }}_equipment_type" class="form-label">{{ __('forms.table_header_equipment_type') }} <span class="text-danger">*</span></label>
                                                <select id="item_{{ $index }}_equipment_type" wire:model.defer="items.{{ $index }}.equipment_type"
                                                       class="form-select form-select-sm @error('items.'.$index.'.equipment_type') is-invalid @enderror">
                                                    <option value="">- {{__('Pilih Jenis')}} -</option>
                                                    @foreach($equipmentTypeOptions as $key => $label) {{-- From Equipment model ASSET_TYPES_LABELS --}}
                                                        <option value="{{ $key }}">{{ __($label) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('items.'.$index.'.equipment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="item_{{ $index }}_quantity_requested" class="form-label">{{ __('forms.table_header_quantity') }} <span class="text-danger">*</span></label>
                                                <input type="number" id="item_{{ $index }}_quantity_requested" wire:model.defer="items.{{ $index }}.quantity_requested" min="1"
                                                       class="form-control form-control-sm @error('items.'.$index.'.quantity_requested') is-invalid @enderror">
                                                @error('items.'.$index.'.quantity_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="item_{{ $index }}_notes" class="form-label">{{ __('forms.table_header_remarks') }}</label>
                                                <input type="text" id="item_{{ $index }}_notes" wire:model.defer="items.{{ $index }}.notes"
                                                       class="form-control form-control-sm @error('items.'.$index.'.notes') is-invalid @enderror"
                                                       placeholder="{{ __('Cth: Model spesifik, perisian khas, dll.') }}">
                                                @error('items.'.$index.'.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info text-center py-2 small">{{__('Sila tambah sekurang-kurangnya satu item peralatan.')}}</div>
                                @endforelse
                            </div>
                             @error('items') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- BAHAGIAN 4: PENGESAHAN PEMOHON --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header"><h5 class="mb-0">{{ __('forms.section_applicant_confirmation_ict') }}</h5></div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox" value="1"
                                       class="form-check-input @error('applicant_confirmation') is-invalid @enderror">
                                <label for="applicant_confirmation" class="form-check-label">
                                    {{ __('forms.text_applicant_declaration_ict') }} <span class="text-danger">*</span>
                                </label>
                                @error('applicant_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <p class="text-muted small fst-italic">
                                {{__('messages.instruction_ict_loan_check_equipment')}}
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-4 d-flex justify-content-between align-items-center">
                        <button type="button" wire:click="resetForm" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i> {{ __('app.button_reset') }}
                        </button>
                        <div>
                            <button type="button" wire:click="saveAsDraft" wire:loading.attr="disabled" wire:target="saveAsDraft,submitForApproval"
                                    class="btn btn-secondary me-2">
                                <span wire:loading.remove wire:target="saveAsDraft">
                                     <i class="ti ti-device-floppy me-1"></i> {{ __('app.button_save_draft') }}
                                </span>
                                <span wire:loading wire:target="saveAsDraft" class="d-flex align-items-center">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    {{ __('Menyimpan...') }}
                                </span>
                            </button>
                            <button type="submit" {{-- submitForApproval is the default form action --}} wire:loading.attr="disabled" wire:target="saveAsDraft,submitForApproval"
                                    class="btn btn-primary">
                                <span wire:loading.remove wire:target="submitForApproval">
                                    <i class="ti ti-send me-1"></i> {{ $this->isEdit && $this->loanApplication && $this->loanApplication->status !== \App\Models\LoanApplication::STATUS_DRAFT ? __('Kemaskini & Hantar Semula') : __('app.button_submit_application') }}
                                </span>
                                <span wire:loading wire:target="submitForApproval" class="d-flex align-items-center">
                                     <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    {{ __('Memproses...') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
