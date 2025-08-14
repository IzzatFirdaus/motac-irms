{{-- resources/views/email-accounts/show.blade.php --}}
{{-- This view is typically rendered by EmailAccountController@showForAdmin --}}
{{-- It displays detailed information about a specific email application for administrative review. --}}

@extends('layouts.app') {{-- Or your main admin/app layout --}}

@php
    $applicant = $emailApplication->user;
    $pageTitle = __('Butiran Permohonan E-mel/ID') . ' #' . $emailApplication->id;
@endphp

@section('title', $pageTitle)

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
            {{-- Iconography: Consistent with System Design --}}
            <i class="bi bi-file-earmark-person-fill me-2 text-primary"></i>
            {{ __('Butiran Permohonan E-mel/ID') }} #{{ $emailApplication->id }}
        </h1>
        <div>
            <a href="{{ route('resource-management.email-applications-admin.index') }}"
               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Permohonan') }}
            </a>
            {{-- Placeholder for other admin actions like editing provisioned details if applicable --}}
            {{-- @can('admin_edit_provisioned_details', $emailApplication)
                <a href="{{ route('resource-management.email-accounts.edit-provisioned', $emailApplication) }}" class="btn btn-sm btn-outline-primary ms-2">
                    <i class="bi bi-pencil-square me-1"></i> {{ __('Kemaskini Butiran Akaun') }}
                </a>
            @endcan --}}
        </div>
    </div>

    <x-alert-manager /> {{-- For displaying session messages --}}

    <div class="card shadow-sm rounded-3">
        <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center">
            <h3 class="h5 card-title fw-semibold mb-0 text-dark">
                {{ __('Maklumat Terperinci Permohonan') }}
            </h3>
            <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($emailApplication->status) }} fs-tiny px-2 py-1">{{ $emailApplication->status_translated }}</span>
        </div>

        <div class="card-body p-4">

            {{-- Section: Provisioned Account Details --}}
            @if ($emailApplication->status === \App\Models\EmailApplication::STATUS_COMPLETED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED || $emailApplication->final_assigned_email || $emailApplication->final_assigned_user_id)
            <section class="mb-4 pb-3 border-bottom">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-envelope-check-fill me-2"></i>{{ __('Maklumat Akaun Disediakan') }}</h4>
                <dl class="row g-3 small">
                    <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel Rasmi MOTAC') }}:</dt>
                    <dd class="col-sm-8 col-lg-9 fw-medium">{{ $emailApplication->final_assigned_email ?? __('Tidak Ditetapkan / Tidak Berkenaan') }}</dd>

                    <dt class="col-sm-4 col-lg-3 text-muted">{{ __('ID Pengguna Sistem') }}:</dt>
                    <dd class="col-sm-8 col-lg-9 fw-medium">{{ $emailApplication->final_assigned_user_id ?? __('Tidak Ditetapkan / Tidak Berkenaan') }}</dd>

                    @if($emailApplication->processor)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Diproses Oleh (Pentadbir IT)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->processor->name ?? 'N/A' }}</dd>
                    @endif

                    @if($emailApplication->processed_at)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Tarikh Diproses') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ App\Helpers\Helpers::formatDate($emailApplication->processed_at, 'datetime_format_my') }}</dd>
                    @endif
                </dl>
                @if($emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED && $emailApplication->rejection_reason)
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle small p-2 mt-2">
                        <strong>{{ __('Sebab Kegagalan Penyediaan') }}:</strong> {{ $emailApplication->rejection_reason }}
                    </div>
                @endif
            </section>
            @endif

            {{-- Section: Applicant Details --}}
            <section class="mb-4 pb-3 border-bottom">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>{{ __('Maklumat Pemohon') }}</h4>
                @if($applicant)
                    <dl class="row g-3 small">
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Nama Penuh') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $applicant->title ? $applicant->title.' ' : '' }}{{ $applicant->name }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Kad Pengenalan') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $applicant->identification_number ?? __('N/A') }}</dd>

                        @if($applicant->passport_number)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Pasport') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $applicant->passport_number }}</dd>
                        @endif

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jawatan') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ optional($applicant->position)->name ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Gred') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ optional($applicant->grade)->name ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jabatan/Unit') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ optional($applicant->department)->name ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel Peribadi (Login)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $applicant->email }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Telefon Bimbit') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $applicant->mobile_number ?? __('N/A') }}</dd>

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Taraf Perkhidmatan (Semasa Permohonan)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ App\Models\User::$SERVICE_STATUS_LABELS[$emailApplication->service_status] ?? $emailApplication->service_status ?? __('N/A') }}</dd> {{-- Citing User::$SERVICE_STATUS_LABELS from model context --}}

                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jenis Pelantikan (Semasa Permohonan)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ App\Models\User::$APPOINTMENT_TYPE_LABELS[$emailApplication->appointment_type] ?? $emailApplication->appointment_type ?? __('N/A') }}</dd> {{-- Citing User::$APPOINTMENT_TYPE_LABELS from model context --}}
                    </dl>
                @else
                    <p class="text-muted fst-italic small">{{ __('Maklumat pemohon tidak tersedia.') }}</p>
                @endif
            </section>

            {{-- Section: Original Application Specifics --}}
            <section class="mb-4 pb-3 border-bottom">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-file-earmark-richtext-fill me-2"></i>{{ __('Butiran Permohonan Asal') }}</h4>
                <dl class="row g-3 small">
                    @if($emailApplication->previous_department_name)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jabatan Terdahulu (Jika Berkaitan)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->previous_department_name }}</dd>
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel Jabatan Terdahulu') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->previous_department_email }}</dd>
                    @endif

                    @if($emailApplication->service_start_date)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Tarikh Mula Khidmat (Jika Berkaitan)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ App\Helpers\Helpers::formatDate($emailApplication->service_start_date, 'date_format_my') }}</dd>
                    @endif
                    @if($emailApplication->service_end_date)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Tarikh Akhir Khidmat (Jika Berkaitan)') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ App\Helpers\Helpers::formatDate($emailApplication->service_end_date, 'date_format_my') }}</dd>
                    @endif

                    <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Tujuan / Catatan Permohonan') }}:</dt>
                    <dd class="col-sm-8 col-lg-9" style="white-space: pre-wrap;">{{ $emailApplication->application_reason_notes ?? __('Tiada') }}</dd>

                    @if($emailApplication->proposed_email)
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Cadangan E-mel / ID Pemohon') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->proposed_email }}</dd>
                    @endif

                    @if($emailApplication->group_email)
                        <dt class="col-sm-12 text-muted mt-2 fw-medium">{{ __('Maklumat Permohonan Group E-mel:') }}</dt>
                        <dt class="col-sm-4 col-lg-3 offset-sm-1 offset-lg-0 text-muted">{{ __('Nama Group E-mel Dicadang') }}:</dt>
                        <dd class="col-sm-7 col-lg-9">{{ $emailApplication->group_email }}</dd>
                        <dt class="col-sm-4 col-lg-3 offset-sm-1 offset-lg-0 text-muted">{{ __('Nama Pentadbir Group (EO/CC)') }}:</dt>
                        <dd class="col-sm-7 col-lg-9">{{ $emailApplication->contact_person_name ?? __('N/A') }}</dd>
                        <dt class="col-sm-4 col-lg-3 offset-sm-1 offset-lg-0 text-muted">{{ __('E-mel Pentadbir Group (EO/CC)') }}:</dt>
                        <dd class="col-sm-7 col-lg-9">{{ $emailApplication->contact_person_email ?? __('N/A') }}</dd>
                    @endif

                    <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Tarikh Permohonan Dihantar') }}:</dt>
                    <dd class="col-sm-8 col-lg-9">{{ App\Helpers\Helpers::formatDate($emailApplication->submitted_at ?? $emailApplication->created_at, 'datetime_format_my') }}</dd>
                </dl>
            </section>

            {{-- Section: Applicant Certification --}}
            <section class="mb-4 pb-3 border-bottom">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-patch-check-fill me-2"></i>{{ __('Perakuan Pemohon') }}</h4>
                <ul class="list-unstyled small mb-1">
                    <li class="mb-1 d-flex align-items-center">
                        @if($emailApplication->cert_info_is_true) <i class="bi bi-check-square-fill text-success me-2 fs-5"></i> @else <i class="bi bi-square text-muted me-2 fs-5"></i> @endif
                        <span>{{ __('Semua maklumat yang dinyatakan di dalam permohonan ini adalah BENAR.') }}</span>
                    </li>
                    <li class="mb-1 d-flex align-items-center">
                        @if($emailApplication->cert_data_usage_agreed) <i class="bi bi-check-square-fill text-success me-2 fs-5"></i> @else <i class="bi bi-square text-muted me-2 fs-5"></i> @endif
                        <span>{{ __('BERSETUJU maklumat yang dinyatakan di dalam permohonan ini diguna pakai oleh Bahagian Pengurusan Maklumat untuk tujuan memproses permohonan saya.') }}</span>
                    </li>
                    <li class="d-flex align-items-center">
                        @if($emailApplication->cert_email_responsibility_agreed) <i class="bi bi-check-square-fill text-success me-2 fs-5"></i> @else <i class="bi bi-square text-muted me-2 fs-5"></i> @endif
                        <span>{{ __('BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel saya.') }}</span>
                    </li>
                </ul>
                @if($emailApplication->certification_timestamp)
                    <p class="small text-muted fst-italic mt-1">
                        {{ __('Disahkan pada') }}: {{ App\Helpers\Helpers::formatDate($emailApplication->certification_timestamp, 'datetime_format_my') }}
                    </p>
                @elseif($emailApplication->isDraft())
                     <p class="small text-warning fst-italic mt-1">{{ __('Perakuan belum disahkan (draf).') }}</p>
                @else
                     <p class="small text-danger fst-italic mt-1">{{ __('Perakuan tidak disahkan sepenuhnya.') }}</p>
                @endif
            </section>

            {{-- Section: Supporting Officer Details (Original Application) --}}
            <section class="mb-4 pb-3 border-bottom">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-person-video3 me-2"></i>{{ __('Maklumat Pegawai Penyokong (Semasa Permohonan Dihantar)') }}</h4>
                @if($emailApplication->supporting_officer_id && $emailApplication->supportingOfficer)
                    {{-- Supporting officer was a system user --}}
                    <dl class="row g-3 small">
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Nama') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->supportingOfficer->name }}</dd>
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Gred') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ optional($emailApplication->supportingOfficer->grade)->name ?? __('N/A') }}</dd>
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->supportingOfficer->email }}</dd>
                    </dl>
                @elseif($emailApplication->supporting_officer_name)
                    {{-- Supporting officer details were entered manually --}}
                    <dl class="row g-3 small">
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Nama') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->supporting_officer_name }}</dd>
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Gred') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->supporting_officer_grade ?? __('N/A') }}</dd>
                        <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel') }}:</dt>
                        <dd class="col-sm-8 col-lg-9">{{ $emailApplication->supporting_officer_email ?? __('N/A') }}</dd>
                    </dl>
                @else
                    <p class="text-muted fst-italic small">{{ __('Tiada maklumat pegawai penyokong direkodkan.') }}</p>
                @endif
            </section>

            {{-- Section: Approval History --}}
            <section class="mb-4">
                <h4 class="h6 fw-semibold text-primary mb-3"><i class="bi bi-signpost-split-fill me-2"></i>{{ __('Sejarah Kelulusan & Tindakan') }}</h4>
                @if ($emailApplication->approvals()->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($emailApplication->approvals()->orderBy('created_at')->get() as $approvalStage)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 small fw-bold">
                                        {{ App\Models\Approval::getStageDisplayName($approvalStage->stage) }}
                                    </h6>
                                    <small class="text-muted">{{ App\Helpers\Helpers::formatDate($approvalStage->approval_timestamp ?? $approvalStage->updated_at, 'datetime_format_my_short') }}</small>
                                </div>
                                <p class="mb-1 small">
                                    <strong class="fw-semibold">{{ __('Pegawai Bertindak') }}:</strong> {{ optional($approvalStage->officer)->name ?? __('Sistem') }}
                                </p>
                                <p class="mb-1 small">
                                    <strong class="fw-semibold">{{ __('Keputusan') }}:</strong>
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass('approval_'.$approvalStage->status) }} fs-tiny px-2 py-1">{{ __(Str::title(str_replace('_', ' ', $approvalStage->status))) }}</span>
                                </p>
                                @if ($approvalStage->comments)
                                    <div class="mt-2 p-2 bg-light border rounded small text-dark" style="white-space: pre-wrap;">
                                        <strong class="fw-semibold">{{ __('Catatan Pegawai') }}:</strong>
                                        {!! nl2br(e($approvalStage->comments)) !!}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic small">{{ __('Tiada sejarah kelulusan direkodkan untuk permohonan ini.') }}</p>
                @endif
            </section>

             @if ($emailApplication->rejection_reason && $emailApplication->status === \App\Models\EmailApplication::STATUS_REJECTED)
                <hr class="my-3">
                <section>
                    <h4 class="h6 fw-semibold text-danger mb-2"><i class="bi bi-x-octagon-fill me-2"></i>{{ __('Sebab Permohonan Ditolak') }}</h4>
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle small p-3">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $emailApplication->rejection_reason }}</p>
                    </div>
                </section>
            @endif

        </div> {{-- /card-body --}}

        <div class="card-footer bg-light py-3 px-4 text-end">
            {{-- IT Admin Action Form (if application is pending admin action) --}}
            @if ($emailApplication->status === \App\Models\EmailApplication::STATUS_PENDING_ADMIN || $emailApplication->status === \App\Models\EmailApplication::STATUS_APPROVED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED)
                @can('processByIT', $emailApplication) {{-- Assuming EmailApplicationPolicy@processByIT exists --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#processEmailApplicationModal-{{ $emailApplication->id }}">
                    <i class="bi bi-gear-fill me-1"></i> {{ __('Proses Penyediaan Akaun') }}
                </button>
                @endcan
            @endif
        </div>
    </div> {{-- /card --}}

    {{-- Modal for IT Admin to Process Application --}}
    @if ($emailApplication->status === \App\Models\EmailApplication::STATUS_PENDING_ADMIN || $emailApplication->status === \App\Models\EmailApplication::STATUS_APPROVED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED)
    @can('processByIT', $emailApplication)
    <div class="modal fade" id="processEmailApplicationModal-{{ $emailApplication->id }}" tabindex="-1" aria-labelledby="processEmailApplicationModalLabel-{{ $emailApplication->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('resource-management.email-applications-admin.process', $emailApplication) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="processEmailApplicationModalLabel-{{ $emailApplication->id }}"><i class="bi bi-person-fill-gear me-2"></i>{{ __('Tindakan Penyediaan Akaun E-mel/ID') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">
                            {{ __('Sila masukkan butiran akaun yang telah disediakan untuk pemohon') }}
                            <strong>{{ $applicant->name }}</strong> (Permohonan #{{ $emailApplication->id }}).
                            Status semasa permohonan: <span class="fw-bold">{{ $emailApplication->status_translated }}</span>.
                        </p>
                        <hr>
                        <div class="mb-3">
                            <label for="final_assigned_email-{{ $emailApplication->id }}" class="form-label">{{ __('E-mel Rasmi MOTAC Disediakan') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('final_assigned_email', 'provisioningForm') is-invalid @enderror"
                                   id="final_assigned_email-{{ $emailApplication->id }}" name="final_assigned_email"
                                   value="{{ old('final_assigned_email', $emailApplication->final_assigned_email ?? $emailApplication->proposed_email) }}"
                                   placeholder="cth: {{ strtolower(Str::slug($applicant->name, '.')) }}@motac.gov.my" required>
                            @error('final_assigned_email', 'provisioningForm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="final_assigned_user_id-{{ $emailApplication->id }}" class="form-label">{{ __('ID Pengguna Sistem Disediakan (Jika Ada)') }}</label>
                            <input type="text" class="form-control @error('final_assigned_user_id', 'provisioningForm') is-invalid @enderror"
                                   id="final_assigned_user_id-{{ $emailApplication->id }}" name="final_assigned_user_id"
                                   value="{{ old('final_assigned_user_id', $emailApplication->final_assigned_user_id) }}"
                                   placeholder="{{ __('Cth: ') }}{{ Str::slug($applicant->name, '_') }}">
                            @error('final_assigned_user_id', 'provisioningForm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="provisioning_status-{{ $emailApplication->id }}" class="form-label">{{ __('Status Penyediaan') }} <span class="text-danger">*</span></label>
                            <select name="provisioning_status" id="provisioning_status-{{ $emailApplication->id }}" class="form-select @error('provisioning_status', 'provisioningForm') is-invalid @enderror" required>
                                <option value="">-- {{ __('Pilih Status') }} --</option>
                                <option value="{{ \App\Models\EmailApplication::STATUS_COMPLETED }}" {{ old('provisioning_status') == \App\Models\EmailApplication::STATUS_COMPLETED ? 'selected' : '' }}>{{ __('Berjaya Disediakan & Dimaklumkan') }}</option>
                                <option value="{{ \App\Models\EmailApplication::STATUS_PROVISION_FAILED }}" {{ old('provisioning_status') == \App\Models\EmailApplication::STATUS_PROVISION_FAILED ? 'selected' : '' }}>{{ __('Gagal Disediakan') }}</option>
                            </select>
                            @error('provisioning_status', 'provisioningForm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="it_admin_notes-{{ $emailApplication->id }}" class="form-label">{{ __('Nota Pentadbir IT (Jika Ada)') }}</label>
                            <textarea name="it_admin_notes" id="it_admin_notes-{{ $emailApplication->id }}" rows="3"
                                      class="form-control @error('it_admin_notes', 'provisioningForm') is-invalid @enderror"
                                      placeholder="{{ __('Cth: Kata laluan sementara ialah Changeme123! Sila tukar selepas log masuk pertama.') }}">{{ old('it_admin_notes', $emailApplication->admin_notes) }}</textarea>
                            @error('it_admin_notes', 'provisioningForm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input @error('notify_user', 'provisioningForm') is-invalid @enderror" type="checkbox" value="1" id="notify_user-{{ $emailApplication->id }}" name="notify_user" checked>
                            <label class="form-check-label" for="notify_user-{{ $emailApplication->id }}">
                                {{ __('Maklumkan kepada pemohon melalui e-mel selepas tindakan ini.') }}
                            </label>
                             @error('notify_user', 'provisioningForm') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ __('Sahkan & Simpan Tindakan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
    @endif

</div>
@endsection

@push('scripts')
    {{-- Script to re-populate form fields if validation fails and modal reopens --}}
    @if($errors->provisioningForm->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var processModal = new bootstrap.Modal(document.getElementById('processEmailApplicationModal-{{ $emailApplication->id }}'));
            processModal.show();
        });
    </script>
    @endif
@endpush
