{{-- Email/User ID Application Form: resources/views/livewire/resource-management/email-account/application-form.blade.php --}}
<div>
    {{-- @section('title', __('Permohonan Akaun Emel / ID Pengguna MOTAC')) --}} {{-- Title typically set in Livewire component class --}}

    <div class="container py-4"> {{-- Using Bootstrap container --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                    <h2 class="h4 fw-bold text-dark">
                        {{ __('Permohonan Akaun Emel / ID Pengguna MOTAC') }}
                    </h2>
                    <span class="text-xs text-danger">{{ __('* WAJIB diisi') }}</span>
                </div>

                <x-alert-manager /> {{-- Assuming a global or Livewire-handled alert component --}}

                <form wire:submit.prevent="submitApplication">
                    {{-- Applicant Details (Read-only from user session) --}}
                    {{-- Assuming you have a Blade component for this, or implement directly --}}
                    {{-- <x-applicant-details-readonly :user="$this->user" title="{{ __('MAKLUMAT PEMOHON') }}" /> --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('MAKLUMAT PEMOHON') }}
                                <small class="d-block text-muted">{{__('(Akan diisi secara automatik jika anda log masuk)')}}</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Display pre-filled user details here --}}
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{__('Nama Penuh & Gelaran')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->title ? $this->user->title.' ' : '' }}{{ $this->user->name ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('No. Kad Pengenalan')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->identification_number ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Emel Peribadi (Untuk Login)')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->email ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Jawatan')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->position->name ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Gred')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->grade->name ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Bahagian/Unit')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->department->name ?? '' }}" readonly>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('No. Telefon Bimbit')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->mobile_number ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('BUTIRAN PERMOHONAN') }}</h5></div>
                        <div class="card-body">
                            {{-- Taraf Perkhidmatan --}}
                            <div class="mb-3">
                                <label for="service_status" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="service_status" id="service_status" class="form-select @error('service_status') is-invalid @enderror">
                                    <option value="">- {{ __('Pilih Taraf Perkhidmatan') }} -</option>
                                    {{-- System Design User Model Enums: 'tetap', 'lantikan_kontrak_mystep', 'pelajar_latihan_industri', 'other_agency_existing_mailbox', etc.
                                         MyMail Supplementary Doc uses numeric IDs.
                                         Ensure User::$SERVICE_STATUS_LABELS keys match the stored enum values and what this form expects.
                                         Example assumes User::$SERVICE_STATUS_LABELS uses the string enum keys.
                                    --}}
                                    @foreach (\App\Models\User::$SERVICE_STATUS_OPTIONS as $key => $label) {{-- Using SERVICE_STATUS_OPTIONS as defined in User Model for MOTAC design --}}
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('service_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    @if($service_status == \App\Models\User::SERVICE_STATUS_PELAJAR_INDUSTRI) {{ __('Pelajar Latihan Industri hanya akan dibekalkan ID Pengguna sahaja.') }}
                                    @elseif($service_status == \App\Models\User::SERVICE_STATUS_OTHER_AGENCY) {{ __('Penetapan e-mel sandaran MOTAC akan dilaksanakan.') }}
                                    @endif
                                </div>
                            </div>

                            {{-- Pelantikan (Appointment Type) - CRUCIAL for dynamic fields --}}
                            <div class="mb-3">
                                <label for="appointment_type" class="form-label">{{ __('Pelantikan') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="appointment_type" id="appointment_type" class="form-select @error('appointment_type') is-invalid @enderror">
                                    <option value="">- {{ __('Pilih Pelantikan') }} -</option>
                                     @foreach (\App\Models\User::$APPOINTMENT_TYPE_OPTIONS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('appointment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                             {{-- Conditional fields based on Pelantikan --}}
                            @if ($this->shouldShowPreviousDepartmentFields()) {{-- Implement this in Livewire component --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_name" class="form-label">{{__('Jabatan Terdahulu')}}</label>
                                        <input type="text" wire:model.defer="previous_department_name" id="previous_department_name" class="form-control @error('previous_department_name') is-invalid @enderror">
                                        @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_email" class="form-label">{{__('E-mel Rasmi Jabatan Terdahulu')}}</label>
                                        <input type="email" wire:model.defer="previous_department_email" id="previous_department_email" class="form-control @error('previous_department_email') is-invalid @enderror">
                                        @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif


                            {{-- Tujuan Permohonan / Catatan --}}
                            <div class="mb-3">
                                <label for="application_reason_notes" class="form-label">{{ __('Tujuan Permohonan / Catatan') }} <span class="text-danger">*</span></label>
                                <textarea wire:model.defer="application_reason_notes" id="application_reason_notes" rows="3"
                                    class="form-control @error('application_reason_notes') is-invalid @enderror"
                                    placeholder="{{ __('Nyatakan tujuan dan cadangan emel jika ada...') }}"></textarea>
                                @error('application_reason_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Cadangan Emel --}}
                            <div class="mb-3">
                                <label for="proposed_email" class="form-label">{{ __('Cadangan E-mel (Jika Ada)') }}</label>
                                <input type="email" wire:model.defer="proposed_email" id="proposed_email"
                                    class="form-control @error('proposed_email') is-invalid @enderror"
                                    placeholder="cth: nama.anda@motac.gov.my">
                                <div class="form-text">{{ __('Biarkan kosong jika tiada cadangan spesifik. Format: nama@motac.gov.my') }}</div>
                                @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Conditional Group Email Fields --}}
                            @if ($this->showGroupEmailFields()) {{-- Method in Livewire component --}}
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="mb-3 fw-semibold">{{ __('Maklumat Group E-mel / E-mel Agensi Luar') }}</h6>
                                    <div class="mb-3">
                                        <label for="group_email" class="form-label">{{ __('Nama Group E-mel / E-mel Rasmi Agensi') }} <span class="text-danger">*</span></label>
                                        <input type="text" wire:model.defer="group_email" id="group_email" class="form-control @error('group_email') is-invalid @enderror">
                                        @error('group_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_name" class="form-label">{{ __('Nama Pegawai Dihubungi (Admin/EO/CC)') }} <span class="text-danger">*</span></label>
                                        <input type="text" wire:model.defer="contact_person_name" id="contact_person_name" class="form-control @error('contact_person_name') is-invalid @enderror">
                                        @error('contact_person_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_email" class="form-label">{{ __('E-mel Pegawai Dihubungi (Rasmi MOTAC)') }} <span class="text-danger">*</span></label>
                                        <input type="email" wire:model.defer="contact_person_email" id="contact_person_email" class="form-control @error('contact_person_email') is-invalid @enderror">
                                        <div class="form-text">{{ __('Sila pastikan e-mel adalah e-mel rasmi MOTAC.') }}</div>
                                        @error('contact_person_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Conditional Service Dates --}}
                            @if ($this->shouldShowServiceDates()) {{-- Method in Livewire component --}}
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_start_date" class="form-label">{{ __('Tarikh Mula Berkhidmat (Jika Berkaitan)') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_start_date" id="service_start_date" class="form-control @error('service_start_date') is-invalid @enderror">
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="service_end_date" class="form-label">{{ __('Tarikh Akhir Berkhidmat (Jika Berkaitan)') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_end_date" id="service_end_date" class="form-control @error('service_end_date') is-invalid @enderror">
                                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Supporting Officer Details - As per MyMail form, these are required. --}}
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h5></div>
                        <div class="card-body">
                            <p class="form-text">{{ __('Permohonan hendaklah DISOKONG oleh Pegawai sekurang-kurangnya Gred 9 dan ke atas SAHAJA.')}}</p>
                            <div class="mb-3">
                                <label for="supporting_officer_name" class="form-label">{{__('Nama Penuh Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="supporting_officer_name" id="supporting_officer_name" class="form-control @error('supporting_officer_name') is-invalid @enderror" placeholder="Cth: Nur Faridah Jasni">
                                @error('supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supporting_officer_grade" class="form-label">{{__('Gred Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                    <select wire:model.defer="supporting_officer_grade_id" id="supporting_officer_grade_id" class="form-select @error('supporting_officer_grade_id') is-invalid @enderror">
                                        <option value="">- {{__('Pilih Gred')}} -</option>
                                        {{-- Populate with grades (e.g., from Grades table, filtered for >= Gred 9 level) --}}
                                        @foreach($this->supportingOfficerGradeOptions as $grade) {{-- Assuming $this->supportingOfficerGradeOptions is populated in component --}}
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supporting_officer_grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supporting_officer_email" class="form-label">{{__('Emel Rasmi Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                    <input type="email" wire:model.defer="supporting_officer_email" id="supporting_officer_email" class="form-control @error('supporting_officer_email') is-invalid @enderror" placeholder="Cth: nur.faridah@motac.gov.my">
                                     @error('supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Applicant Certification Section --}}
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('PERAKUAN PEMOHON') }}</h5></div>
                        <div class="card-body">
                            <p class="mb-3 text-sm text-muted">{{ __('Saya dengan ini mengesahkan bahawa:') }}</p>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model.defer="cert_info_is_true" id="cert_info_is_true" value="1" class="form-check-input @error('cert_info_is_true') is-invalid @enderror">
                                <label for="cert_info_is_true" class="form-check-label">
                                    {{ __('Semua maklumat yang diberikan di dalam borang ini adalah BENAR.') }} <span class="text-danger">*</span>
                                </label>
                                @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model.defer="cert_data_usage_agreed" id="cert_data_usage_agreed" value="1" class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror">
                                <label for="cert_data_usage_agreed" class="form-check-label">
                                    {{ __('Saya BERSETUJU Bahagian Pengurusan Maklumat (BPM) menggunakan maklumat yang diberikan untuk memproses permohonan ini.') }} <span class="text-danger">*</span>
                                </label>
                                @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model.defer="cert_email_responsibility_agreed" id="cert_email_responsibility_agreed" value="1" class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror">
                                <label for="cert_email_responsibility_agreed" class="form-check-label">
                                    {{ __('Saya BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel ini.') }} <span class="text-danger">*</span>
                                </label>
                                @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <p class="mt-3 text-xs text-danger fst-italic">{{ __('Sila tandakan semua kotak perakuan untuk meneruskan permohonan.') }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4 pt-3 border-top">
                        <button type="submit" wire:loading.attr="disabled" wire:target="submitApplication" class="btn btn-success px-4 py-2">
                            <span wire:loading.remove wire:target="submitApplication">
                                <i class="ti ti-send me-1"></i>
                                {{ $this->applicationToEdit ? __('Kemaskini Permohonan') : __('Hantar Permohonan') }}
                            </span>
                            <span wire:loading wire:target="submitApplication" class="d-flex align-items-center">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Memproses...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
