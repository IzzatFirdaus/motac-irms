{{-- resources/views/email-applications/show.blade.php --}}
@extends('layouts.app')

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
                <p class="text-muted mb-0">ID Permohonan: #{{ $emailApplication->id }}</p>
            </div>
            {{-- Assuming x-resource-status-panel is a component that displays status with appropriate styling --}}
            <x-resource-status-panel :status="$emailApplication->status" class="fs-5 p-2 mt-2 mt-sm-0" />
        </div>

        @include('partials.session-messages') {{-- Extracted session messages to a partial for reusability --}}

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
                        {{-- Assuming x-user-info-card is a component that displays user details --}}
                        <x-user-info-card :user="$emailApplication->user" title=""/>
                    @else
                        {{-- Fallback if user relationship isn't loaded or doesn't exist --}}
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 fw-medium text-muted">{{__('Nama Penuh')}}:</dt>
                            <dd class="col-sm-8">{{ e($emailApplication->applicant_name ?? 'N/A') }}</dd>
                            {{-- Add other fallback fields from $emailApplication if necessary --}}
                        </dl>
                    @endif
                    <p class="small mt-2"><span class="fw-medium text-muted">{{__('Taraf Perkhidmatan')}}:</span>
                        <span>{{ e(\App\Models\User::getServiceStatusDisplayName($emailApplication->service_status)) }}</span> {{-- Assumes a helper method on User or EmailApplication model --}}
                    </p>
                </section>

                <section aria-labelledby="application-details-heading">
                    <h2 id="application-details-heading" class="fs-5 fw-semibold mb-3 mt-4 border-bottom pb-2">{{ __('BUTIRAN PERMOHONAN') }}</h2>
                    <dl class="row g-2 small">
                        <dt class="col-sm-12 fw-medium text-muted">{{__('Tujuan Permohonan / Catatan Tambahan')}}:</dt>
                        <dd class="col-sm-12" style="white-space: pre-wrap;">{{ e($emailApplication->purpose ?? ($emailApplication->application_reason_notes ?? 'Tiada')) }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{__('Cadangan E-mel Rasmi')}}:</dt>
                        <dd class="col-sm-8">{{ e($emailApplication->proposed_email ?? 'Tidak Memohon E-mel Rasmi') }}</dd>

                        @if ($emailApplication->is_group_email_request || $emailApplication->group_email) {{-- Check if it was a group email request or if group email exists --}}
                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light-subtle border rounded">
                                    <h3 class="fs-6 fw-semibold mb-2">{{ __('Maklumat Group E-mel') }}</h3>
                                    <dl class="row g-1 nested-dl small"> {{-- Nested definition list --}}
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
                        <dd class="col-sm-7"><x-boolean-badge :value="$emailApplication->cert_info_is_true" /></dd>

                        <dt class="col-sm-5 fw-medium text-muted">{{__('BERSETUJU maklumat diguna pakai untuk pemprosesan')}}:</dt>
                         <dd class="col-sm-7"><x-boolean-badge :value="$emailApplication->cert_data_usage_agreed" /></dd>

                        <dt class="col-sm-5 fw-medium text-muted">{{__('BERSETUJU bertanggungjawab atas penggunaan e-mel')}}:</dt>
                        <dd class="col-sm-7"><x-boolean-badge :value="$emailApplication->cert_email_responsibility_agreed" /></dd>

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

                        <dt class="col-sm-4 fw-medium text-muted">{{__('Gred')}}:</dt> {{-- System Design reference for grades [cite: 284] --}}
                        <dd class="col-sm-8">{{ e(optional(optional($emailApplication->supportingOfficer)->grade)->name ?? $emailApplication->supporting_officer_grade ?? 'N/A') }}</dd>
                    </dl>
                </section>
                @endif

                @if ($emailApplication->isCompletedOrProvisionFailed()) {{-- Assuming method exists on model --}}
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <a href="{{ route('email-applications.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            {{ __('Kembali ke Senarai') }}
                        </a>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @can('update', $emailApplication)
                             @if ($emailApplication->isDraft()) {{-- Assuming isDraft() method on model --}}
                                <a href="{{ route('email-applications.edit', $emailApplication) }}" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                   <i class="bi bi-pencil-square me-1"></i> {{ __('Edit Draf') }}
                                </a>
                            @endif
                        @endcan

                        @can('submit', $emailApplication) {{-- Policy for submitting --}}
                            <form action="{{ route('email-applications.submit', $emailApplication) }}" method="POST" onsubmit="return confirm('{{ __('Adakah anda pasti untuk menghantar permohonan ini?') }}');" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center">
                                    <i class="bi bi-send-check-fill me-1"></i> {{ __('Hantar Permohonan') }}
                                </button>
                            </form>
                        @endcan

                        {{-- Example: Button for IT Admin to process (if applicable and policy allows) --}}
                        @can('processByIT', $emailApplication)
                             <button type="button" class="btn btn-info btn-sm d-inline-flex align-items-center" onclick="alert('Trigger IT Process Modal/Action');"> {{-- Replace onclick with actual modal trigger --}}
                                <i class="bi bi-gear-fill me-1"></i> {{ __('Proses Penyediaan Akaun') }}
                            </button>
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
                </div>
            </div>
        </div>
    </div>

    {{-- The Alpine.js Modals were commented out in the original. Keeping them commented. --}}
    {{-- If you plan to use them, ensure Alpine.js is set up and the modal actions/routes are correctly wired. --}}
    {{--
    <div x-data="{ showApprovalModal: false, ... }" x-cloak>
        ... Alpine Modal HTML ...
    </div>
    --}}
</div>
@endsection

@push('styles')
<style>
    .nested-dl dt { font-weight: normal; } /* Example style for nested dl */
</style>
@endpush
