<div>
    <form wire:submit.prevent="saveApplication(false)" class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">
                {{ $isEdit ? __('Kemaskini Permohonan Pinjaman Peralatan ICT') : __('Borang Permohonan Pinjaman Peralatan ICT') }}
                @if($loanApplication && $loanApplication->exists)
                    <span class="badge bg-secondary ms-2">ID: #{{ $loanApplication->id }}</span>
                    <span class="badge {{ App\Helpers\Helpers::getBootstrapStatusColorClass($loanApplication->status) }} ms-1">
                        {{ $loanApplication->status_translated }}
                    </span>
                @endif
            </h4>
        </div>

        <div class="card-body">
            {{-- Section: Maklumat Pemohon (Applicant Details) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN 1: MAKLUMAT PEMOHON (Prefilled)') }}</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Nama Penuh') }}</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $applicant_name }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Jawatan & Gred') }}</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $applicant_jawatan_gred }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Bahagian/Unit') }}</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $applicant_bahagian_unit }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('No. Telefon') }}</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $applicant_mobile_number }}" readonly>
                </div>
            </div>
            <hr class="my-4">

            {{-- Section: Tujuan Permohonan (Loan Details) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN 1 (Samb.): TUJUAN & LOKASI PERMOHONAN') }}</h5>
            <div class="row g-3">
                <div class="col-12">
                    <label for="purpose" class="form-label">{{ __('Tujuan Permohonan') }} <span class="text-danger">*</span></label>
                    <textarea id="purpose" class="form-control form-control-sm @error('purpose') is-invalid @enderror" rows="3" wire:model.defer="purpose"></textarea>
                    @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label">{{ __('Lokasi Penggunaan Peralatan') }} <span class="text-danger">*</span></label>
                    <input type="text" id="location" class="form-control form-control-sm @error('location') is-invalid @enderror" wire:model.defer="location">
                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="return_location" class="form-label">{{ __('Lokasi Dijangka Pulang (Jika lain)') }}</label>
                    <input type="text" id="return_location" class="form-control form-control-sm @error('return_location') is-invalid @enderror" wire:model.defer="return_location">
                    @error('return_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="loan_start_date" class="form-label">{{ __('Tarikh Pinjaman') }} <span class="text-danger">*</span></label>
                    <input type="date" id="loan_start_date" class="form-control form-control-sm @error('loan_start_date') is-invalid @enderror" wire:model.defer="loan_start_date" min="{{ date('Y-m-d') }}">
                    @error('loan_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="loan_end_date" class="form-label">{{ __('Tarikh Dijangka Pulang') }} <span class="text-danger">*</span></label>
                    <input type="date" id="loan_end_date" class="form-control form-control-sm @error('loan_end_date') is-invalid @enderror" wire:model.defer="loan_end_date" min="{{ $loan_start_date ?? date('Y-m-d') }}">
                    @error('loan_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <hr class="my-4">

            {{-- Section: Pegawai Bertanggungjawab (Responsible Officer) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h5>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="isApplicantResponsible" wire:model.live="isApplicantResponsible">
                <label class="form-check-label" for="isApplicantResponsible">
                    {{ __('Pemohon adalah Pegawai Bertanggungjawab') }}
                </label>
            </div>

            @if(!$isApplicantResponsible)
            <div class="row g-3 mb-3 p-3 border rounded bg-light">
                 <p class="small text-muted">{{__('Sila pilih pegawai dari sistem atau masukkan maklumat secara manual.')}}</p>
                <div class="col-md-12">
                    <label for="responsible_officer_id" class="form-label">{{ __('Pilih Pegawai (Sistem)') }}</label>
                    <select id="responsible_officer_id" class="form-select form-select-sm @error('responsible_officer_id') is-invalid @enderror" wire:model.live="responsible_officer_id">
                        @foreach($systemUsersForResponsibleOfficer as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('responsible_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 text-center my-1"><strong>{{ __('ATAU') }}</strong></div>
                <div class="col-md-6">
                    <label for="manual_responsible_officer_name" class="form-label">{{ __('Nama Penuh (Manual)') }}</label>
                    <input type="text" id="manual_responsible_officer_name" class="form-control form-control-sm @error('manual_responsible_officer_name') is-invalid @enderror" wire:model.defer="manual_responsible_officer_name" @if($responsible_officer_id) readonly @endif>
                    @error('manual_responsible_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="manual_responsible_officer_jawatan_gred" class="form-label">{{ __('Jawatan & Gred (Manual)') }}</label>
                    <input type="text" id="manual_responsible_officer_jawatan_gred" class="form-control form-control-sm @error('manual_responsible_officer_jawatan_gred') is-invalid @enderror" wire:model.defer="manual_responsible_officer_jawatan_gred" @if($responsible_officer_id) readonly @endif>
                    @error('manual_responsible_officer_jawatan_gred') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="col-md-6">
                    <label for="manual_responsible_officer_mobile" class="form-label">{{ __('No. Telefon (Manual)') }}</label>
                    <input type="text" id="manual_responsible_officer_mobile" class="form-control form-control-sm @error('manual_responsible_officer_mobile') is-invalid @enderror" wire:model.defer="manual_responsible_officer_mobile" @if($responsible_officer_id) readonly @endif>
                    @error('manual_responsible_officer_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            @endif
            <hr class="my-4">

            {{-- Section: Maklumat Peralatan (Equipment Items) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN 3: MAKLUMAT PERALATAN') }}</h5>
            @error('items') <div class="alert alert-danger alert-sm py-1">{{ $message }}</div> @enderror

            @foreach($items as $index => $item)
            <div class="row g-3 align-items-center mb-3 p-3 border rounded {{ $loop->odd ? 'bg-light' : '' }}">
                <div class="col-md-4">
                    <label for="items_{{$index}}_equipment_type" class="form-label">{{ __('Jenis Peralatan') }} <span class="text-danger">*</span></label>
                    <select id="items_{{$index}}_equipment_type" class="form-select form-select-sm @error('items.'.$index.'.equipment_type') is-invalid @enderror" wire:model.defer="items.{{$index}}.equipment_type">
                        @foreach($equipmentTypeOptions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('items.'.$index.'.equipment_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="items_{{$index}}_quantity_requested" class="form-label">{{ __('Kuantiti') }} <span class="text-danger">*</span></label>
                    <input type="number" id="items_{{$index}}_quantity_requested" class="form-control form-control-sm @error('items.'.$index.'.quantity_requested') is-invalid @enderror" wire:model.defer="items.{{$index}}.quantity_requested" min="1">
                    @error('items.'.$index.'.quantity_requested') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-5">
                    <label for="items_{{$index}}_notes" class="form-label">{{ __('Catatan (cth: keperluan khas)') }}</label>
                    <input type="text" id="items_{{$index}}_notes" class="form-control form-control-sm @error('items.'.$index.'.notes') is-invalid @enderror" wire:model.defer="items.{{$index}}.notes">
                    @error('items.'.$index.'.notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    @if(count($items) > 1)
                    <button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{$index}})" title="{{__('Buang Item')}}">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
            <button type="button" class="btn btn-outline-success btn-sm mt-2" wire:click="addItem">
                <i class="fas fa-plus"></i> {{ __('Tambah Item Peralatan') }}
            </button>
            <hr class="my-4">

            {{-- Section: Pengesahan Pemohon (Applicant Confirmation) --}}
            @if(!$isEdit || ($loanApplication && $loanApplication->status === \App\Models\LoanApplication::STATUS_DRAFT))
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN 4: PENGESAHAN PEMOHON') }}</h5>
            <div class="form-check">
                <input class="form-check-input @error('applicant_confirmation') is-invalid @enderror" type="checkbox" id="applicant_confirmation" wire:model.live="applicant_confirmation">
                <label class="form-check-label" for="applicant_confirmation">
                    {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.') }} <span class="text-danger">*</span>
                </label>
                @error('applicant_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            @else
                <h5 class="card-subtitle mb-3 text-muted">{{ __('PENGESAHAN PEMOHON') }}</h5>
                 <p class="text-success"><i class="fas fa-check-circle"></i> {{ __('Perakuan telah dibuat pada ') }} {{ $loanApplication->applicant_confirmation_timestamp ? $loanApplication->applicant_confirmation_timestamp->format('d/m/Y H:i A') : '' }}</p>
            @endif
        </div>

        <div class="card-footer text-end">
            <div wire:loading wire:target="saveAsDraft,submitForApproval,saveApplication">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Memproses...') }}
            </div>

            @if(!$isEdit || ($loanApplication && $loanApplication->status === \App\Models\LoanApplication::STATUS_DRAFT))
                <button type="button" wire:click="saveAsDraft" class="btn btn-outline-secondary me-2" wire:loading.attr="disabled">
                    <i class="fas fa-save me-1"></i> {{ __('Simpan Draf') }}
                </button>
                <button type="button" wire:click="submitForApproval" class="btn btn-primary" wire:loading.attr="disabled"
                    @if(!$applicant_confirmation) disabled title="{{__('Sila buat pengesahan pemohon untuk menghantar.')}}" @endif>
                    <i class="fas fa-paper-plane me-1"></i> {{ __('Hantar untuk Sokongan') }}
                </button>
            @elseif($isEdit && Auth::user()->can('update', $loanApplication)) {{-- Generic update for admin if applicable --}}
                 <button type="button" wire:click="saveApplication(true)" class="btn btn-success" wire:loading.attr="disabled"> {{-- isFinalSubmission implicitly true for admin updates --}}
                    <i class="fas fa-check-circle me-1"></i> {{ __('Kemaskini Permohonan (Admin)') }}
                </button>
            @endif
        </div>
    </form>
</div>
