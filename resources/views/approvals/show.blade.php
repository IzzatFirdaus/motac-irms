{{-- resources/views/approvals/show.blade.php --}}
@extends('layouts.app')

@php
    $approvableItem = $approval->approvable; // Renamed for clarity
    $itemTypeDisplay =
        $approvableItem instanceof \App\Models\EmailApplication
            ? __('Permohonan Emel/ID Pengguna')
            : ($approvableItem instanceof \App\Models\LoanApplication
                ? __('Permohonan Pinjaman Peralatan ICT')
                : __('Permohonan Tidak Diketahui'));
@endphp

@section('title', $itemTypeDisplay . ' #' . ($approvableItem->id ?? 'N/A') . ' - ' . __('Butiran Tugasan Kelulusan'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card shadow-lg rounded-4"> {{-- Enhanced shadow and rounding --}}
                    <div class="card-header bg-light py-3 px-3 px-sm-4 border-bottom-0">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                            <h2 class="h5 card-title mb-1 mb-sm-0 fw-semibold text-dark">
                                <i class="bi bi-clipboard-check-fill me-2 text-primary"></i> {{ __('Tugasan Kelulusan') }}
                                #{{ $approval->id }}
                            </h2>
                            <a href="{{ url()->previous(route('approval.dashboard')) }}" {{-- Ensure route('approval.dashboard') is correct --}}
                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center mt-2 mt-sm-0">
                                <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali') }}
                            </a>
                        </div>
                        <p class="small text-muted mb-0 mt-1">
                            {{ __('Peringkat') }}: <span
                                class="fw-medium">{{ \App\Models\Approval::getStageDisplayName($approval->stage) }}</span>
                            <span class="mx-2 text-muted">|</span>
                            {{ __('Status Semasa Tugasan') }}:
                            <x-approval-status-badge :status="$approval->status" />
                        </p>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading fw-bold"><i
                                        class="bi bi-x-octagon-fill me-2"></i>{{ __('Sila perbetulkan ralat berikut:') }}
                                </h5>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li><small>{{ $error }}</small></li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($approvableItem)
                            <section class="mb-4 pb-3 border-bottom">
                                <h3 class="h6 fw-semibold text-dark mb-3">
                                    <i class="bi bi-file-earmark-text me-2"></i>{{ __('Permohonan Berkaitan') }}:
                                    {{ $itemTypeDisplay }} #{{ $approvableItem->id }}
                                </h3>
                                <dl class="row g-2 small">
                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Pemohon') }}:</dt>
                                    <dd class="col-sm-8 col-lg-9 text-dark">
                                        {{ $approvableItem->user->name ?? ($approvableItem->user->full_name ?? 'N/A') }}
                                    </dd>

                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jabatan') }}:</dt>
                                    <dd class="col-sm-8 col-lg-9 text-dark">
                                        {{ $approvableItem->user->department->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jawatan') }}:</dt>
                                    <dd class="col-sm-8 col-lg-9 text-dark">
                                        {{ $approvableItem->user->position->name ?? 'N/A' }} (Gred:
                                        {{ $approvableItem->user->grade->name ?? 'N/A' }})</dd>

                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Permohonan Dihantar') }}:
                                    </dt>
                                    <dd class="col-sm-8 col-lg-9 text-dark">
                                        {{ $approvableItem->created_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>

                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">
                                        {{ __('Status Keseluruhan Permohonan') }}:</dt>
                                    <dd class="col-sm-8 col-lg-9"><x-approval-status-badge :status="$approvableItem->status" /></dd>

                                    @if (property_exists($approvableItem, 'purpose') && !empty($approvableItem->purpose))
                                        <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tujuan') }}:</dt>
                                        <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                            {{ $approvableItem->purpose }}</dd>
                                    @elseif(property_exists($approvableItem, 'application_reason_notes') && !empty($approvableItem->application_reason_notes))
                                        <dt class="col-sm-4 col-lg-3 fw-medium text-muted">
                                            {{ __('Tujuan/Catatan Permohonan') }}:</dt>
                                        <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                            {{ $approvableItem->application_reason_notes }}</dd>
                                    @endif

                                    @if ($approvableItem instanceof \App\Models\EmailApplication)
                                        <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Cadangan Emel/ID') }}:
                                        </dt>
                                        <dd class="col-sm-8 col-lg-9 text-dark">
                                            {{ $approvableItem->proposed_email ?? ($approvableItem->group_email ?? 'N/A') }}
                                        </dd>
                                    @elseif($approvableItem instanceof \App\Models\LoanApplication)
                                        <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Lokasi Penggunaan') }}:
                                        </dt>
                                        <dd class="col-sm-8 col-lg-9 text-dark">{{ $approvableItem->location }}</dd>
                                        <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tempoh Pinjaman') }}:
                                        </dt>
                                        <dd class="col-sm-8 col-lg-9 text-dark">
                                            {{ \Carbon\Carbon::parse($approvableItem->loan_start_date)->translatedFormat('d M Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($approvableItem->loan_end_date)->translatedFormat('d M Y') }}
                                        </dd>
                                        @if ($approvableItem->relationLoaded('applicationItems') && $approvableItem->applicationItems->isNotEmpty())
                                            <dt class="col-12 fw-medium text-muted mt-2">{{ __('Item Dipohon') }}:</dt>
                                            <dd class="col-12">
                                                <ul class="list-unstyled ps-1 mb-0">
                                                    @foreach ($approvableItem->applicationItems as $item)
                                                        <li class="mb-1"><i
                                                                class="bi bi-caret-right-fill text-secondary me-1 small"></i>{{ $item->equipment_type }}
                                                            (Qty: {{ $item->quantity_requested }})
                                                            @if ($item->notes)
                                                                <span class="text-muted fst-italic d-block ps-3">-
                                                                    {{ $item->notes }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </dd>
                                        @endif
                                    @endif
                                    @if (property_exists($approvableItem, 'rejection_reason') && $approvableItem->rejection_reason)
                                        <div class="col-12 mt-2">
                                            <div
                                                class="alert alert-warning bg-warning-subtle border-warning-subtle p-2 small">
                                                <strong class="text-warning-emphasis d-block mb-1"><i
                                                        class="bi bi-exclamation-octagon-fill me-1"></i>{{ __('Sebab Permohonan Terdahulu Ditolak') }}:</strong>
                                                <p class="mb-0" style="white-space: pre-wrap;">
                                                    {{ $approvableItem->rejection_reason }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </dl>
                            </section>
                        @else
                            <div class="alert alert-warning small" role="alert">
                                <i
                                    class="bi bi-exclamation-triangle me-1"></i>{{ __('Butiran permohonan berkaitan tidak tersedia atau telah dipadam.') }}
                            </div>
                        @endif

                        <section class="mb-4">
                            <h3 class="h6 fw-semibold text-dark mb-3"><i
                                    class="bi bi-person-check me-2"></i>{{ __('Butiran Tugasan Kelulusan Ini') }}</h3>
                            <dl class="row g-2 small">
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Pegawai Ditugaskan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $approval->officer->name ?? ($approval->officer->full_name ?? 'N/A') }}</dd>
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Tugasan Dicipta') }}:</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $approval->created_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                                @if ($approval->approval_timestamp)
                                    <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Keputusan Dibuat Pada') }}:
                                    </dt>
                                    <dd class="col-sm-8 col-lg-9 text-dark">
                                        {{ \Carbon\Carbon::parse($approval->approval_timestamp)->translatedFormat('d M Y, h:i A') }}
                                    </dd>
                                @endif
                                @if ($approval->comments)
                                    <dt class="col-12 fw-medium text-muted mt-2">
                                        {{ __('Catatan Pegawai Pelulus Terdahulu/Semasa') }}:</dt>
                                    <dd class="col-12">
                                        <div class="p-2 bg-light border rounded" style="white-space: pre-wrap;">
                                            {{ $approval->comments }}</div>
                                    </dd>
                                @endif
                            </dl>
                        </section>

                        @if ($approval->status === \App\Models\Approval::STATUS_PENDING)
                            @can('update', $approval)
                                {{-- Assumes an 'update' policy for Approval model --}}
                                <section class="mt-4 pt-4 border-top">
                                    <h3 class="h6 fw-semibold text-dark mb-3"><i
                                            class="bi bi-pencil-fill me-2"></i>{{ __('Rekod Keputusan Anda') }}</h3>
                                    <form action="{{ route('approvals.decision.store', $approval->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="decision" class="form-label fw-medium">{{ __('Keputusan') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="decision" id="decision" required
                                                class="form-select @error('decision') is-invalid @enderror">
                                                <option value="">-- {{ __('Pilih Keputusan') }} --</option>
                                                <option value="{{ \App\Models\Approval::STATUS_APPROVED }}"
                                                    {{ old('decision') == \App\Models\Approval::STATUS_APPROVED ? 'selected' : '' }}>
                                                    {{ __('Luluskan') }}</option>
                                                <option value="{{ \App\Models\Approval::STATUS_REJECTED }}"
                                                    {{ old('decision') == \App\Models\Approval::STATUS_REJECTED ? 'selected' : '' }}>
                                                    {{ __('Tolak') }}</option>
                                            </select>
                                            @error('decision')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="comments"
                                                class="form-label fw-medium">{{ __('Catatan Tambahan (Jika Ada)') }} <span
                                                    id="comments_required_star" class="text-danger fst-italic"
                                                    style="display:none;">*
                                                    {{ __('Wajib diisi jika ditolak') }}</span></label>
                                            <textarea name="comments" id="comments" rows="4" class="form-control @error('comments') is-invalid @enderror"
                                                placeholder="{{ __('Sila berikan justifikasi jika menolak permohonan.') }}">{{ old('comments') }}</textarea>
                                            @error('comments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="text-end">
                                            <button type="submit"
                                                class="btn btn-primary d-inline-flex align-items-center px-4">
                                                <i class="bi bi-check-circle-fill me-2"></i> {{ __('Hantar Keputusan') }}
                                            </button>
                                        </div>
                                    </form>
                                </section>
                            @else
                                <div class="alert alert-secondary small" role="alert">
                                    <i
                                        class="bi bi-info-circle me-1"></i>{{ __('Anda tidak dibenarkan untuk mengambil tindakan ke atas tugasan ini, atau tindakan telah pun diambil.') }}
                                </div>
                            @endcan
                        @else
                            <div class="alert alert-info small" role="alert">
                                <i
                                    class="bi bi-info-circle-fill me-1"></i>{{ __('Tugasan kelulusan ini telah selesai diproses.') }}
                            </div>
                        @endif
                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div> {{-- col --}}
        </div> {{-- row --}}
    </div> {{-- container --}}

    @push('custom-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const decisionSelect = document.getElementById('decision');
                const commentsInput = document.getElementById('comments'); // Get the textarea itself
                const commentsRequiredStar = document.getElementById('comments_required_star');

                if (decisionSelect && commentsRequiredStar && commentsInput) {
                    const toggleCommentsRequired = () => {
                        if (decisionSelect.value === '{{ \App\Models\Approval::STATUS_REJECTED }}') {
                            commentsRequiredStar.style.display = 'inline';
                            commentsInput.setAttribute('required', 'required'); // Make textarea truly required
                        } else {
                            commentsRequiredStar.style.display = 'none';
                            commentsInput.removeAttribute('required'); // Remove required attribute
                        }
                    };
                    decisionSelect.addEventListener('change', toggleCommentsRequired);
                    toggleCommentsRequired(); // Initial check on page load
                }
            });
        </script>
    @endpush
@endsection
