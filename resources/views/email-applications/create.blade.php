@php
    // The $message variable is automatically available within @error blocks in Laravel Blade.
    // Linter warnings about unassigned $message (PHP1412) within @error blocks are false positives.
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
                                <div class="col-md-6">
                                    <label for="proposed_email" class="form-label">{{ __('Cadangan E-mel') }}</label>
                                    <input type="text" wire:model.defer="proposed_email" id="proposed_email" class="form-control @error('proposed_email') is-invalid @enderror" placeholder="nama.anda@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}">
                                    <div class="form-text">{{ __('Biarkan kosong jika hanya memohon ID Pengguna.') }}</div>
                                    @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                 <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" wire:model.live="is_group_email_request" id="is_group_email_request" class="form-check-input">
                                        <label for="is_group_email_request" class="form-check-label">{{ __('Ini adalah permohonan untuk Group E-mel?') }}</label>
                                    </div>
                                </div>

                                @if ($is_group_email_request)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-light mt-2">
                                             <h3 class="fs-6 fw-semibold mb-2">{{ __('Butiran Group E-mel') }}</h3>
                                             <p class="text-muted small mb-2">{{__('Sila lengkapkan maklumat di bawah untuk permohonan Group E-mel.')}}</p>
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
                                                    <div class="alert alert-warning mt-2 small">{{__('*Sila pastikan E-mel Admin/EO/CC Group E-mel adalah e-mel rasmi MOTAC yang sah.')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>
                                    <label for="service_status_form" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="service_status" id="service_status_form"
                                        class="form-select @error('service_status') is-invalid @enderror" required>
                                        @foreach ($serviceStatusOptions as $key => $label) {{-- Passed from Livewire component --}}
                                            <option value="{{ $key }}">{{ e($label) }}</option>
=======
                            <h2 id="application-specific-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">{{ __('Butiran Permohonan Akaun') }}</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="service_status" class="form-label">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="service_status" id="service_status" class="form-select @error('service_status') is-invalid @enderror" required>
                                        @foreach ($serviceStatusOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
>>>>>>> b3ca845 (code additions and edits)
                                        @endforeach
                                    </select>
                                    @error('service_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
<<<<<<< HEAD
                                    <label for="appointment_type_form" class="form-label">{{ __('Jenis Pelantikan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="appointment_type" id="appointment_type_form"
                                        class="form-select @error('appointment_type') is-invalid @enderror" required>
                                        @foreach ($appointmentTypeOptions as $key => $label) {{-- Passed from Livewire component --}}
                                            <option value="{{ $key }}">{{ e($label) }}</option>
=======
                                    <label for="appointment_type" class="form-label">{{ __('Jenis Pelantikan') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="appointment_type" id="appointment_type" class="form-select @error('appointment_type') is-invalid @enderror" required>
                                        @foreach ($appointmentTypeOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
>>>>>>> b3ca845 (code additions and edits)
                                        @endforeach
                                    </select>
                                    @error('appointment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if ($showPreviousDepartment)
<<<<<<< HEAD
                                    <div class="col-md-12">
                                        <div class="row g-3 p-3 border rounded bg-body-secondary mt-2">
                                            <div class="col-md-6">
                                                <label for="previous_department_name_form" class="form-label">{{ __('Jabatan Terdahulu (Jika Pertukaran)') }} <span class="text-danger">*</span></label>
                                                <input type="text" wire:model.defer="previous_department_name" id="previous_department_name_form"
                                                    class="form-control @error('previous_department_name') is-invalid @enderror" {{ $showPreviousDepartment ? 'required' : '' }}>
                                                @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="previous_department_email_form" class="form-label">{{ __('E-mel Rasmi Jabatan Terdahulu') }} <span class="text-danger">*</span></label>
                                                <input type="email" wire:model.defer="previous_department_email" id="previous_department_email_form"
                                                    class="form-control @error('previous_department_email') is-invalid @enderror" {{ $showPreviousDepartment ? 'required' : '' }}>
                                                @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
=======
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
>>>>>>> b3ca845 (code additions and edits)
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($showServiceDates)
                                    <div class="col-md-6">
<<<<<<< HEAD
                                        <label for="service_start_date_form" class="form-label">{{ __('Tarikh Mula Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_start_date" id="service_start_date_form"
                                            class="form-control @error('service_start_date') is-invalid @enderror" {{ $showServiceDates ? 'required' : '' }}>
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="service_end_date_form" class="form-label">{{ __('Tarikh Akhir Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_end_date" id="service_end_date_form"
                                            class="form-control @error('service_end_date') is-invalid @enderror" {{ $showServiceDates ? 'required' : '' }}>
=======
                                        <label for="service_start_date" class="form-label">{{ __('Tarikh Mula Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_start_date" id="service_start_date" class="form-control @error('service_start_date') is-invalid @enderror">
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="service_end_date" class="form-label">{{ __('Tarikh Akhir Khidmat') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model.defer="service_end_date" id="service_end_date" class="form-control @error('service_end_date') is-invalid @enderror">
>>>>>>> b3ca845 (code additions and edits)
                                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

<<<<<<< HEAD
                                <div class="col-md-12">
                                    <label for="application_reason_notes_form" class="form-label">{{ __('Cadangan ID E-mel / Tujuan / Catatan Tambahan') }} <span class="text-danger">*</span></label>
                                    <textarea wire:model.defer="application_reason_notes" id="application_reason_notes_form" rows="3"
                                        class="form-control @error('application_reason_notes') is-invalid @enderror" required></textarea>
                                    <div class="form-text mt-1">{{__('Jika memohon ID Pengguna sahaja, sila nyatakan tujuan. Untuk permohonan e-mel, boleh nyatakan cadangan ID di sini.')}}</div>
                                    @error('application_reason_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="proposed_email_form" class="form-label">{{ __('Cadangan E-mel Rasmi (Jika mohon E-mel Individu)') }}</label>
                                    <input type="text" wire:model.defer="proposed_email" id="proposed_email_form"
                                        class="form-control @error('proposed_email') is-invalid @enderror"
                                        placeholder="nama.anda@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}">
                                    <div class="form-text mt-1">{{ __('Biarkan kosong jika hanya memohon ID Pengguna atau untuk Group E-mel.') }}</div>
                                    @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                 <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" wire:model.live="is_group_email_request" id="is_group_email_request_form" class="form-check-input @error('is_group_email_request') is-invalid @enderror">
                                        <label for="is_group_email_request_form" class="form-check-label">{{ __('Ini adalah permohonan untuk Group E-mel?') }}</label>
                                        @error('is_group_email_request') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                @if ($is_group_email_request)
                                    <div class="col-md-12">
                                        <div class="row g-3 p-3 border rounded bg-body-secondary mt-2">
                                             <h3 class="fs-6 fw-semibold mb-2">{{ __('Butiran Group E-mel') }}</h3>
                                             <p class="text-muted small mt-0 mb-2">{{__('Sila lengkapkan maklumat di bawah untuk permohonan Group E-mel.')}}</p>
                                            <div class="col-md-6">
                                                <label for="group_email_form" class="form-label">{{ __('Alamat Group E-mel Dicadangkan') }} <span class="text-danger">*</span></label>
                                                <input type="email" wire:model.defer="group_email" id="group_email_form"
                                                    class="form-control @error('group_email') is-invalid @enderror"
                                                    placeholder="nama.kumpulan@{{ config('motac.email_provisioning.default_domain', 'motac.gov.my') }}" {{ $is_group_email_request ? 'required' : '' }}>
                                                @error('group_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="contact_person_name_form" class="form-label">{{ __('Nama Admin/EO/CC Group') }} <span class="text-danger">*</span></label>
                                                <input type="text" wire:model.defer="contact_person_name" id="contact_person_name_form"
                                                    class="form-control @error('contact_person_name') is-invalid @enderror" {{ $is_group_email_request ? 'required' : '' }}>
                                                @error('contact_person_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <label for="contact_person_email_form" class="form-label">{{ __('E-mel Rasmi Admin/EO/CC Group') }} <span class="text-danger">*</span></label>
                                                <input type="email" wire:model.defer="contact_person_email" id="contact_person_email_form"
                                                    class="form-control @error('contact_person_email') is-invalid @enderror" {{ $is_group_email_request ? 'required' : '' }}>
                                                @error('contact_person_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <div class="alert alert-warning mt-2 small py-2">{{__('*Sila pastikan E-mel Admin/EO/CC Group E-mel adalah e-mel rasmi MOTAC yang sah.')}}</div>
=======
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
>>>>>>> b3ca845 (code additions and edits)
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- Supporting Officer Details --}}
                        <section aria-labelledby="supporting-officer-heading">
<<<<<<< HEAD
                            <h2 id="supporting-officer-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2 text-dark">{{ __('Maklumat Pegawai Penyokong') }}</h2>
                            <p class="small text-muted mb-3">{{ __('Permohonan hendaklah DISOKONG oleh Pegawai sekurang-kurangnya Gred :grade atau ke atas.', ['grade' => config('motac.approval.min_email_supporting_officer_grade_level', 9)]) }}</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="supporting_officer_id_form" class="form-label">{{ __('Pilih Pegawai Penyokong (dari Sistem)') }}</label>
                                    <select wire:model.live="supporting_officer_id" id="supporting_officer_id_form"
                                        class="form-select @error('supporting_officer_id') is-invalid @enderror">
                                        <option value="">{{ __('- Sila Pilih -')}}</option>
                                        @foreach ($systemSupportingOfficers as $id => $name) {{-- Passed from Livewire Component --}}
                                            <option value="{{ $id }}">{{ e($name) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text mt-1">{{ __('Atau, isi butiran manual di bawah jika tiada dalam senarai.') }}</div>
                                    @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6"></div> {{-- Spacer --}}

                                {{-- Manual supporting officer fields, assuming Alpine.js is used for :disabled and x-show --}}
                                <div class="col-md-6">
                                    <label for="manual_supporting_officer_name_form" class="form-label">{{ __('Nama Pegawai Penyokong (Manual)') }} <span x-show="!$wire.supporting_officer_id" class="text-danger">*</span></label>
                                    <input type="text" wire:model.defer="manual_supporting_officer_name" id="manual_supporting_officer_name_form"
                                        class="form-control @error('manual_supporting_officer_name') is-invalid @enderror"
                                        :disabled="$wire.supporting_officer_id">
                                    @error('manual_supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="manual_supporting_officer_grade_key_form" class="form-label">{{ __('Gred Pegawai Penyokong (Manual)') }} <span x-show="!$wire.supporting_officer_id" class="text-danger">*</span></label>
                                    <select wire:model.defer="manual_supporting_officer_grade_key" id="manual_supporting_officer_grade_key_form"
                                        class="form-select @error('manual_supporting_officer_grade_key') is-invalid @enderror"
                                        :disabled="$wire.supporting_officer_id">
                                        <option value="">{{ __('- Sila Pilih Gred -')}}</option>
                                        @foreach ($gradeOptionsForSupportingOfficer as $key => $label) {{-- Passed from Livewire Component --}}
                                            <option value="{{ $key }}">{{ e($label) }}</option>
=======
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
>>>>>>> b3ca845 (code additions and edits)
                                        @endforeach
                                    </select>
                                    @error('manual_supporting_officer_grade_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
<<<<<<< HEAD
                                <div class="col-md-12">
                                    <label for="manual_supporting_officer_email_form" class="form-label">{{ __('E-mel Pegawai Penyokong (Manual)') }} <span x-show="!$wire.supporting_officer_id" class="text-danger">*</span></label>
                                    <input type="email" wire:model.defer="manual_supporting_officer_email" id="manual_supporting_officer_email_form"
                                        class="form-control @error('manual_supporting_officer_email') is-invalid @enderror"
                                        :disabled="$wire.supporting_officer_id">
=======
                                <div class="col-12">
                                    <label for="manual_supporting_officer_email" class="form-label">{{ __('E-mel Pegawai Penyokong (Manual)') }} <span x-show="!supporting_officer_id" class="text-danger">*</span></label>
                                    <input type="email" wire:model.defer="manual_supporting_officer_email" id="manual_supporting_officer_email" class="form-control @error('manual_supporting_officer_email') is-invalid @enderror" :disabled="supporting_officer_id">
>>>>>>> b3ca845 (code additions and edits)
                                    @error('manual_supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </section>

                        <section aria-labelledby="certification-heading" class="mt-4">
<<<<<<< HEAD
                            <h2 id="certification-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2 text-dark">{{ __('Perakuan Pemohon') }}</h2>
                            <p class="small text-muted mb-3">{{ __('Saya dengan ini mengesahkan bahawa:') }}</p>
                            <div class="vstack gap-2">
                                <div class="form-check">
                                    <input type="checkbox" wire:model.defer="cert_info_is_true" id="cert_info_is_true_form" value="1" class="form-check-input @error('cert_info_is_true') is-invalid @enderror">
                                    <label for="cert_info_is_true_form" class="form-check-label">{{ __('Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.') }} <span class="text-danger">*</span></label>
                                    @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" wire:model.defer="cert_data_usage_agreed" id="cert_data_usage_agreed_form" value="1" class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror">
                                    <label for="cert_data_usage_agreed_form" class="form-check-label">{{ __('BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.') }} <span class="text-danger">*</span></label>
                                    @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" wire:model.defer="cert_email_responsibility_agreed" id="cert_email_responsibility_agreed_form" value="1" class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror">
                                    <label for="cert_email_responsibility_agreed_form" class="form-check-label">{{ __('BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.') }} <span class="text-danger">*</span></label>
                                    @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
=======
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
>>>>>>> b3ca845 (code additions and edits)
                                </div>
                            </div>
                            <p class="form-text mt-2">{{ __('Sila tandakan pada ketiga-tiga kotak perakuan untuk meneruskan permohonan.') }}</p>
                        </section>

<<<<<<< HEAD
                        <div class="mt-4 d-flex flex-wrap justify-content-end align-items-center gap-2">
                            <a href="{{ $isEdit && isset($applicationId) ? route('email-applications.show', $applicationId) : route('email-applications.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Batal') }}
                            </a>
                            <button type="button" wire:click="saveAsDraft" class="btn btn-warning d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="saveAsDraft">
                                <span wire:loading wire:target="saveAsDraft" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                <i class="bi bi-journal-bookmark-fill me-1" wire:loading.remove></i>
                                {{ __('Simpan Draf') }}
                            </button>

                            @if ($isEdit && $application?->isDraft())
                                <button type="button" wire:click="submitForApproval" class="btn btn-success d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="submitForApproval">
                                    <span wire:loading wire:target="submitForApproval" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-send-check-fill me-1" wire:loading.remove></i>
                                    {{ __('Hantar untuk Kelulusan') }}
                                </button>
                            @elseif(!$isEdit)
                                {{-- The main form submit button for new applications --}}
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="{{ $isEdit ? 'updateApplication' : 'submitForApproval' }}">
                                    <span wire:loading wire:target="{{ $isEdit ? 'updateApplication' : 'submitForApproval' }}" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-send-check-fill me-1" wire:loading.remove></i>
                                    {{ __('Hantar Permohonan') }}
                                </button>
                            @endif

                            @if ($isEdit && !($application?->isDraft()) && auth()->user()->can('updateByAdmin', $application)) {{-- Admin can update submitted applications --}}
                                <button type="submit" class="btn btn-info d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="updateApplication"> {{-- This will trigger updateApplication --}}
                                     <span wire:loading wire:target="updateApplication" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-pencil-square me-1" wire:loading.remove></i>
                                    {{ __('Kemaskini Oleh Pentadbir') }}
                                </button>
                            @endif
                        </div>

                        <div wire:loading wire:target="saveAsDraft, submitForApproval, updateApplication, {{-- Add more targets if specific updateByAdmin has its own target --}}" class="mt-3 small text-muted d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>{{ __('Memproses...') }}</span>
=======
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
>>>>>>> b3ca845 (code additions and edits)
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
