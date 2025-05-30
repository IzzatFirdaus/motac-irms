{{-- Email/User ID Application Form: resources/views/livewire/resource-management/email-account/application-form.blade.php --}}
<div>
    {{-- Title is now set in the Livewire component's render method via ->title() --}}

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                    <h2 class="h4 fw-bold text-dark">
                        {{ $this->applicationToEdit ? __('Kemaskini Draf Permohonan Emel/ID') : __('Borang Permohonan E-mel / ID Pengguna MOTAC') }}
                    </h2>
                    <span class="text-xs text-danger">{{ __('* WAJIB diisi') }}</span>
                </div>

                {{-- Use the established general alert partial for session messages/validation errors --}}
                @include('_partials._alerts.alert-general') {{-- Make sure this partial exists and works as expected --}}

                {{-- EDITED: Changed wire:submit.prevent to "saveApplication" --}}
                <form wire:submit.prevent="saveApplication">
                    {{-- Applicant Details (Read-only from user session) --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('MAKLUMAT PEMOHON') }}
                                <small class="d-block text-muted">{{__('(Maklumat diambil dari profil pengguna anda)')}}</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{__('Nama Penuh & Gelaran')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantName }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('No. Kad Pengenalan')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->identification_number ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Emel Peribadi (Untuk Login)')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantEmail }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Jawatan & Gred')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantPositionAndGrade }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('Bahagian/Unit')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantDepartment }}" readonly>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label class="form-label">{{__('No. Telefon Bimbit')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantPhone }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('BUTIRAN PERMOHONAN') }}</h5></div>
                        <div class="card-body">
                            {{-- Taraf Perkhidmatan --}}
                            <div class="mb-3">
                                <label for="service_status_selection" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="service_status_selection" id="service_status_selection" class="form-select @error('service_status_selection') is-invalid @enderror">
                                    @foreach ($this->serviceStatusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ __($label) }}</option>
                                    @endforeach
                                </select>
                                @error('service_status_selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    @if($service_status_selection == \App\Models\User::SERVICE_STATUS_PELAJAR_INDUSTRI) {{ __('Pelajar Latihan Industri hanya akan dibekalkan ID Pengguna sahaja.') }} [cite: 174, 390]
                                    @elseif($service_status_selection == \App\Models\User::SERVICE_STATUS_OTHER_AGENCY) {{ __('Penetapan e-mel sandaran MOTAC akan dilaksanakan.') }} [cite: 175]
                                    @endif
                                </div>
                            </div>

                            {{-- Pelantikan (Appointment Type) --}}
                            <div class="mb-3">
                                <label for="appointment_type_selection" class="form-label">{{ __('Pelantikan') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="appointment_type_selection" id="appointment_type_selection" class="form-select @error('appointment_type_selection') is-invalid @enderror">
                                     @foreach ($this->appointmentTypeOptions as $key => $label)
                                        <option value="{{ $key }}">{{ __($label) }}</option>
                                    @endforeach
                                </select>
                                @error('appointment_type_selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Conditional Previous Department Fields --}}
                            @if ($this->shouldShowPreviousDepartmentFields()) {{-- Logic in Component: show if appointment_type is 'kenaikan_pangkat_pertukaran' [cite: 104, 388] --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_name" class="form-label">{{__('Jabatan Terdahulu')}} <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="previous_department_name" id="previous_department_name" class="form-control @error('previous_department_name') is-invalid @enderror">
                                        @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_email" class="form-label">{{__('E-mel Rasmi Jabatan Terdahulu')}} <span class="text-danger">*</span></label>
                                        <input type="email" wire:model="previous_department_email" id="previous_department_email" class="form-control @error('previous_department_email') is-invalid @enderror">
                                        @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="application_reason_notes" class="form-label">{{ __('Tujuan Permohonan / Catatan') }} <span class="text-danger">*</span></label>
                                <textarea wire:model="application_reason_notes" id="application_reason_notes" rows="3"
                                    class="form-control @error('application_reason_notes') is-invalid @enderror"
                                    placeholder="{{ __('Nyatakan tujuan dan cadangan emel jika ada...') }}"></textarea> {{-- [cite: 107] --}}
                                @error('application_reason_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="proposed_email" class="form-label">{{ __('Cadangan E-mel (Jika Ada)') }}</label>
                                <input type="email" wire:model="proposed_email" id="proposed_email"
                                    class="form-control @error('proposed_email') is-invalid @enderror"
                                    placeholder="cth: nama.anda@motac.gov.my"> {{-- [cite: 107] --}}
                                <div class="form-text">{{ __('Biarkan kosong jika tiada cadangan spesifik. Format: pengguna@motac.gov.my') }}</div>
                                @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Conditional Group Email Fields --}}
                            @if ($this->showGroupEmailFields()) {{-- Logic in Component --}}
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="mb-3 fw-semibold">{{ __('Maklumat Group E-mel / E-mel Agensi Luar') }}</h6>
                                    <div class="mb-3">
                                        <label for="group_email_request_name" class="form-label">{{ __('Nama Group E-mel / E-mel Rasmi Agensi') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="text" wire:model="group_email_request_name" id="group_email_request_name" class="form-control @error('group_email_request_name') is-invalid @enderror">
                                        @error('group_email_request_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_name" class="form-label">{{ __('Nama Pegawai Dihubungi (Admin/EO/CC)') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="text" wire:model="contact_person_name" id="contact_person_name" class="form-control @error('contact_person_name') is-invalid @enderror">
                                        @error('contact_person_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_email" class="form-label">{{ __('E-mel Pegawai Dihubungi (Rasmi MOTAC)') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="email" wire:model="contact_person_email" id="contact_person_email" class="form-control @error('contact_person_email') is-invalid @enderror">
                                        <div class="form-text">{{ __('Sila pastikan e-mel adalah e-mel rasmi MOTAC.') }}</div>
                                        @error('contact_person_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Conditional Service Dates --}}
                            @if ($this->shouldShowServiceDates()) {{-- Logic in Component: show if not 'tetap' (permanent) etc. [cite: 103, 387] --}}
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_start_date" class="form-label">{{ __('Tarikh Mula Berkhidmat (Jika Berkaitan)') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="service_start_date" id="service_start_date" class="form-control @error('service_start_date') is-invalid @enderror" max="{{ date('Y-m-d') }}">
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="service_end_date" class="form-label">{{ __('Tarikh Akhir Berkhidmat (Jika Berkaitan)') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="service_end_date" id="service_end_date" class="form-control @error('service_end_date') is-invalid @enderror">
                                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h5></div>
                        <div class="card-body">
                            <p class="form-text">{{ __('Permohonan hendaklah DISOKONG oleh Pegawai sekurang-kurangnya Gred 9 dan ke atas SAHAJA.')}}</p> {{-- [cite: 114, 398] --}}
                            <div class="mb-3">
                                <label for="supporting_officer_name" class="form-label">{{__('Nama Penuh Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="supporting_officer_name" id="supporting_officer_name" class="form-control @error('supporting_officer_name') is-invalid @enderror" placeholder="Cth: Nur Faridah Jasni">
                                @error('supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supporting_officer_grade" class="form-label">{{__('Gred Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                    <select wire:model="supporting_officer_grade" id="supporting_officer_grade" class="form-select @error('supporting_officer_grade') is-invalid @enderror">
                                        @foreach($this->supportingOfficerGradeOptions as $gradeKey => $gradeName)
                                            <option value="{{ $gradeKey }}">{{ $gradeName }}</option>
                                        @endforeach
                                    </select>
                                    @error('supporting_officer_grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supporting_officer_email" class="form-label">{{__('Emel Rasmi Pegawai Penyokong')}} <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="supporting_officer_email" id="supporting_officer_email" class="form-control @error('supporting_officer_email') is-invalid @enderror" placeholder="Cth: nur.faridah@motac.gov.my">
                                     @error('supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">{{ __('PERAKUAN PEMOHON') }}</h5></div> {{-- [cite: 108, 392] --}}
                        <div class="card-body">
                            <p class="mb-3 text-sm text-muted">{{ __('Saya dengan ini mengesahkan bahawa:') }}</p>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model="cert_info_is_true" id="cert_info_is_true" value="1" class="form-check-input @error('cert_info_is_true') is-invalid @enderror">
                                <label for="cert_info_is_true" class="form-check-label">
                                    {{ __('Semua maklumat yang diberikan di dalam borang ini adalah BENAR.') }} <span class="text-danger">*</span> {{-- [cite: 108, 392] --}}
                                </label>
                                @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model="cert_data_usage_agreed" id="cert_data_usage_agreed" value="1" class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror">
                                <label for="cert_data_usage_agreed" class="form-check-label">
                                    {{ __('Saya BERSETUJU Bahagian Pengurusan Maklumat (BPM) menggunakan maklumat yang diberikan untuk memproses permohonan ini.') }} <span class="text-danger">*</span> {{-- [cite: 109, 393] --}}
                                </label>
                                @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" wire:model="cert_email_responsibility_agreed" id="cert_email_responsibility_agreed" value="1" class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror">
                                <label for="cert_email_responsibility_agreed" class="form-check-label">
                                    {{ __('Saya BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel ini.') }} <span class="text-danger">*</span> {{-- [cite: 110, 394] --}}
                                </label>
                                @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <p class="mt-3 text-xs text-danger fst-italic">{{ __('Sila tandakan semua kotak perakuan untuk meneruskan permohonan.') }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4 pt-3 border-top">
                        {{-- EDITED: Button text and wire:target to "saveApplication" --}}
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveApplication" class="btn btn-success px-4 py-2">
                            <span wire:loading.remove wire:target="saveApplication">
                                <i class="ti ti-device-floppy me-1"></i> {{ $this->applicationToEdit ? __('Kemaskini Draf') : __('Simpan Draf Permohonan') }}
                            </span>
                            <span wire:loading wire:target="saveApplication" class="d-flex align-items-center">
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
