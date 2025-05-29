{{-- resources/views/livewire/resource-management/email-account/application-form.blade.php (Bootstrap Version) --}}
<div>
    {{-- @section('title', $isEdit ? __('Kemaskini Permohonan Emel/ID') : __('Borang Permohonan Emel/ID Pengguna MOTAC')) --}}
    {{-- Title typically set via #[Title] attribute in the Livewire component --}}

    <form wire:submit.prevent="submitApplication({{ $isEdit && (Auth::user()->isAdmin() || Auth::user()->isItAdmin()) ? 'true' : 'false' }})" class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">
                {{ $isEdit ? __('Kemaskini Permohonan Emel/ID') : __('Borang Permohonan Emel/ID Pengguna MOTAC') }}
                @if ($application && $application->exists) {{-- Assuming $application is the loaded model instance for editing --}}
                    <span class="badge bg-secondary ms-2">ID: #{{ $application->id }}</span>
                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($application->status) }} ms-1">{{ $application->status_translated }}</span>
                @endif
            </h4>
        </div>

        <div class="card-body">
            <x-alert-manager /> {{-- For session messages --}}

            {{-- Section: Maklumat Pemohon (Applicant Details) --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN A: MAKLUMAT PEMOHON') }}
                <small class="d-block text-muted fw-normal">{{ __('(Maklumat diambil dari profil pengguna yang log masuk)')}}</small>
            </h5>
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="applicant_title" class="form-label">{{ __('Gelaran') }}</label>
                    <input type="text" id="applicant_title" class="form-control form-control-sm"
                        wire:model.defer="applicant_title" readonly>
                </div>
                <div class="col-md-10">
                    <label for="applicant_name" class="form-label">{{ __('Nama Penuh') }}</label>
                    <input type="text" id="applicant_name" class="form-control form-control-sm"
                        wire:model.defer="applicant_name" readonly>
                    @error('applicant_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="applicant_identifier_input" class="form-label">
                        {{ $isPassportInputMode ? __('No. Pasport/ID Staf') : __('No. Kad Pengenalan') }}
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="{{ $isPassportInputMode ? 'text' : 'text' }}" id="applicant_identifier_input"
                            class="form-control form-control-sm"
                            wire:model.defer="{{ $isPassportInputMode ? 'applicant_passport_number' : 'applicant_identification_number' }}"
                            readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button"
                            wire:click="toggleIdentifierInput"
                            title="{{ __('Tukar ke ') }} {{ $isPassportInputMode ? __('No. Kad Pengenalan') : __('No. Pasport/ID Staf') }}">
                            <i class="ti ti-arrows-exchange"></i> {{-- Using Theme Icon --}}
                        </button>
                    </div>
                    @if ($isPassportInputMode)
                        @error('applicant_passport_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @else
                        @error('applicant_identification_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="applicant_mobile_number_form" class="form-label">{{ __('No. Telefon Bimbit') }}</label>
                    <input type="text" id="applicant_mobile_number_form" class="form-control form-control-sm"
                        wire:model.defer="applicant_mobile_number" readonly>
                    @error('applicant_mobile_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="applicant_personal_email_form" class="form-label">{{ __('E-mel Peribadi') }}</label>
                    <input type="email" id="applicant_personal_email_form" class="form-control form-control-sm"
                        wire:model.defer="applicant_personal_email" readonly>
                    @error('applicant_personal_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                @if ($showApplicantJawatanGred) {{-- This boolean is controlled by Livewire component logic --}}
                    <div class="col-md-6">
                        <label for="applicant_jawatan_gred" class="form-label">{{ __('Jawatan & Gred') }}</label>
                        <input type="text" id="applicant_jawatan_gred" class="form-control form-control-sm"
                            wire:model.defer="applicant_jawatan_gred" readonly>
                        @error('applicant_jawatan_gred') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-10"> {{-- Span more for Bahagian/Unit --}}
                        <label for="applicant_bahagian_unit" class="form-label">{{ __('MOTAC Negeri/Bahagian/Unit') }}</label>
                        <input type="text" id="applicant_bahagian_unit" class="form-control form-control-sm"
                            wire:model.defer="applicant_bahagian_unit" readonly>
                        @error('applicant_bahagian_unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="applicant_level_aras" class="form-label">{{ __('Aras') }}</label>
                        <input type="text" id="applicant_level_aras" class="form-control form-control-sm"
                            wire:model.defer="applicant_level_aras" readonly>
                        @error('applicant_level_aras') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                @endif
            </div>
            <hr class="my-4">

            {{-- Section: Maklumat Perkhidmatan & Permohonan --}}
            <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN B: MAKLUMAT PERKHIDMATAN & PERMOHONAN') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="service_status_form" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                    <select id="service_status_form" class="form-select form-select-sm @error('service_status') is-invalid @enderror" wire:model.live="service_status">
                        {{-- Populate $serviceStatusOptions from Livewire component, ensure keys match User model enums --}}
                        @foreach ($serviceStatusOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('service_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     <div class="form-text small mt-1">
                        @if($service_status == \App\Models\User::SERVICE_STATUS_PELAJAR_INDUSTRI) <i class="ti ti-info-circle text-info me-1"></i>{{ __('Pelajar Latihan Industri hanya akan dibekalkan ID Pengguna sahaja.') }}
                        @elseif($service_status == \App\Models\User::SERVICE_STATUS_OTHER_AGENCY) <i class="ti ti-info-circle text-info me-1"></i>{{ __('Penetapan e-mel sandaran MOTAC akan dilaksanakan.') }}
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="appointment_type_form" class="form-label">{{ __('Pelantikan') }} <span class="text-danger">*</span></label>
                    <select id="appointment_type_form" class="form-select form-select-sm @error('appointment_type') is-invalid @enderror" wire:model.live="appointment_type">
                         {{-- Populate $appointmentTypeOptions from Livewire component, ensure keys match User model enums --}}
                        @foreach ($appointmentTypeOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('appointment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if ($showPreviousDepartment) {{-- Controlled by Livewire component logic based on appointment_type --}}
                    <div class="col-md-6">
                        <label for="previous_department_name_form" class="form-label">{{ __('Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                        <input type="text" id="previous_department_name_form" class="form-control form-control-sm @error('previous_department_name') is-invalid @enderror" wire:model.defer="previous_department_name">
                        @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="previous_department_email_form" class="form-label">{{ __('E-mel Rasmi Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                        <input type="email" id="previous_department_email_form" class="form-control form-control-sm @error('previous_department_email') is-invalid @enderror" wire:model.defer="previous_department_email">
                        @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                @if ($showServiceDates) {{-- Controlled by Livewire component logic based on service_status --}}
                    <div class="col-md-6">
                        <label for="service_start_date_form" class="form-label">{{ __('Tarikh Mula Berkhidmat') }} <span class="text-danger">*</span></label>
                        <input type="date" id="service_start_date_form" class="form-control form-control-sm @error('service_start_date') is-invalid @enderror" wire:model.defer="service_start_date">
                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="service_end_date_form" class="form-label">{{ __('Tarikh Akhir Berkhidmat') }} <span class="text-danger">*</span></label>
                        <input type="date" id="service_end_date_form" class="form-control form-control-sm @error('service_end_date') is-invalid @enderror" wire:model.defer="service_end_date">
                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="col-12">
                    <label for="purpose_form" class="form-label">{{ __('Cadangan E-mel ID / Tujuan / Catatan') }} <span class="text-danger">*</span></label>
                    <textarea id="purpose_form" class="form-control form-control-sm @error('purpose') is-invalid @enderror" rows="3" wire:model.defer="purpose"
                        placeholder="{{ __('Contoh: cadangan.emel@motac.gov.my / Permohonan bagi Pegawai baharu bertukar masuk / Perlu akses kepada sistem XYZ') }}"></textarea>
                    @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="proposed_email_form" class="form-label">{{ __('Cadangan E-mel Rasmi Tambahan (Jika lain dari di atas)') }}</label>
                    <input type="email" id="proposed_email_form" class="form-control form-control-sm @error('proposed_email') is-invalid @enderror" wire:model.defer="proposed_email"
                        placeholder="{{ __('cth: unit.saya@motac.gov.my') }}">
                    @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="form-text text-muted">{{ __('Format: nama@') }}{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}</small>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_group_email_request_form" wire:model.live="is_group_email_request">
                        <label class="form-check-label" for="is_group_email_request_form">
                            {{ __('Permohonan untuk Group E-mel atau E-mel Sandaran Agensi Luar?') }}
                        </label>
                    </div>
                </div>

                @if ($is_group_email_request)
                    <div class="col-md-6">
                        <label for="group_email_form" class="form-label">{{ __('Alamat Group E-mel / E-mel Agensi Sedia Ada') }} <span class="text-danger">*</span></label>
                        <input type="text" id="group_email_form" class="form-control form-control-sm @error('group_email') is-invalid @enderror" wire:model.defer="group_email" placeholder="cth: group.all@motac.gov.my atau pengguna@agensi.gov.my">
                        @error('group_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="group_admin_name_form" class="form-label">{{ __('Nama Admin/EO/CC Kumpulan (Jika Group E-mel)') }}</label>
                        <input type="text" id="group_admin_name_form" class="form-control form-control-sm @error('group_admin_name') is-invalid @enderror" wire:model.defer="group_admin_name">
                        @error('group_admin_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="group_admin_email_form" class="form-label">{{ __('E-mel Admin/EO/CC Kumpulan (Jika Group E-mel)') }}</label>
                        <input type="email" id="group_admin_email_form" class="form-control form-control-sm @error('group_admin_email') is-invalid @enderror" wire:model.defer="group_admin_email">
                        @error('group_admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <label for="supporting_officer_id_form" class="form-label">{{ __('Pilih Pegawai Penyokong (dari senarai sistem)') }}</label>
                    <select id="supporting_officer_id_form" class="form-select form-select-sm @error('supporting_officer_id') is-invalid @enderror" wire:model.live="supporting_officer_id">
                        <option value="">-- {{__('Pilih Pegawai atau Isi Manual')}} --</option>
                        @foreach ($systemSupportingOfficers as $id => $name) {{-- Populate from Livewire component --}}
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="form-text text-muted">{{ __('Atau isi maklumat di bawah jika tiada dalam senarai.') }}</small>
                </div>
                <div class="col-md-6"> {{-- Spacer to balance the row if needed --}} </div>

                <div class="col-md-6">
                    <label for="manual_supporting_officer_name_form" class="form-label">{{ __('Nama Penuh Pegawai Penyokong (Manual)') }} @if(!$supporting_officer_id) <span class="text-danger">*</span>@endif</label>
                    <input type="text" id="manual_supporting_officer_name_form" class="form-control form-control-sm @error('manual_supporting_officer_name') is-invalid @enderror" wire:model.defer="manual_supporting_officer_name" @if($supporting_officer_id) readonly @endif>
                    @error('manual_supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="manual_supporting_officer_grade_key_form" class="form-label">{{ __('Gred Pegawai Penyokong (Manual)') }} @if(!$supporting_officer_id) <span class="text-danger">*</span>@endif</label>
                    <select id="manual_supporting_officer_grade_key_form" class="form-select form-select-sm @error('manual_supporting_officer_grade_key') is-invalid @enderror" wire:model.defer="manual_supporting_officer_grade_key" @if($supporting_officer_id) disabled @endif>
                         <option value="">-- {{__('Pilih Gred')}} --</option>
                        @foreach ($gradeOptionsForSupportingOfficer as $key => $value) {{-- Populate from Livewire component --}}
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('manual_supporting_officer_grade_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="manual_supporting_officer_email_form" class="form-label">{{ __('E-mel Pegawai Penyokong (Manual)') }} @if(!$supporting_officer_id) <span class="text-danger">*</span>@endif</label>
                    <input type="email" id="manual_supporting_officer_email_form" class="form-control form-control-sm @error('manual_supporting_officer_email') is-invalid @enderror" wire:model.defer="manual_supporting_officer_email" @if($supporting_officer_id) readonly @endif>
                    @error('manual_supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <hr class="my-4">

            {{-- Section: Perakuan Pemohon (Applicant Certification) --}}
            @if (!$isEdit || ($application && $application->status === \App\Models\EmailApplication::STATUS_DRAFT && !(Auth::user()->isAdmin() || Auth::user()->isItAdmin())))
                <h5 class="card-subtitle mb-3 text-muted">{{ __('BAHAGIAN D: PERAKUAN PEMOHON') }}</h5>
                <div class="form-check mb-2">
                    <input class="form-check-input @error('cert_info_is_true') is-invalid @enderror" type="checkbox" id="cert_info_is_true_form" wire:model.live="cert_info_is_true">
                    <label class="form-check-label" for="cert_info_is_true_form">
                        {{ __('Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.') }} <span class="text-danger">*</span>
                    </label>
                    @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror" type="checkbox" id="cert_data_usage_agreed_form" wire:model.live="cert_data_usage_agreed">
                    <label class="form-check-label" for="cert_data_usage_agreed_form">
                        {{ __('BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.') }} <span class="text-danger">*</span>
                    </label>
                    @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror" type="checkbox" id="cert_email_responsibility_agreed_form" wire:model.live="cert_email_responsibility_agreed">
                    <label class="form-check-label" for="cert_email_responsibility_agreed_form">
                        {{ __('BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.') }} <span class="text-danger">*</span>
                    </label>
                    @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <p class="text-muted small fst-italic">{{ __('Sila Tandakan Pada Ketiga-tiga Kotak Perakuan Untuk Meneruskan Permohonan.') }}</p>
            @elseif($isEdit && $application && $application->certification_timestamp)
                <h5 class="card-subtitle mb-3 text-muted">{{ __('PERAKUAN PEMOHON') }}</h5>
                <div class="alert alert-success py-2 small"><i class="ti ti-checks text-success me-1"></i> {{ __('Perakuan telah dibuat pada ') }}
                    {{ $application->certification_timestamp ? $application->certification_timestamp->translatedFormat(config('app.datetime_format_my')) : '' }}
                </div>
            @endif

            {{-- Admin specific fields for edit mode --}}
            @if ($isEdit && (Auth::user()->isAdmin() || Auth::user()->isItAdmin()))
                <hr class="my-4">
                <h5 class="card-subtitle mb-3 text-danger"><i class="ti ti-shield-cog me-1"></i>{{ __('BAHAGIAN E: TINDAKAN PENTADBIR IT SAHAJA') }}</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="editable_status_for_admin_key_form" class="form-label">{{ __('Status Permohonan Semasa') }}</label>
                        <select wire:model.live="editable_status_for_admin_key" id="editable_status_for_admin_key_form" class="form-select form-select-sm @error('editable_status_for_admin_key') is-invalid @enderror">
                            {{-- Populate from EmailApplication model status constants/labels --}}
                            @foreach (\App\Models\EmailApplication::$STATUSES_LABELS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('editable_status_for_admin_key') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label for="rejection_reason_form" class="form-label">{{ __('Sebab Penolakan (Jika Ditolak)') }}</label>
                        <input type="text" wire:model.defer="rejection_reason" id="rejection_reason_form" class="form-control form-control-sm @error('rejection_reason') is-invalid @enderror"
                               @if ($editable_status_for_admin_key !== \App\Models\EmailApplication::STATUS_REJECTED) disabled @endif>
                        @error('rejection_reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="final_assigned_email_form" class="form-label">{{ __('E-mel Rasmi Diluluskan') }}</label>
                        <input type="email" wire:model.defer="final_assigned_email" id="final_assigned_email_form" class="form-control form-control-sm @error('final_assigned_email') is-invalid @enderror"
                               @if (!in_array($editable_status_for_admin_key, [\App\Models\EmailApplication::STATUS_PROCESSING, \App\Models\EmailApplication::STATUS_COMPLETED, \App\Models\EmailApplication::STATUS_APPROVED ])) disabled @endif> {{-- Corrected logic for enabling --}}
                        @error('final_assigned_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="final_assigned_user_id_form" class="form-label">{{ __('ID Pengguna Diluluskan (jika ada)') }}</label>
                        <input type="text" wire:model.defer="final_assigned_user_id" id="final_assigned_user_id_form" class="form-control form-control-sm @error('final_assigned_user_id') is-invalid @enderror"
                               @if (!in_array($editable_status_for_admin_key, [\App\Models\EmailApplication::STATUS_PROCESSING, \App\Models\EmailApplication::STATUS_COMPLETED, \App\Models\EmailApplication::STATUS_APPROVED ])) disabled @endif> {{-- Corrected logic for enabling --}}
                        @error('final_assigned_user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif
        </div>

        <div class="card-footer text-end bg-light border-top py-2">
            <div wire:loading wire:target="saveAsDraft,submitForApproval,submitApplication" class="text-muted small me-2 d-inline-block">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Memproses...') }}
            </div>

            @if (!$isEdit || ($application && $application->status === \App\Models\EmailApplication::STATUS_DRAFT && !(Auth::user()->isAdmin() || Auth::user()->isItAdmin())))
                <button type="button" wire:click="saveAsDraft" class="btn btn-outline-secondary btn-sm me-2" wire:loading.attr="disabled">
                    <i class="ti ti-device-floppy me-1"></i> {{ __('Simpan Draf') }}
                </button>
                <button type="submit" {{-- submitApplication(false) is the default for this form --}} class="btn btn-primary btn-sm"
                    wire:loading.attr="disabled"
                    @if (!$cert_info_is_true || !$cert_data_usage_agreed || !$cert_email_responsibility_agreed) disabled title="{{ __('Sila lengkapkan semua perakuan untuk menghantar.') }}" @endif>
                    <i class="ti ti-send me-1"></i> {{ __('Hantar Permohonan') }}
                </button>
            @elseif($isEdit && (Auth::user()->isAdmin() || Auth::user()->isItAdmin()))
                <button type="submit" {{-- submitApplication(true) for admin updates --}} class="btn btn-success btn-sm" wire:loading.attr="disabled">
                    <i class="ti ti-checks me-1"></i> {{ __('Kemaskini Status (Admin)') }}
                </button>
             @elseif($isEdit && $application?->status === \App\Models\EmailApplication::STATUS_REJECTED && $application->user_id == Auth::id())
                <button type="submit" {{-- submitApplication(false) for resubmission --}} class="btn btn-warning btn-sm" wire:loading.attr="disabled"
                    @if (!$cert_info_is_true || !$cert_data_usage_agreed || !$cert_email_responsibility_agreed) disabled title="{{ __('Sila lengkapkan semua perakuan untuk menghantar semula.') }}" @endif>
                    <i class="ti ti-send me-1"></i> {{ __('Hantar Semula Selepas Pembetulan') }}
                </button>
            @endif
        </div>
    </form>
</div>
