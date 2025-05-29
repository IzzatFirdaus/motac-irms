@php
    // The $message variable is automatically available within @error blocks in Laravel Blade.
@endphp
@extends('layouts.app')

@section('title', __('Permohonan Akaun E-mel / ID Pengguna MOTAC'))

@section('content')
    <div class="container py-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body p-4 p-sm-5">
                    <h1 class="fs-3 fw-bold mb-4 text-center">
                        {{ __('Permohonan Akaun E-mel / ID Pengguna MOTAC') }}
                    </h1>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading fw-bold">{{ __('Ralat Validasi!') }}</h4>
                            <p>{{ __('Sila semak semula borang permohonan. Terdapat maklumat yang tidak sah.') }}</p>
                            <hr>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Display success or error messages from session --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif


                    <form wire:submit.prevent="{{ $isEdit ? 'updateApplication' : 'submitApplication' }}" class="vstack gap-4">
                        @csrf

                        {{-- Applicant Details Section --}}
                        <section aria-labelledby="applicant-info-heading">
                            <h2 id="applicant-info-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">
                                {{ __('Maklumat Pemohon (Dipaparkan dari Profil Anda)') }}
                            </h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ __('Nama Penuh') }}:</label>
                                    <p>{{ $applicant_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ $isPassportInputMode ? __('No. Pasport') : __('No. Kad Pengenalan') }}:</label>
                                    <p>{{ $isPassportInputMode ? $applicant_passport_number : $applicant_identification_number }}</p>
                                </div>
                                @if ($showApplicantJawatanGred)
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">{{ __('Jawatan & Gred') }}:</label>
                                        <p>{{ $applicant_jawatan_gred }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">{{ __('Bahagian/Unit') }}:</label>
                                        <p>{{ $applicant_bahagian_unit }}</p>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ __('Aras') }}:</label>
                                    <p>{{ $applicant_level_aras ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">{{ __('No. Telefon Bimbit') }}:</label>
                                    <p>{{ $applicant_mobile_number }}</p>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted">{{ __('E-mel Peribadi') }}:</label>
                                    <p>{{ $applicant_personal_email }}</p>
                                </div>
                            </div>
                        </section>

                        {{-- Application Specific Details --}}
                        <section aria-labelledby="application-specific-heading">
                            <h2 id="application-specific-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">{{ __('Butiran Permohonan Akaun') }}</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="service_status" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="service_status" id="service_status" class="form-select @error('service_status') is-invalid @enderror" required>
                                        @foreach ($serviceStatusOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="appointment_type" class="form-label">{{ __('Jenis Pelantikan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="appointment_type" id="appointment_type" class="form-select @error('appointment_type') is-invalid @enderror" required>
                                        @foreach ($appointmentTypeOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if ($showPreviousDepartment)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-light mt-2">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="previous_department_name" class="form-label">{{ __('Jabatan Terdahulu (Jika Pertukaran)') }} <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model.defer="previous_department_name" id="previous_department_name" class="form-control @error('previous_department_name') is-invalid @enderror">
                                                    @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="previous_department_email" class="form-label">{{ __('E-mel Rasmi Jabatan Terdahulu') }} <span class="text-danger">*</span></label>
                                                    <input type="email" wire:model.defer="previous_department_email" id="previous_department_email" class="form-control @error('previous_department_email') is-invalid @enderror">
                                                    @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($showServiceDates)
                                    <div class="col-md-6">
                                        <label for="service_start_date" class="form-label">{{ __('Tarikh Mula Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_start_date" id="service_start_date" class="form-control @error('service_start_date') is-invalid @enderror">
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="service_end_date" class="form-label">{{ __('Tarikh Akhir Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_end_date" id="service_end_date" class="form-control @error('service_end_date') is-invalid @enderror">
                                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

                                <div class="col-12">
                                    <label for="purpose" class="form-label">{{ __('Cadangan ID E-mel / Tujuan / Catatan') }} <span class="text-danger">*</span></label>
                                    <textarea wire:model.defer="purpose" id="purpose" rows="3" class="form-control @error('purpose') is-invalid @enderror"></textarea>
                                    @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6"> {{-- Changed from col-12 to col-md-6 for better layout with the checkbox --}}
                                    <label for="proposed_email" class="form-label">{{ __('Cadangan E-mel') }}</label>
                                    <input type="text" wire:model.defer="proposed_email" id="proposed_email" class="form-control @error('proposed_email') is-invalid @enderror" placeholder="nama.anda@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}">
                                    <div class="form-text">{{ __('Biarkan kosong jika hanya memohon ID Pengguna.') }}</div>
                                    @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                 <div class="col-md-6 d-flex align-items-end"> {{-- Aligned with the input above --}}
                                    <div class="form-check mb-2">
                                        <input type="checkbox" wire:model.live="is_group_email_request" id="is_group_email_request" class="form-check-input">
                                        <label for="is_group_email_request" class="form-check-label">{{ __('Ini adalah permohonan untuk Group E-mel?') }}</label>
                                    </div>
                                </div>


                                @if ($is_group_email_request)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-light mt-2">
                                            <h5 class="fs-6 fw-semibold mb-3">{{ __('Butiran Group E-mel (Jika Berkenaan)') }}</h5>
                                             <p class="text-muted small mb-3">{{__('Sila isi bahagian ini jika permohonan ini adalah untuk akaun Group E-mel.')}}</p>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="group_email" class="form-label">{{ __('Alamat Group E-mel Dicadangkan') }} <span class="text-danger">*</span></label>
                                                    <input type="email" wire:model.defer="group_email" id="group_email" class="form-control @error('group_email') is-invalid @enderror" placeholder="nama.kumpulan@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}">
                                                    @error('group_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="group_admin_name" class="form-label">{{ __('Nama Admin/EO/CC Group') }} <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model.defer="group_admin_name" id="group_admin_name" class="form-control @error('group_admin_name') is-invalid @enderror">
                                                    @error('group_admin_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label for="group_admin_email" class="form-label">{{ __('E-mel Admin/EO/CC Group') }} <span class="text-danger">*</span></label>
                                                    <input type="email" wire:model.defer="group_admin_email" id="group_admin_email" class="form-control @error('group_admin_email') is-invalid @enderror">
                                                    @error('group_admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                 <div class="col-12">
                                                    <div class="alert alert-warning mt-2 small">{{__('*Sila pastikan E-mel Admin/EO/CC Group E-mel adalah e-mel rasmi MOTAC.')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- Supporting Officer Details --}}
                        <section aria-labelledby="supporting-officer-heading">
                            <h2 id="supporting-officer-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">{{ __('Maklumat Pegawai Penyokong') }}</h2>
                            <p class="small text-muted mb-3">{{ __('Permohonan hendaklah DISOKONG oleh Pegawai sekurang-kurangnya Gred :grade atau ke atas.', ['grade' => config('motac.approval.min_supporting_officer_grade_level', 9)]) }}</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="supporting_officer_id" class="form-label">{{ __('Pilih Pegawai Penyokong (dari Sistem)') }}</label>
                                    <select wire:model.live="supporting_officer_id" id="supporting_officer_id" class="form-select @error('supporting_officer_id') is-invalid @enderror">
                                         <option value="">{{ __('- Sila Pilih Pegawai -') }}</option>
                                        @foreach ($systemSupportingOfficers as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('Atau, isi butiran manual di bawah jika tiada dalam senarai.') }}</div>
                                    @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6"></div> {{-- Spacer --}}
                                <div class="col-md-6">
                                    <label for="manual_supporting_officer_name" class="form-label">{{ __('Nama Pegawai Penyokong (Manual)') }} <span x-show="!supporting_officer_id" class="text-danger">*</span></label>
                                    <input type="text" wire:model.defer="manual_supporting_officer_name" id="manual_supporting_officer_name" class="form-control @error('manual_supporting_officer_name') is-invalid @enderror" :disabled="supporting_officer_id">
                                    @error('manual_supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="manual_supporting_officer_grade_key" class="form-label">{{ __('Gred Pegawai Penyokong (Manual)') }} <span x-show="!supporting_officer_id" class="text-danger">*</span></label>
                                    <select wire:model.defer="manual_supporting_officer_grade_key" id="manual_supporting_officer_grade_key" class="form-select @error('manual_supporting_officer_grade_key') is-invalid @enderror" :disabled="supporting_officer_id">
                                        <option value="">{{ __('- Pilih Gred -') }}</option>
                                        @foreach ($gradeOptionsForSupportingOfficer as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('manual_supporting_officer_grade_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label for="manual_supporting_officer_email" class="form-label">{{ __('E-mel Pegawai Penyokong (Manual)') }} <span x-show="!supporting_officer_id" class="text-danger">*</span></label>
                                    <input type="email" wire:model.defer="manual_supporting_officer_email" id="manual_supporting_officer_email" class="form-control @error('manual_supporting_officer_email') is-invalid @enderror" :disabled="supporting_officer_id">
                                    @error('manual_supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </section>

                        <section aria-labelledby="certification-heading" class="mt-4">
                            <h2 id="certification-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">{{ __('Perakuan Pemohon') }}</h2>
                            <p class="small text-muted mb-3">{{ __('Saya dengan ini mengesahkan bahawa:') }}</p>
                            <div class="vstack gap-2">
                                <div class="mb-1">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model.defer="cert_info_is_true" id="cert_info_is_true" value="1" class="form-check-input @error('cert_info_is_true') is-invalid @enderror">
                                        <label for="cert_info_is_true" class="form-check-label">{{ __('Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.') }} <span class="text-danger">*</span></label>
                                        @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model.defer="cert_data_usage_agreed" id="cert_data_usage_agreed" value="1" class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror">
                                        <label for="cert_data_usage_agreed" class="form-check-label">{{ __('BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.') }} <span class="text-danger">*</span></label>
                                        @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model.defer="cert_email_responsibility_agreed" id="cert_email_responsibility_agreed" value="1" class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror">
                                        <label for="cert_email_responsibility_agreed" class="form-check-label">{{ __('BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.') }} <span class="text-danger">*</span></label>
                                        @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <p class="form-text mt-2">{{ __('Sila tandakan pada ketiga-tiga kotak perakuan untuk meneruskan permohonan.') }}</p>
                        </section>

                        <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
                            @if ($isEdit && $application?->isDraft())
                                <button type="button" wire:click="saveAsDraft" class="btn btn-warning">{{ __('Simpan Draf') }}</button>
                                <button type="button" wire:click="submitForApproval" class="btn btn-primary">{{ __('Hantar untuk Kelulusan') }}</button>
                            @elseif(!$isEdit)
                                 {{-- Changed wire:submit.prevent on form to wire:click on specific button for submit --}}
                                <button type="button" wire:click="saveAsDraft" class="btn btn-warning">{{ __('Simpan Draf') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('Hantar Permohonan') }}</button> {{-- submitApplication will be called by form due to wire:submit --}}
                            @else
                                @can('update', $application) {{-- Assuming $application is available in edit mode --}}
                                    <button type="button" wire:click="updateApplicationByAdmin" class="btn btn-primary">{{ __('Kemaskini Permohonan (Admin)') }}</button>
                                @endcan
                            @endif
                             <a href="{{ $isEdit && isset($applicationId) ? route('resource-management.my-applications.email-applications.show', $applicationId) : route('resource-management.my-applications.email.index') }}" class="btn btn-light">{{ __('Batal') }}</a>
                        </div>

                        <div wire:loading wire:target="saveAsDraft,submitForApproval,updateApplicationByAdmin,submitApplication" class="mt-2 small text-muted">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            {{ __('Memproses...') }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
