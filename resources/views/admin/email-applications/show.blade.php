{{-- resources/views/admin/email-applications/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Permohonan E-mel') . ' #' . $emailApplication->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0">{{ __('Butiran Permohonan E-mel') }}</h1>
                <span class="text-muted small">{{__('Permohonan')}} #{{ $emailApplication->id }}</span>
            </div>

            @include('_partials._alerts.alert-general')

            @if ($errors->any())
                <x-alert type="danger" :title="__('Amaran! Sila semak ralat input berikut:')" dismissible="true">
                    <ul class="list-unstyled mb-0 small ps-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Maklumat Permohonan') }}</h2>
                </div>
                <div class="card-body p-4 small">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">{{ __('Pemohon:') }}</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->user)->name ?? __('N/A')) }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Taraf Perkhidmatan:') }}</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->user)->service_status_label ?? __('N/A')) }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Jawatan & Gred:') }}</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->user->position)->name ?? __('N/A')) }} / {{ e(optional($emailApplication->user->grade)->name ?? __('N/A')) }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Bahagian/Unit:') }}</dt>
                        <dd class="col-sm-8">{{ e(optional($emailApplication->user->department)->name ?? __('N/A')) }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Tujuan Permohonan:') }}</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{{ e($emailApplication->application_reason_notes ?? __('N/A')) }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('Cadangan E-mel ID:') }}</dt>
                        <dd class="col-sm-8">{{ e($emailApplication->proposed_email) }}</dd>

                        @if ($emailApplication->group_email)
                            <dt class="col-sm-4 text-muted">{{ __('E-mel Kumpulan:') }}</dt>
                            <dd class="col-sm-8">{{ e($emailApplication->group_email) }}</dd>
                        @endif

                        <dt class="col-sm-4 text-muted">{{ __('Status Permohonan:') }}</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $emailApplication->status_color }}">{{ $emailApplication->status_label }}</span>
                            @if ($emailApplication->rejection_reason)
                                <br><span class="text-danger fst-italic">{{ __('Sebab Tolakan:') }} {{ e($emailApplication->rejection_reason) }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted">{{ __('Tarikh Permohonan:') }}</dt>
                        <dd class="col-sm-8">{{ optional($emailApplication->created_at)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                    </dl>

                    @if ($emailApplication->supportingOfficer)
                        <h3 class="h6 fw-semibold mt-3 mb-2 pt-2 border-top">{{ __('Maklumat Pegawai Penyokong') }}</h3>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Nama:') }}</dt>
                            <dd class="col-sm-8">{{ e($emailApplication->supportingOfficer->name ?? __('N/A')) }}</dd>
                            <dt class="col-sm-4 text-muted">{{ __('Gred:') }}</dt>
                            <dd class="col-sm-8">{{ e(optional($emailApplication->supportingOfficer->grade)->name ?? __('N/A')) }}</dd>
                            <dt class="col-sm-4 text-muted">{{ __('Tarikh Sokongan:') }}</dt>
                            <dd class="col-sm-8">{{ optional($emailApplication->approvals->where('stage', 'email_support_review')->first()->approval_timestamp ?? null)->translatedFormat('d M Y, H:i A') ?? __('Belum Disokong') }}</dd>
                            <dt class="col-sm-4 text-muted">{{ __('Komen Penyokong:') }}</dt>
                            <dd class="col-sm-8">{{ e($emailApplication->approvals->where('stage', 'email_support_review')->first()->comments ?? __('Tiada')) }}</dd>
                        </dl>
                    @else
                        <p class="text-info fst-italic mt-2 small"><i class="bi bi-info-circle me-1"></i>{{ __('Belum ada pegawai penyokong ditetapkan atau permohonan belum disokong.') }}</p>
                    @endif
                </div>
            </div>

            {{-- IT Admin Processing Form --}}
            @if (in_array($emailApplication->status, [\App\Models\EmailApplication::STATUS_PENDING_ADMIN, \App\Models\EmailApplication::STATUS_APPROVED, \App\Models\EmailApplication::STATUS_PROVISION_FAILED]))
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Tindakan Pentadbir IT (Penyediaan Akaun)') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('resource-management.email-applications-admin.process', $emailApplication) }}" method="POST">
                            @csrf

                            <input type="hidden" name="email_application_id" value="{{ $emailApplication->id }}">

                            <div class="alert alert-info small py-2">
                                <i class="bi bi-info-circle-fill me-1"></i> {{__('Sila isikan butiran akaun yang telah disediakan.')}}
                            </div>

                            <div class="mb-3">
                                <label for="final_assigned_email" class="form-label fw-semibold">{{ __('E-mel Akaun Yang Disediakan') }}<span class="text-danger">*</span></label>
                                <input type="email" name="final_assigned_email" id="final_assigned_email" class="form-control @error('final_assigned_email') is-invalid @enderror" value="{{ old('final_assigned_email', $emailApplication->final_assigned_email ?? $emailApplication->proposed_email . config('motac.email_account.email_domain', '@motac.gov.my')) }}" required placeholder="cth: nama.pegawai@motac.gov.my">
                                @error('final_assigned_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="final_assigned_user_id" class="form-label fw-semibold">{{ __('ID Pengguna Yang Disediakan (Jika Ada)') }}</label>
                                <input type="text" name="final_assigned_user_id" id="final_assigned_user_id" class="form-control @error('final_assigned_user_id') is-invalid @enderror" value="{{ old('final_assigned_user_id', $emailApplication->final_assigned_user_id) }}" placeholder="cth: MOTAC_USER001">
                                @error('final_assigned_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="provisioning_notes" class="form-label fw-semibold">{{ __('Catatan Penyediaan:') }}</label>
                                <textarea name="provisioning_notes" id="provisioning_notes" class="form-control @error('provisioning_notes') is-invalid @enderror" rows="3" placeholder="Nyatakan sebarang maklumat tambahan atau masalah yang dihadapi semasa proses penyediaan.">{{ old('provisioning_notes', $emailApplication->rejection_reason) }}</textarea>
                                @error('provisioning_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="provisioning_status" class="form-label fw-semibold">{{ __('Status Penyediaan') }}<span class="text-danger">*</span></label>
                                <select name="provisioning_status" id="provisioning_status" class="form-select @error('provisioning_status') is-invalid @enderror" required>
                                    <option value="">-- {{__('Pilih Status')}} --</option>
                                    <option value="{{ \App\Models\EmailApplication::STATUS_COMPLETED }}" {{ old('provisioning_status') == \App\Models\EmailApplication::STATUS_COMPLETED ? 'selected' : '' }}>
                                        {{ \App\Models\EmailApplication::$STATUS_OPTIONS[\App\Models\EmailApplication::STATUS_COMPLETED] }}
                                    </option>
                                    <option value="{{ \App\Models\EmailApplication::STATUS_PROVISION_FAILED }}" {{ old('provisioning_status') == \App\Models\EmailApplication::STATUS_PROVISION_FAILED ? 'selected' : '' }}>
                                        {{ \App\Models\EmailApplication::$STATUS_OPTIONS[\App\Models\EmailApplication::STATUS_PROVISION_FAILED] }}
                                    </option>
                                </select>
                                @error('provisioning_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="text-center mt-4 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                                    <i class="bi bi-gear-fill me-2"></i>
                                    {{ __('Proses Permohonan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info small py-3">
                    <i class="bi bi-info-circle-fill me-1"></i> {{__('Permohonan ini tidak memerlukan tindakan penyediaan pada masa ini atau telah selesai.')}}
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('resource-management.email-applications-admin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                     <i class="bi bi-arrow-left-circle me-1"></i>
                    {{ __('Kembali ke Senarai Permohonan E-mel') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
