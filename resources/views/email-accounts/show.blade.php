@extends('layouts.app') {{-- Your Bootstrap base layout --}}

@section('title', __('Butiran Permohonan Emel/ID') . ' #' . $emailApplication->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Butiran Permohonan Akaun E-mel / ID Pengguna') }} #{{ $emailApplication->id }}</h4>
                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($emailApplication->status) }}">{{ $emailApplication->status_translated }}</span>
                </div>

                <div class="card-body">
                    <x-alert-manager />

                    {{-- MAKLUMAT PEMOHON --}}
                    <section class="mb-4">
                        <h5 class="card-subtitle mb-3 text-muted">{{ __('Maklumat Pemohon') }}</h5>
                        @if ($emailApplication->user)
                            <div class="row g-3">
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('Nama Penuh') }}:</strong> {{ $emailApplication->user->title ? $emailApplication->user->title.' ' : '' }}{{ $emailApplication->user->name }}</p></div>
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('No. Kad Pengenalan') }}:</strong> {{ $emailApplication->user->identification_number ?? __('N/A') }}</p></div>
                                {{-- ... other user fields as per design using Bootstrap classes ... --}}
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('Taraf Perkhidmatan') }}:</strong> {{ $emailApplication->service_status_translated ?? __('N/A') }}</p></div>
                            </div>
                        @else
                            <p class="text-danger">{{ __('Maklumat pemohon tidak dapat dimuatkan.') }}</p>
                        @endif
                    </section>
                    <hr class="my-3">

                    {{-- BUTIRAN PERMOHONAN --}}
                    <section class="mb-4">
                        <h5 class="card-subtitle mb-3 text-muted">{{ __('Butiran Permohonan') }}</h5>
                        <div class="row g-3">
                            <div class="col-12"><p class="mb-1"><strong class="fw-semibold">{{ __('Tujuan / Catatan') }}:</strong> <span style="white-space: pre-wrap;">{{ $emailApplication->application_reason_notes ?? __('Tiada') }}</span></p></div>
                            <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('Cadangan E-mel / ID Pengguna') }}:</strong> {{ $emailApplication->proposed_email ?? __('Tiada Cadangan') }}</p></div>
                            {{-- ... other application detail fields ... --}}
                            @if($emailApplication->group_email)
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('Group Email') }}:</strong> {{ $emailApplication->group_email }}</p></div>
                                {{-- ... group admin name and email ... --}}
                            @endif
                        </div>
                    </section>
                    <hr class="my-3">

                    {{-- PERAKUAN PEMOHON --}}
                    <section class="mb-4">
                        <h5 class="card-subtitle mb-3 text-muted">{{ __('Perakuan Pemohon') }}</h5>
                        @if ($emailApplication->cert_info_is_true && $emailApplication->cert_data_usage_agreed && $emailApplication->cert_email_responsibility_agreed)
                            <div class="alert alert-success py-2 small"><i class="ti ti-checks me-1"></i>{{ __('Pemohon telah memperakui semua syarat pada:') }} {{ $emailApplication->certification_timestamp?->translatedFormat(config('app.datetime_format_my')) ?? __('N/A') }}</div>
                        @else
                            <div class="alert alert-warning py-2 small">{{ __('Perakuan pemohon tidak lengkap atau permohonan belum dihantar.') }}</div>
                        @endif
                    </section>
                    <hr class="my-3">

                    {{-- MAKLUMAT PEGAWAI PENYOKONG --}}
                    <section class="mb-4">
                        <h5 class="card-subtitle mb-3 text-muted">{{__('Maklumat Pegawai Penyokong')}}</h5>
                        @if($emailApplication->supporting_officer_name)
                            <p class="mb-1"><strong class="fw-semibold">{{__('Nama:')}}</strong> {{ $emailApplication->supporting_officer_name }}</p>
                            <p class="mb-1"><strong class="fw-semibold">{{__('Gred:')}}</strong> {{ $emailApplication->supporting_officer_grade ?? __('N/A') }}</p>
                            <p class="mb-1"><strong class="fw-semibold">{{__('Emel:')}}</strong> {{ $emailApplication->supporting_officer_email ?? __('N/A') }}</p>
                        @else
                             <p class="text-muted">{{__('Tiada maklumat pegawai penyokong.')}}</p>
                        @endif
                    </section>
                    <hr class="my-3">

                    {{-- SEJARAH KELULUSAN --}}
                    <section class="mb-4">
                        <h5 class="card-subtitle mb-3 text-muted">{{ __('Sejarah Kelulusan') }}</h5>
                        @if ($emailApplication->approvals->isNotEmpty())
                            @foreach ($emailApplication->approvals as $approval)
                                <div class="border-start border-4 border-info ps-3 mb-3">
                                    <p class="mb-0"><strong class="fw-semibold">{{ App\Models\Approval::getStageDisplayName($approval->stage) }}</strong></p>
                                    <p class="mb-0 small"><strong class="fw-semibold">{{ __('Pegawai:') }}</strong> {{ $approval->officer?->name ?? __('Tidak Diketahui') }}</p>
                                    <p class="mb-0 small"><strong class="fw-semibold">{{ __('Status Keputusan:') }}</strong>
                                        <span class="badge {{ App\Helpers\Helpers::getStatusColorClass('approval_'.$approval->status) }}">{{ __(Str::title(str_replace('_', ' ', $approval->status))) }}</span>
                                    </p>
                                    @if ($approval->comments)
                                        <p class="mb-0 small fst-italic"><strong class="fw-semibold">{{ __('Komen:') }}</strong> "{{ $approval->comments }}"</p>
                                    @endif
                                    <p class="mb-0 text-muted small">{{ __('Pada:') }} {{ $approval->approval_timestamp?->translatedFormat(config('app.datetime_format_my')) ?? __('N/A') }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">{{ __('Tiada sejarah kelulusan direkodkan lagi.') }}</p>
                        @endif
                    </section>

                    {{-- Conditional Admin Notes, Rejection Reason, Final Assigned Details --}}
                    @if ($emailApplication->admin_notes) {{-- Assuming admin_notes can be part of EmailApplication --}}
                        <hr class="my-3">
                        <section class="mb-4">
                            <h5 class="card-subtitle mb-3 text-muted">{{ __('Catatan IT Admin') }}</h5>
                            <p style="white-space: pre-wrap;">{{ $emailApplication->admin_notes }}</p>
                        </section>
                    @endif

                    @if ($emailApplication->isRejected() && $emailApplication->rejection_reason)
                        <hr class="my-3">
                        <section class="mb-4">
                            <h5 class="card-subtitle mb-3 text-danger">{{ __('Sebab Penolakan') }}</h5>
                            <p class="text-danger" style="white-space: pre-wrap;">{{ $emailApplication->rejection_reason }}</p>
                        </section>
                    @endif

                    @if ($emailApplication->isCompleted() || $emailApplication->isApproved())
                        @if ($emailApplication->final_assigned_email || $emailApplication->final_assigned_user_id)
                        <hr class="my-3">
                        <section class="mb-4">
                            <h5 class="card-subtitle mb-3 text-success">{{ __('Butiran Akaun yang Ditetapkan') }}</h5>
                            <div class="row g-3">
                                @if ($emailApplication->final_assigned_email)
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('Akaun E-mel MOTAC') }}:</strong> {{ $emailApplication->final_assigned_email }}</p></div>
                                @endif
                                @if ($emailApplication->final_assigned_user_id)
                                <div class="col-md-6"><p class="mb-1"><strong class="fw-semibold">{{ __('ID Pengguna Ditetapkan') }}:</strong> {{ $emailApplication->final_assigned_user_id }}</p></div>
                                @endif
                            </div>
                        </section>
                        @endif
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        @if ($emailApplication->isDraft() && Auth::id() == $emailApplication->user_id)
                            @can('update', $emailApplication)
                            <a href="{{ route('email-applications.edit', $emailApplication) }}" class="btn btn-warning btn-sm" wire:navigate>{{ __('Edit Permohonan') }}</a>
                            @endcan
                            @can('delete', $emailApplication)
                            <form action="{{ route('email-applications.destroy', $emailApplication) }}" method="POST" onsubmit="return confirm('{{ __('Anda pasti ingin membuang draf permohonan ini?') }}');" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Buang Draf') }}</button>
                            </form>
                            @endcan
                        @endif
                        {{-- Other conditional action buttons for different statuses and roles --}}
                        <a href="{{ route('email-applications.index') }}" class="btn btn-outline-secondary btn-sm" wire:navigate>{{ __('Kembali ke Senarai') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
