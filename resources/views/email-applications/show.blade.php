{{-- resources/views/email-applications/show.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@php
    // Consolidate type display logic
    $itemTypeDisplay = __('Permohonan Emel / ID Pengguna MOTAC');
@endphp

@section('title', $itemTypeDisplay . ' #' . $emailApplication->id)

@section('content')
<div class="container py-4">
    <div class="col-lg-10 mx-auto">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
            <div>
                <h1 class="fs-3 fw-bold mb-1">
                    {{ $itemTypeDisplay }}
                </h1>
                <p class="text-muted mb-0">{{ __('ID Permohonan') }}: #{{ $emailApplication->id }}</p>
            </div>
            <x-resource-status-panel :resource="$emailApplication" statusAttribute="status" class="fs-5 p-2 mt-2 mt-sm-0" :showIcon="true" />
        </div>

        {{-- Assuming session messages are handled globally by _partials._alerts.alert-general.blade.php --}}

        @if (($emailApplication->status === \App\Models\EmailApplication::STATUS_REJECTED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED) && $emailApplication->rejection_reason)
            <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis p-3 rounded mb-4" role="alert">
                <h4 class="alert-heading fw-semibold"><i class="bi bi-exclamation-octagon-fill me-2"></i>{{ __('Sebab Penolakan / Kegagalan Proses') }}:</h4>
                <p class="mb-0" style="white-space: pre-wrap;">{{ e($emailApplication->rejection_reason) }}</p>
            </div>
        @endif

        <div class="card shadow-lg rounded-4">
            <div class="card-body p-4 p-sm-5 vstack gap-4">

                <section aria-labelledby="applicant-information-heading">
                    <h2 id="applicant-information-heading" class="fs-5 fw-semibold mb-3 border-bottom pb-2">{{ __('MAKLUMAT PEMOHON') }}</h2>
                    @if($emailApplication->user)
                        <x-user-info-card :user="$emailApplication->user" title=""/>
                    @else
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 fw-medium text-muted">{{__('Nama Penuh')}}:</dt>
                            <dd class="col-sm-8">{{ e($emailApplication->applicant_name ?? 'N/A') }}</dd>
                        </dl>
                    @endif
                    <p class="small mt-2"><span class="fw-medium text-muted">{{__('Taraf Perkhidmatan')}}:</span>
                        <span>{{ e(\App\Models\User::getServiceStatusDisplayName($emailApplication->service_status)) }}</span>
                    </p>
                </section>

                <section aria-labelledby="application-details-heading">
                    <h2 id="application-details-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('BUTIRAN PERMOHONAN') }}</h2>
                    <dl class="row g-2 small">
                        <dt class="col-sm-12 fw-medium text-muted">{{__('Tujuan Permohonan / Catatan Tambahan')}}:</dt>
                        <dd class="col-sm-12" style="white-space: pre-wrap;">{{ e($emailApplication->application_reason_notes ?? ($emailApplication->purpose ?? 'Tiada')) }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{__('Cadangan E-mel Rasmi')}}:</dt>
                        <dd class="col-sm-8">{{ e($emailApplication->proposed_email ?? 'Tidak Memohon E-mel Rasmi') }}</dd>

                        {{-- CORRECTED: Check if group_email field has a value --}}
                        @if (!empty($emailApplication->group_email))
                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light-subtle border rounded">
                                    <h3 class="fs-6 fw-semibold mb-2">{{ __('Maklumat Group E-mel') }}</h3>
                                    <dl class="row g-1 nested-dl small">
                                        <dt class="col-sm-5 fw-medium text-muted">{{__('Alamat Group E-mel Dicadang')}}:</dt>
                                        <dd class="col-sm-7">{{ e($emailApplication->group_email ?? 'N/A') }}</dd>
                                        <dt class="col-sm-5 fw-medium text-muted">{{__('Nama Admin/EO/CC Group')}}:</dt>
                                        <dd class="col-sm-7">{{ e($emailApplication->contact_person_name ?? ($emailApplication->group_admin_name ?? 'N/A')) }}</dd>
                                        <dt class="col-sm-5 fw-medium text-muted">{{__('E-mel Rasmi Admin/EO/CC Group')}}:</dt>
                                        <dd class="col-sm-7">{{ e($emailApplication->contact_person_email ?? ($emailApplication->group_admin_email ?? 'N/A')) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        @endif
                    </dl>
                </section>

                 <section aria-labelledby="applicant-certification-heading">
                    <h2 id="applicant-certification-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('PERAKUAN PEMOHON') }}</h2>
                     <dl class="row g-2 small">
                        <dt class="col-sm-5 fw-medium text-muted">{{__('Semua maklumat yang diisi adalah BENAR')}}:</dt>
                        <dd class="col-sm-7"><x-boolean-badge :value="(bool)$emailApplication->cert_info_is_true" /></dd>

                        <dt class="col-sm-5 fw-medium text-muted">{{__('BERSETUJU maklumat diguna pakai untuk pemprosesan')}}:</dt>
                         <dd class="col-sm-7"><x-boolean-badge :value="(bool)$emailApplication->cert_data_usage_agreed" /></dd>

                        <dt class="col-sm-5 fw-medium text-muted">{{__('BERSETUJU bertanggungjawab atas penggunaan e-mel')}}:</dt>
                        <dd class="col-sm-7"><x-boolean-badge :value="(bool)$emailApplication->cert_email_responsibility_agreed" /></dd>

                        <dt class="col-sm-5 fw-medium text-muted">{{__('Tarikh Perakuan')}}:</dt>
                        <dd class="col-sm-7">{{ optional($emailApplication->certification_timestamp)->translatedFormat('d M Y, H:i A') ?? __('Belum Diperakui') }}</dd>
                    </dl>
                </section>

                @if($emailApplication->supportingOfficer || $emailApplication->supporting_officer_name)
                <section aria-labelledby="supporting-officer-info-heading">
                    <h2 id="supporting-officer-info-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h2>
                    <dl class="row g-2 small">
                        <dt class="col-sm-4 fw-medium text-muted">{{__('Nama')}}:</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->supportingOfficer)->name ?? $emailApplication->supporting_officer_name ?? 'N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{__('E-mel')}}:</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->supportingOfficer)->email ?? $emailApplication->supporting_officer_email ?? 'N/A') }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{__('Gred')}}:</dt>
                        <dd class="col-sm-8">{{ e(optional(optional($emailApplication->supportingOfficer)->grade)->name ?? $emailApplication->supporting_officer_grade ?? 'N/A') }}</dd>
                    </dl>
                </section>
                @endif

                @if ($emailApplication->isCompletedOrProvisionFailed()) {{-- Ensure this method exists on EmailApplication model --}}
                <section aria-labelledby="provisioning-details-heading">
                    <h2 id="provisioning-details-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('MAKLUMAT PENYEDIAAN AKAUN') }}</h2>
                     <dl class="row g-2 small">
                        <dt class="col-sm-4 fw-medium text-muted">{{__('E-mel Rasmi Ditetapkan')}}:</dt>
                        <dd class="col-sm-8 font-monospace">{{ e($emailApplication->final_assigned_email ?? 'Belum Ditetapkan') }}</dd>
                        <dt class="col-sm-4 fw-medium text-muted">{{__('ID Pengguna Ditetapkan')}}:</dt>
                        <dd class="col-sm-8 font-monospace">{{ e($emailApplication->final_assigned_user_id ?? 'Belum Ditetapkan') }}</dd>
                    </dl>
                </section>
                @endif

                <section aria-labelledby="approval-history-heading">
                    <h2 id="approval-history-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('SEJARAH KELULUSAN & TINDAKAN') }}</h2>
                    @if ($emailApplication->approvals->isEmpty())
                        <p class="text-muted fst-italic small">{{ __('Tiada sejarah kelulusan atau tindakan untuk permohonan ini.') }}</p>
                    @else
                        <div class="table-responsive border rounded shadow-sm">
                            <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="p-2 small text-uppercase text-muted fw-medium">{{__('Peringkat')}}</th>
                                        <th scope="col" class="p-2 small text-uppercase text-muted fw-medium">{{__('Pegawai')}}</th>
                                        <th scope="col" class="p-2 small text-uppercase text-muted fw-medium">{{__('Status')}}</th>
                                        <th scope="col" class="p-2 small text-uppercase text-muted fw-medium">{{__('Catatan')}}</th>
                                        <th scope="col" class="p-2 small text-uppercase text-muted fw-medium">{{__('Tarikh Tindakan')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emailApplication->approvals()->orderBy('created_at', 'desc')->get() as $approval)
                                        <tr>
                                            <td class="p-2 small">{{ e(\App\Models\Approval::getStageDisplayName($approval->stage)) }}</td>
                                            <td class="p-2 small">{{ e(optional($approval->officer)->name ?? 'N/A') }}</td>
                                            <td class="p-2"><x-approval-status-badge :status="$approval->status" /></td>
                                            <td class="p-2 small" style="white-space: pre-wrap;">{{ e($approval->comments ?? '-') }}</td>
                                            <td class="p-2 small">{{ optional($approval->approval_timestamp)->translatedFormat('d M Y, H:i A') ?? optional($approval->created_at)->translatedFormat('d M Y, H:i A') }}</td>
=======
@section('title', __('Butiran Permohonan E-mel #') . $emailApplication->id)

@section('content')
<div class="container py-5">
    <div class="col-lg-10 mx-auto">

        <div class="d-md-flex justify-content-between align-items-md-center mb-4">
            <h1 class="fs-2 fw-bold mb-2 mb-md-0">
                {{ __('Butiran Permohonan E-mel ICT') }} #{{ $emailApplication->id }}
            </h1>
            <div>
                {{-- Ensure x-resource-status-panel uses Bootstrap classes internally --}}
                <x-resource-status-panel :resource="$emailApplication" statusAttribute="status" class="fs-5 p-2" />
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (($emailApplication->status === \App\Models\EmailApplication::STATUS_REJECTED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED) && $emailApplication->rejection_reason)
            <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger-emphasis p-3 rounded mb-4" role="alert">
                <h4 class="alert-heading fw-semibold">{{ __('Sebab Penolakan/Kegagalan') }}:</h4>
                <p class="mb-0" style="white-space: pre-wrap;">{{ $emailApplication->rejection_reason }}</p>
            </div>
        @endif

        <div class="card shadow-lg rounded-3">
            <div class="card-body p-4 p-sm-5 vstack gap-4">

                <section aria-labelledby="applicant-information">
                    <h3 id="applicant-information" class="fs-5 fw-semibold mb-3 border-bottom pb-2">MAKLUMAT PEMOHON</h3>
                    @if($emailApplication->user)
                        {{-- Ensure x-user-info-card uses Bootstrap classes internally --}}
                        <x-user-info-card :user="$emailApplication->user" title=""/>
                    @else
                        <div class="row gx-4 gy-2">
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('Nama Penuh')}}:</span> <span>{{ $emailApplication->applicant_name ?? 'N/A' }}</span></div>
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('No. Pengenalan')}}:</span> <span>{{ $emailApplication->applicant_identification_number ?? 'N/A' }}</span></div>
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('Jawatan & Gred')}}:</span> <span>{{ $emailApplication->applicant_jawatan_gred ?? 'N/A' }}</span></div>
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('Bahagian/Unit')}}:</span> <span>{{ $emailApplication->applicant_bahagian_unit ?? 'N/A' }}</span></div>
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('No. Telefon Bimbit')}}:</span> <span>{{ $emailApplication->applicant_mobile_number ?? 'N/A' }}</span></div>
                            <div class="col-md-6"><span class="fw-medium text-muted">{{__('E-mel Peribadi')}}:</span> <span>{{ $emailApplication->applicant_personal_email ?? 'N/A' }}</span></div>
                        </div>
                    @endif
                    <div class="mt-3"><span class="fw-medium text-muted">{{__('Taraf Perkhidmatan')}}:</span> <span>{{ \App\Models\EmailApplication::$SERVICE_STATUSES[$emailApplication->service_status] ?? ucfirst(str_replace('_', ' ', $emailApplication->service_status ?? 'N/A')) }}</span></div>
                </section>

                <section aria-labelledby="application-details">
                    <h3 id="application-details" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">BUTIRAN PERMOHONAN</h3>
                    <div class="row gx-4 gy-2">
                        <div class="col-12"><span class="fw-medium text-muted">{{__('Tujuan Permohonan / Catatan')}}:</span> <p style="white-space: pre-wrap;">{{ $emailApplication->purpose ?? ($emailApplication->application_reason_notes ?? 'N/A') }}</p></div>
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('Cadangan E-mel/ID')}}:</span> <span>{{ $emailApplication->proposed_email ?? 'N/A' }}</span></div>
                         @if ($emailApplication->group_email || $emailApplication->group_admin_name || $emailApplication->group_admin_email)
                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light border rounded">
                                    <h5 class="fs-6 fw-semibold mb-2">{{ __('Butiran Group E-mel:') }}</h5>
                                    <p class="mb-1"><span class="fw-medium text-muted">{{__('Nama Group Email')}}:</span> <span>{{ $emailApplication->group_email ?? 'N/A' }}</span></p>
                                    <p class="mb-1"><span class="fw-medium text-muted">{{__('Nama Admin/EO/CC')}}:</span> <span>{{ $emailApplication->contact_person_name ?? ($emailApplication->group_admin_name ?? 'N/A') }}</span></p>
                                    <p class="mb-0"><span class="fw-medium text-muted">{{__('E-mel Admin/EO/CC')}}:</span> <span>{{ $emailApplication->contact_person_email ?? ($emailApplication->group_admin_email ?? 'N/A') }}</span></p>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                 <section aria-labelledby="applicant-certification">
                    <h3 id="applicant-certification" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">PERAKUAN PEMOHON</h3>
                     <div class="vstack gap-1">
                        <p><span class="fw-medium text-muted">{{__('Semua maklumat BENAR')}}:</span>
                            {{-- Ensure x-approval-status-badge uses Bootstrap classes internally --}}
                            <x-approval-status-badge :status="$emailApplication->cert_info_is_true ? 'approved' : 'rejected'" />
                        </p>
                        <p><span class="fw-medium text-muted">{{__('Persetujuan Penggunaan Data')}}:</span>
                             <x-approval-status-badge :status="$emailApplication->cert_data_usage_agreed ? 'approved' : 'rejected'" />
                        </p>
                        <p><span class="fw-medium text-muted">{{__('Persetujuan Tanggungjawab E-mel')}}:</span>
                            <x-approval-status-badge :status="$emailApplication->cert_email_responsibility_agreed ? 'approved' : 'rejected'" />
                        </p>
                        <p><span class="fw-medium text-muted">{{__('Tarikh Perakuan')}}:</span> <span>{{ optional($emailApplication->certification_timestamp)->format('d M Y, H:i A') ?? __('Belum Diperakui') }}</span></p>
                    </div>
                </section>

                @if($emailApplication->supportingOfficer || $emailApplication->supporting_officer_name)
                <section aria-labelledby="supporting-officer-info">
                    <h3 id="supporting-officer-info" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">MAKLUMAT PEGAWAI PENYOKONG</h3>
                    <div class="row gx-4 gy-2">
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('Nama')}}:</span> <span>{{ optional($emailApplication->supportingOfficer)->name ?? $emailApplication->supporting_officer_name ?? 'N/A' }}</span></div>
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('E-mel')}}:</span> <span>{{ optional($emailApplication->supportingOfficer)->email ?? $emailApplication->supporting_officer_email ?? 'N/A' }}</span></div>
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('Gred')}}:</span> <span>{{ optional(optional($emailApplication->supportingOfficer)->grade)->name ?? $emailApplication->supporting_officer_grade ?? 'N/A' }}</span></div>
                    </div>
                </section>
                @endif

                @if ($emailApplication->status === \App\Models\EmailApplication::STATUS_COMPLETED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PROVISION_FAILED || $emailApplication->final_assigned_email)
                <section aria-labelledby="provisioning-details">
                    <h3 id="provisioning-details" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">MAKLUMAT PENYEDIAAN AKAUN</h3>
                     <div class="row gx-4 gy-2">
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('E-mel Rasmi Ditetapkan')}}:</span> <span class="font-monospace">{{ $emailApplication->final_assigned_email ?? 'N/A' }}</span></div>
                        <div class="col-md-6"><span class="fw-medium text-muted">{{__('ID Pengguna Ditetapkan')}}:</span> <span class="font-monospace">{{ $emailApplication->final_assigned_user_id ?? 'N/A' }}</span></div>
                    </div>
                </section>
                @endif

                <section aria-labelledby="approval-history">
                    <h3 id="approval-history" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">SEJARAH KELULUSAN & TINDAKAN</h3>
                    @if ($emailApplication->approvals->isEmpty())
                        <p class="text-muted">{{ __('Tiada sejarah kelulusan untuk permohonan ini.') }}</p>
                    @else
                        <div class="table-responsive border rounded shadow-sm">
                            <table class="table table-sm mb-0"> {{-- table-sm for denser table --}}
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="p-2 text-start small text-uppercase text-muted">{{__('Peringkat')}}</th>
                                        <th scope="col" class="p-2 text-start small text-uppercase text-muted">{{__('Pegawai')}}</th>
                                        <th scope="col" class="p-2 text-start small text-uppercase text-muted">{{__('Status')}}</th>
                                        <th scope="col" class="p-2 text-start small text-uppercase text-muted">{{__('Catatan')}}</th>
                                        <th scope="col" class="p-2 text-start small text-uppercase text-muted">{{__('Tarikh')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($emailApplication->approvals as $approval)
                                        <tr>
                                            <td class="p-2 align-middle small">{{ Str::title(str_replace('_', ' ', $approval->stage ?? 'N/A')) }}</td>
                                            <td class="p-2 align-middle small">{{ optional($approval->officer)->name ?? 'N/A' }}</td>
                                            <td class="p-2 align-middle">
                                                {{-- Ensure x-approval-status-badge uses Bootstrap classes internally --}}
                                                <x-approval-status-badge :status="$approval->status" />
                                            </td>
                                            <td class="p-2 align-middle small" style="white-space: pre-wrap;">{{ $approval->comments ?? '-' }}</td>
                                            <td class="p-2 align-middle small">{{ optional($approval->approval_timestamp)->format('d M Y, H:i A') ?? 'N/A' }}</td>
>>>>>>> b3ca845 (code additions and edits)
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

<<<<<<< HEAD
                <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <a href="{{ route('email-applications.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            {{ __('Kembali ke Senarai') }}
                        </a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @can('update', $emailApplication)
                             @if ($emailApplication->isDraft())
                                <a href="{{ route('email-applications.edit', $emailApplication) }}" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                   <i class="bi bi-pencil-square me-1"></i> {{ __('Edit Draf') }}
                                </a>
                            @endif
                        @endcan

                        @can('submit', $emailApplication)
                            @if ($emailApplication->isDraft() || $emailApplication->status === \App\Models\EmailApplication::STATUS_REJECTED)
                                <form action="{{ route('email-applications.submit', $emailApplication) }}" method="POST" onsubmit="return confirm('{{ __('Adakah anda pasti untuk menghantar permohonan ini?') }}');" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center">
                                        <i class="bi bi-send-check-fill me-1"></i> {{ __('Hantar Permohonan') }}
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('processByIT', $emailApplication)
                             @if ($emailApplication->status === \App\Models\EmailApplication::STATUS_APPROVED || $emailApplication->status === \App\Models\EmailApplication::STATUS_PENDING_ADMIN)
                                 <button type="button" class="btn btn-info btn-sm d-inline-flex align-items-center" onclick="alert('Trigger IT Process Modal/Action for {{ $emailApplication->id }}');">
                                    <i class="bi bi-gear-fill me-1"></i> {{ __('Proses Penyediaan Akaun') }}
                                </button>
                            @endif
                        @endcan

                        @can('delete', $emailApplication)
                            @if ($emailApplication->isDraft())
                                <form action="{{ route('email-applications.destroy', $emailApplication) }}" method="POST" onsubmit="return confirm('{{ __('Adakah anda pasti untuk memadam draf permohonan ini?') }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                        <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Draf') }}
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
=======
                <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2 justify-content-start">
                    <a href="{{ route('resource-management.my-applications.email.index') }}" class="btn btn-outline-secondary btn-sm text-uppercase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-left-circle me-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/></svg>
                        {{ __('Kembali ke Senarai') }}
                    </a>

                    @can('update', $emailApplication)
                         @if ($emailApplication->isDraft())
                            <a href="{{ route('resource-management.my-applications.email-applications.edit', $emailApplication) }}" class="btn btn-primary btn-sm text-uppercase">
                                {{ __('Edit Draf') }}
                            </a>
                        @endif
                    @endcan

                    @can('submit', $emailApplication)
                        <form action="{{ route('resource-management.my-applications.email-applications.submit', $emailApplication) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk menghantar permohonan ini?');" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm text-uppercase">{{ __('Hantar Permohonan') }}</button>
                        </form>
                    @endcan

                    @can('approve', [\App\Models\Approval::class, $emailApplication, \App\Models\Approval::STAGE_SUPPORT_REVIEW])
                        <button type="button" @click="showApprovalModal = true; approvalAction = 'approve'; approvalStage = 'support_review';" class="btn btn-info btn-sm text-uppercase">
                            {{ __('Sokong Permohonan (Tindakan Peg. Penyokong)') }}
                        </button>
                    @endcan

                    @can('processByIT', $emailApplication)
                         <button type="button" @click="showProvisioningModal = true;" class="btn btn-purple btn-sm text-uppercase"> {{-- Assuming .btn-purple is custom or use Bootstrap theme color --}}
                            {{ __('Proses Penyediaan Akaun (Tindakan Pentadbir IT)') }}
                        </button>
                    @endcan

                    @can('delete', $emailApplication)
                        @if ($emailApplication->isDraft())
                            <form action="{{ route('resource-management.my-applications.email-applications.destroy', $emailApplication) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk memadam draf permohonan ini?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm text-uppercase">{{ __('Padam Draf') }}</button>
                            </form>
                        @endif
                    @endcan
>>>>>>> b3ca845 (code additions and edits)
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
</div>
@endsection

@push('styles')
<style>
    .nested-dl dt { font-weight: normal; }
</style>
@endpush
=======

    {{-- Alpine.js Modal Example (Refactored for Bootstrap classes) --}}
    {{-- Note: For full Bootstrap modal functionality, you'd typically use Bootstrap's JS.
         This Alpine version attempts to mimic appearance with Bootstrap classes. --}}
    {{--
    <div x-data="{ showApprovalModal: false, showProvisioningModal: false, approvalAction: '', approvalStage: '', comments: '', final_assigned_email: '{{ $emailApplication->proposed_email }}', final_assigned_user_id: '' }" x-cloak>

        <div x-show="showApprovalModal" class="modal fade" :class="{'show': showApprovalModal, 'd-block': showApprovalModal}" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvalModalLabel" x-text="approvalAction === 'approve' ? 'Sahkan Tindakan Kelulusan' : 'Sahkan Tindakan Penolakan'"></h5>
                        <button type="button" class="btn-close" @click="showApprovalModal = false" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('approvals.recordDecision', ['approval' => 'APPROVAL_ID_PLACEHOLDER' ]) }}" method="POST">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="decision" x-model="approvalAction">
                            <input type="hidden" name="stage" x-model="approvalStage">
                            <div class="mb-3">
                                <label for="comments" class="form-label">Catatan (Pilihan jika meluluskan, Wajib jika menolak):</label>
                                <textarea id="comments" name="comments" x-model="comments" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showApprovalModal = false">Batal</button>
                            <button type="submit" class="btn" :class="{ 'btn-primary': approvalAction === 'approve', 'btn-danger': approvalAction === 'reject' }">Hantar Tindakan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         <div x-show="showApprovalModal" class="modal-backdrop fade show" @click="showApprovalModal = false"></div>


        <div x-show="showProvisioningModal" class="modal fade" :class="{'show': showProvisioningModal, 'd-block': showProvisioningModal}" tabindex="-1" role="dialog" aria-labelledby="provisioningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="provisioningModalLabel">Proses Penyediaan Akaun E-mel</h5>
                        <button type="button" class="btn-close" @click="showProvisioningModal = false" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('resource-management.admin.email-accounts.process', $emailApplication) }}" method="POST">
                        <div class="modal-body vstack gap-3">
                            @csrf
                            <div>
                                <label for="final_assigned_email" class="form-label">E-mel Rasmi Ditetapkan:</label>
                                <input type="email" id="final_assigned_email" name="final_assigned_email" x-model="final_assigned_email" required class="form-control">
                            </div>
                            <div>
                                <label for="final_assigned_user_id" class="form-label">ID Pengguna Ditetapkan (Jika Ada):</label>
                                <input type="text" id="final_assigned_user_id" name="final_assigned_user_id" x-model="final_assigned_user_id" class="form-control">
                            </div>
                            <div>
                                <label for="provisioning_notes" class="form-label">Catatan Penyediaan (Pilihan):</label>
                                <textarea id="provisioning_notes" name="provisioning_notes" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showProvisioningModal = false">Batal</button>
                            <button type="submit" class="btn btn-primary">Sahkan & Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="showProvisioningModal" class="modal-backdrop fade show" @click="showProvisioningModal = false"></div>

    </div>
    --}}
</div>
@endsection
>>>>>>> b3ca845 (code additions and edits)
