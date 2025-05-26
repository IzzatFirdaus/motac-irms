<div>
    <form wire:submit.prevent="submitApplication(false)" class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">
                {{ $isEdit ? __('Kemaskini Permohonan Emel/ID') : __('Borang Permohonan Emel/ID Pengguna MOTAC') }}
                @if ($application && $application->exists)
                    <span class="badge bg-secondary ms-2">ID: #{{ $application->id }}</span>
                    <span
                        class="badge {{ App\Helpers\Helpers::getBootstrapStatusColorClass($application->status) }} ms-1">{{ $application->status_translated }}</span>
                @endif
            </h4>
        </div>

        <div class="card-body">
            {{-- Section: Maklumat Pemohon (Applicant Details) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN A: MAKLUMAT PEMOHON') }}</h5>
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="applicant_title" class="form-label">{{ __('Gelaran') }}</label>
                    <input type="text" id="applicant_title" class="form-control form-control-sm"
                        wire:model.defer="applicant_title" readonly>
                </div>
                <div class="col-md-10">
                    <label for="applicant_name" class="form-label">{{ __('Nama Penuh') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="applicant_name" class="form-control form-control-sm"
                        wire:model.defer="applicant_name" readonly>
                    @error('applicant_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="applicant_identification_number" class="form-label">
                        {{ $isPassportInputMode ? __('No. Pasport/ID Staf') : __('No. Kad Pengenalan') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="{{ $isPassportInputMode ? 'text' : 'text' }}" id="applicant_identification_number"
                            class="form-control form-control-sm"
                            wire:model.defer="{{ $isPassportInputMode ? 'applicant_passport_number' : 'applicant_identification_number' }}"
                            readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button"
                            wire:click="toggleIdentifierInput"
                            title="{{ __('Tukar ke ') }} {{ $isPassportInputMode ? __('No. Kad Pengenalan') : __('No. Pasport/ID Staf') }}">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    </div>
                    @if ($isPassportInputMode)
                        @error('applicant_passport_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    @else
                        @error('applicant_identification_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    @endif
                </div>


                <div class="col-md-6">
                    <label for="applicant_mobile_number" class="form-label">{{ __('No. Telefon Bimbit') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="applicant_mobile_number" class="form-control form-control-sm"
                        wire:model.defer="applicant_mobile_number" readonly>
                    @error('applicant_mobile_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="applicant_personal_email" class="form-label">{{ __('E-mel Peribadi') }} <span
                            class="text-danger">*</span></label>
                    <input type="email" id="applicant_personal_email" class="form-control form-control-sm"
                        wire:model.defer="applicant_personal_email" readonly>
                    @error('applicant_personal_email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                @if ($showApplicantJawatanGred)
                    <div class="col-md-6">
                        <label for="applicant_jawatan_gred" class="form-label">{{ __('Jawatan & Gred') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="applicant_jawatan_gred" class="form-control form-control-sm"
                            wire:model.defer="applicant_jawatan_gred" readonly>
                        @error('applicant_jawatan_gred')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-10">
                        <label for="applicant_bahagian_unit" class="form-label">{{ __('MOTAC Negeri/Bahagian/Unit') }}
                            <span class="text-danger">*</span></label>
                        <input type="text" id="applicant_bahagian_unit" class="form-control form-control-sm"
                            wire:model.defer="applicant_bahagian_unit" readonly>
                        @error('applicant_bahagian_unit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="applicant_level_aras" class="form-label">{{ __('Aras') }}</label>
                        <input type="text" id="applicant_level_aras" class="form-control form-control-sm"
                            wire:model.defer="applicant_level_aras" readonly>
                        @error('applicant_level_aras')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            </div>
            <hr class="my-4">

            {{-- Section: Maklumat Perkhidmatan (Service Details) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN B: MAKLUMAT PERKHIDMATAN & PERMOHONAN') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="service_status" class="form-label">{{ __('Taraf Perkhidmatan') }} <span
                            class="text-danger">*</span></label>
                    <select id="service_status"
                        class="form-select form-select-sm @error('service_status') is-invalid @enderror"
                        wire:model.live="service_status">
                        @foreach ($serviceStatusOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('service_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="appointment_type" class="form-label">{{ __('Pelantikan') }} <span
                            class="text-danger">*</span></label>
                    <select id="appointment_type"
                        class="form-select form-select-sm @error('appointment_type') is-invalid @enderror"
                        wire:model.live="appointment_type">
                        @foreach ($appointmentTypeOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('appointment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($showPreviousDepartment)
                    <div class="col-md-6">
                        <label for="previous_department_name"
                            class="form-label">{{ __('Jabatan Terdahulu') }}</label>
                        <input type="text" id="previous_department_name"
                            class="form-control form-control-sm @error('previous_department_name') is-invalid @enderror"
                            wire:model.defer="previous_department_name">
                        @error('previous_department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="previous_department_email"
                            class="form-label">{{ __('E-mel Rasmi Jabatan Terdahulu') }}</label>
                        <input type="email" id="previous_department_email"
                            class="form-control form-control-sm @error('previous_department_email') is-invalid @enderror"
                            wire:model.defer="previous_department_email">
                        @error('previous_department_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if ($showServiceDates)
                    <div class="col-md-6">
                        <label for="service_start_date" class="form-label">{{ __('Tarikh Mula Berkhidmat') }}</label>
                        <input type="date" id="service_start_date"
                            class="form-control form-control-sm @error('service_start_date') is-invalid @enderror"
                            wire:model.defer="service_start_date">
                        @error('service_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="service_end_date" class="form-label">{{ __('Tarikh Akhir Berkhidmat') }}</label>
                        <input type="date" id="service_end_date"
                            class="form-control form-control-sm @error('service_end_date') is-invalid @enderror"
                            wire:model.defer="service_end_date">
                        @error('service_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="col-12">
                    <label for="purpose" class="form-label">{{ __('Cadangan E-mel ID / Tujuan / Catatan') }} <span
                            class="text-danger">*</span></label>
                    <textarea id="purpose" class="form-control form-control-sm @error('purpose') is-invalid @enderror" rows="3"
                        wire:model.defer="purpose"
                        placeholder="{{ __('Contoh: annis@motac.gov.my / Permohonan bagi Pegawai baharu bertukar masuk') }}"></textarea>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="proposed_email"
                        class="form-label">{{ __('Cadangan E-mel Rasmi (Jika lain dari di atas)') }}</label>
                    <input type="email" id="proposed_email"
                        class="form-control form-control-sm @error('proposed_email') is-invalid @enderror"
                        wire:model.defer="proposed_email"
                        placeholder="{{ __('biarkan kosong jika sama dengan cadangan di Tujuan/Catatan') }}">
                    @error('proposed_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small
                        class="form-text text-muted">{{ __('Format: nama@') }}{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}</small>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_group_email_request"
                            wire:model.live="is_group_email_request">
                        <label class="form-check-label" for="is_group_email_request">
                            {{ __('Permohonan untuk Group E-mel?') }}
                        </label>
                    </div>
                </div>

                @if ($is_group_email_request)
                    <div class="col-md-6">
                        <label for="group_email" class="form-label">{{ __('Alamat Group E-mel') }} <span
                                class="text-danger">*</span></label>
                        <input type="email" id="group_email"
                            class="form-control form-control-sm @error('group_email') is-invalid @enderror"
                            wire:model.defer="group_email">
                        @error('group_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="group_admin_name" class="form-label">{{ __('Nama Admin/EO/CC Kumpulan') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="group_admin_name"
                            class="form-control form-control-sm @error('group_admin_name') is-invalid @enderror"
                            wire:model.defer="group_admin_name">
                        @error('group_admin_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="group_admin_email" class="form-label">{{ __('E-mel Admin/EO/CC Kumpulan') }}
                            <span class="text-danger">*</span></label>
                        <input type="email" id="group_admin_email"
                            class="form-control form-control-sm @error('group_admin_email') is-invalid @enderror"
                            wire:model.defer="group_admin_email">
                        @error('group_admin_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            </div>
            <hr class="my-4">

            {{-- Section: Maklumat Pegawai Penyokong (Supporting Officer) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN C: MAKLUMAT PEGAWAI PENYOKONG') }}</h5>
            <p class="text-muted small">
                {{ __('Permohonan hendaklah DISOKONG oleh Pegawai sekurang-kurangnya Gred :minGrade atau ke atas SAHAJA.', ['minGrade' => config('motac.approval.min_email_supporting_officer_grade_level_numeric', 9)]) }}
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="supporting_officer_id"
                        class="form-label">{{ __('Pilih Pegawai Penyokong (Sistem)') }}</label>
                    <select id="supporting_officer_id"
                        class="form-select form-select-sm @error('supporting_officer_id') is-invalid @enderror"
                        wire:model.live="supporting_officer_id">
                        @foreach ($systemSupportingOfficers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('supporting_officer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small
                        class="form-text text-muted">{{ __('Atau isi maklumat di bawah jika tiada dalam senarai.') }}</small>
                </div>
                <div class="col-md-6"> {{-- Spacer --}} </div>

                <div class="col-md-6">
                    <label for="manual_supporting_officer_name"
                        class="form-label">{{ __('Nama Penuh Pegawai Penyokong (Manual)') }}</label>
                    <input type="text" id="manual_supporting_officer_name"
                        class="form-control form-control-sm @error('manual_supporting_officer_name') is-invalid @enderror"
                        wire:model.defer="manual_supporting_officer_name"
                        @if ($supporting_officer_id) readonly @endif>
                    @error('manual_supporting_officer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="manual_supporting_officer_grade_key"
                        class="form-label">{{ __('Gred Pegawai Penyokong (Manual)') }}</label>
                    <select id="manual_supporting_officer_grade_key"
                        class="form-select form-select-sm @error('manual_supporting_officer_grade_key') is-invalid @enderror"
                        wire:model.defer="manual_supporting_officer_grade_key"
                        @if ($supporting_officer_id) disabled @endif>
                        @foreach ($gradeOptionsForSupportingOfficer as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('manual_supporting_officer_grade_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="manual_supporting_officer_email"
                        class="form-label">{{ __('E-mel Pegawai Penyokong (Manual)') }}</label>
                    <input type="email" id="manual_supporting_officer_email"
                        class="form-control form-control-sm @error('manual_supporting_officer_email') is-invalid @enderror"
                        wire:model.defer="manual_supporting_officer_email"
                        @if ($supporting_officer_id) readonly @endif>
                    @error('manual_supporting_officer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <hr class="my-4">

            {{-- Section: Perakuan Pemohon (Applicant Certification) --}}
            @if (!$isEdit || ($application && $application->status === \App\Models\EmailApplication::STATUS_DRAFT))
                <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN D: PERAKUAN PEMOHON') }}</h5>
                <div class="form-check mb-2">
                    <input class="form-check-input @error('cert_info_is_true') is-invalid @enderror" type="checkbox"
                        id="cert_info_is_true" wire:model.live="cert_info_is_true">
                    <label class="form-check-label" for="cert_info_is_true">
                        {{ __('Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.') }} <span
                            class="text-danger">*</span>
                    </label>
                    @error('cert_info_is_true')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror"
                        type="checkbox" id="cert_data_usage_agreed" wire:model.live="cert_data_usage_agreed">
                    <label class="form-check-label" for="cert_data_usage_agreed">
                        {{ __('BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.') }}
                        <span class="text-danger">*</span>
                    </label>
                    @error('cert_data_usage_agreed')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror"
                        type="checkbox" id="cert_email_responsibility_agreed"
                        wire:model.live="cert_email_responsibility_agreed">
                    <label class="form-check-label" for="cert_email_responsibility_agreed">
                        {{ __('BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.') }}
                        <span class="text-danger">*</span>
                    </label>
                    @error('cert_email_responsibility_agreed')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <p class="text-muted small">
                    {{ __('Sila Tandakan Pada Ketiga-tiga Kotak Perakuan Untuk Meneruskan Permohonan.') }}</p>
            @else
                <h5 class="card-subtitle mb-3 text-muted">{{ __('PERAKUAN PEMOHON') }}</h5>
                <p class="text-success"><i class="fas fa-check-circle"></i> {{ __('Perakuan telah dibuat pada ') }}
                    {{ $application->certification_timestamp ? $application->certification_timestamp->format('d/m/Y H:i A') : '' }}
                </p>
            @endif

            {{-- Admin specific fields for edit mode --}}
            @if ($isEdit && (Auth::user()->isAdmin() || Auth::user()->isItAdmin()))
                <hr class="my-4">
                <h5 class="card-subtitle mb-3 text-danger">{{ __('BAHAGIAN E: TINDAKAN PENTADBIR IT SAHAJA') }}</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="editable_status_for_admin_key"
                            class="form-label">{{ __('Status Permohonan') }}</label>
                        <select wire:model.live="editable_status_for_admin_key" id="editable_status_for_admin_key"
                            class="form-select form-select-sm @error('editable_status_for_admin_key') is-invalid @enderror">
                            @foreach (\App\Models\EmailApplication::$STATUSES_LABELS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('editable_status_for_admin_key')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8">
                        <label for="rejection_reason"
                            class="form-label">{{ __('Sebab Penolakan (Jika Ditolak)') }}</label>
                        <input type="text" wire:model="rejection_reason" id="rejection_reason"
                            class="form-control form-control-sm @error('rejection_reason') is-invalid @enderror"
                            @if ($editable_status_for_admin_key !== \App\Models\EmailApplication::STATUS_REJECTED) disabled @endif>
                        @error('rejection_reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="final_assigned_email"
                            class="form-label">{{ __('E-mel Rasmi Diluluskan') }}</label>
                        <input type="email" wire:model="final_assigned_email" id="final_assigned_email"
                            class="form-control form-control-sm @error('final_assigned_email') is-invalid @enderror"
                            @if (
                                !in_array($editable_status_for_admin_key, [
                                    \App\Models\EmailApplication::STATUS_PROCESSING,
                                    \App\Models\EmailApplication::STATUS_COMPLETED,
                                ])) disabled @endif>
                        @error('final_assigned_email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="final_assigned_user_id"
                            class="form-label">{{ __('ID Pengguna Diluluskan (jika ada)') }}</label>
                        <input type="text" wire:model="final_assigned_user_id" id="final_assigned_user_id"
                            class="form-control form-control-sm @error('final_assigned_user_id') is-invalid @enderror"
                            @if (
                                !in_array($editable_status_for_admin_key, [
                                    \App\Models\EmailApplication::STATUS_PROCESSING,
                                    \App\Models\EmailApplication::STATUS_COMPLETED,
                                ])) disabled @endif>
                        @error('final_assigned_user_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

        </div>

        <div class="card-footer text-end">
            <div wire:loading wire:target="saveAsDraft,submitForApproval,submitApplication">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Memproses...') }}
            </div>

            @if (
                !$isEdit ||
                    ($application &&
                        $application->status === \App\Models\EmailApplication::STATUS_DRAFT &&
                        !(Auth::user()->isAdmin() || Auth::user()->isItAdmin())))
                <button type="button" wire:click="saveAsDraft" class="btn btn-outline-secondary me-2"
                    wire:loading.attr="disabled">
                    <i class="fas fa-save me-1"></i> {{ __('Simpan Draf') }}
                </button>
                <button type="button" wire:click="submitForApproval" class="btn btn-primary"
                    wire:loading.attr="disabled"
                    @if (!$cert_info_is_true || !$cert_data_usage_agreed || !$cert_email_responsibility_agreed) disabled title="{{ __('Sila lengkapkan semua perakuan untuk menghantar.') }}" @endif>
                    <i class="fas fa-paper-plane me-1"></i> {{ __('Hantar Permohonan') }}
                </button>
            @elseif($isEdit && (Auth::user()->isAdmin() || Auth::user()->isItAdmin()))
                <button type="button" wire:click="submitApplication(true)" class="btn btn-success"
                    wire:loading.attr="disabled"> {{-- isFinalSubmission = true for admin updates --}}
                    <i class="fas fa-check-circle me-1"></i> {{ __('Kemaskini Status (Admin)') }}
                </button>
            @endif
        </div>
    </form>
</div>
