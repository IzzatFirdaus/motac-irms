{{-- resources/views/approvals/approvals-show.blade.php --}}
@extends('layouts.app')

@php
    $approvableItem = $approval->approvable; // Variable for easier access
    $itemTypeDisplay = __('Permohonan Tidak Diketahui'); // Default

    $resourceStatusPanelType = 'unknown';
    if ($approvableItem instanceof \App\Models\LoanApplication) {
        $itemTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $resourceStatusPanelType = 'loan_application';
    } elseif ($approvableItem instanceof \App\Models\HelpdeskTicket) {
        $itemTypeDisplay = __('Tiket Meja Bantuan');
        $resourceStatusPanelType = 'helpdesk_ticket';
    }
@endphp

@section('title', $itemTypeDisplay . ' #' . (optional($approvableItem)->id ?? 'N/A') . ' - ' . __('Butiran Tugasan Kelulusan'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card shadow-lg rounded-4 motac-card">
                    <div class="card-header bg-light py-3 px-3 px-sm-4 border-bottom-0 motac-card-header">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                            <h2 class="h5 card-title mb-1 mb-sm-0 fw-semibold text-dark">
                                <i class="bi bi-clipboard-check-fill me-2"></i>{{ __('Butiran Tugasan Kelulusan') }}
                            </h2>
                            <span class="badge bg-info-subtle border border-info-subtle text-info fw-bold py-2 px-3">
                                {{ $itemTypeDisplay }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3 p-sm-4">

                        @include('_partials._alerts.alert-general')

                        <h3 class="h6 fw-bold mb-3 text-dark">{{ __('Maklumat Kelulusan') }}</h3>
                        <dl class="row g-2 small mb-4 pb-2 border-bottom">
                            <dt class="col-sm-4 col-md-3 text-muted">{{ __('ID Tugasan Kelulusan:') }}</dt>
                            <dd class="col-sm-8 col-md-9 text-dark">{{ $approval->id }}</dd>

                            <dt class="col-sm-4 col-md-3 text-muted">{{ __('Dicipta Oleh:') }}</dt>
                            <dd class="col-sm-8 col-md-9 text-dark">
                                {{ $approval->creator->name ?? __('Tidak Diketahui') }}</dd>

                            <dt class="col-sm-4 col-md-3 text-muted">{{ __('Untuk Kelulusan Oleh:') }}</dt>
                            <dd class="col-sm-8 col-md-9 text-dark">
                                {{ $approval->approver->name ?? __('Tidak Diketahui') }}
                                @if ($approval->approver_grade_id)
                                    ({{ $approval->approverGrade->name ?? __('Gred Tidak Diketahui') }})
                                @endif
                            </dd>

                            <dt class="col-sm-4 col-md-3 text-muted">{{ __('Status Semasa:') }}</dt>
                            <dd class="col-sm-8 col-md-9 text-dark">
                                @include('approvals._partials.status-badge', ['status' => $approval->status])
                            </dd>

                            @if ($approval->approved_at)
                                <dt class="col-sm-4 col-md-3 text-muted">{{ __('Diluluskan Pada:') }}</dt>
                                <dd class="col-sm-8 col-md-9 text-dark">
                                    {{ $approval->approved_at->translatedFormat('d M Y, h:i A') }}</dd>
                            @endif

                            @if ($approval->rejected_at)
                                <dt class="col-sm-4 col-md-3 text-muted">{{ __('Ditolak Pada:') }}</dt>
                                <dd class="col-sm-8 col-md-9 text-danger">
                                    {{ $approval->rejected_at->translatedFormat('d M Y, h:i A') }}</dd>
                            @endif

                            @if ($approval->cancelled_at)
                                <dt class="col-sm-4 col-md-3 text-muted">{{ __('Dibatalkan Pada:') }}</dt>
                                <dd class="col-sm-8 col-md-9 text-warning">
                                    {{ $approval->cancelled_at->translatedFormat('d M Y, h:i A') }}</dd>
                            @endif

                            @if ($approval->forwarded_at)
                                <dt class="col-sm-4 col-md-3 text-muted">{{ __('Dimajukan Pada:') }}</dt>
                                <dd class="col-sm-8 col-md-9 text-dark">
                                    {{ $approval->forwarded_at->translatedFormat('d M Y, h:i A') }}</dd>
                            @endif

                            @if ($approval->forwardedApprover)
                                <dt class="col-sm-4 col-md-3 text-muted">{{ __('Dimajukan Kepada:') }}</dt>
                                <dd class="col-sm-8 col-md-9 text-dark">
                                    {{ $approval->forwardedApprover->name ?? __('Tidak Diketahui') }}
                                    @if ($approval->forwarded_approver_grade_id)
                                        ({{ $approval->forwardedApproverGrade->name ?? __('Gred Tidak Diketahui') }})
                                    @endif
                                </dd>
                            @endif
                        </dl>

                                @include('approvals.comments', ['approval' => $approval])

                        @if ($approvableItem)
                            @if ($approvableItem instanceof \App\Models\LoanApplication)
                                @include('loan_applications._partials.loan-summary-panel', [
                                    'loanApplication' => $approvableItem,
                                    'panelTitle' => __('Butiran Permohonan Pinjaman'),
                                ])
                                @elseif ($approvableItem instanceof \App\Models\HelpdeskTicket)
                                {{-- Helpdesk Ticket Details Panel --}}
                                <div class="card shadow-sm mb-4 motac-card">
                                    <div class="card-header bg-light py-3 motac-card-header">
                                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Tiket Meja Bantuan') }}</h2>
                                    </div>
                                    <div class="card-body p-4">
                                        <dl class="row g-3 small">
                                            <dt class="col-sm-4 text-muted">{{ __('Subjek Tiket') }}:</dt>
                                            <dd class="col-sm-8">{{ $approvableItem->subject ?? '-' }}</dd>

                                            <dt class="col-sm-4 text-muted">{{ __('Keterangan') }}:</dt>
                                            <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $approvableItem->description ?? '-' }}</dd>

                                            <dt class="col-sm-4 text-muted">{{ __('Kategori') }}:</dt>
                                            <dd class="col-sm-8">{{ $approvableItem->category->name ?? '-' }}</dd>

                                            <dt class="col-sm-4 text-muted">{{ __('Prioriti') }}:</dt>
                                            <dd class="col-sm-8">{{ $approvableItem->priority->name ?? '-' }}</dd>

                                            <dt class="col-sm-4 text-muted">{{ __('Status Tiket') }}:</dt>
                                            <dd class="col-sm-8">{{ $approvableItem->status_label ?? '-' }}</dd>

                                            <dt class="col-sm-4 text-muted">{{ __('Tarikh Dicipta') }}:</dt>
                                            <dd class="col-sm-8">{{ $approvableItem->created_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning small">
                                {{ __('Item yang berkaitan dengan kelulusan ini tidak ditemui atau telah dipadamkan.') }}
                            </div>
                        @endif

                        @if (
                            $approval->isPending() &&
                                auth()->user()->id === $approval->approver_id &&
                                auth()->user()->can('approve', $approval))
                            <hr class="my-4">
                            <h3 class="h6 fw-bold mb-3 text-dark">{{ __('Ambil Tindakan') }}</h3>
                            <form action="{{ route('approvals.take-action', $approval->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="decision" class="form-label">{{ __('Keputusan') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('decision') is-invalid @enderror"
                                        id="decision" name="decision" required>
                                        <option value="">{{ __('Sila Pilih') }}</option>
                                        <option value="{{ \App\Models\Approval::STATUS_APPROVED }}"
                                            {{ old('decision') == \App\Models\Approval::STATUS_APPROVED ? 'selected' : '' }}>
                                            {{ __('Lulus') }}</option>
                                        <option value="{{ \App\Models\Approval::STATUS_REJECTED }}"
                                            {{ old('decision') == \App\Models\Approval::STATUS_REJECTED ? 'selected' : '' }}>
                                            {{ __('Tolak') }}</option>
                                        <option value="{{ \App\Models\Approval::STATUS_FORWARDED }}"
                                            {{ old('decision') == \App\Models\Approval::STATUS_FORWARDED ? 'selected' : '' }}>
                                            {{ __('Majukan') }}</option>
                                    </select>
                                    @error('decision')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="forward_to_section"
                                    style="display: {{ old('decision') == \App\Models\Approval::STATUS_FORWARDED ? 'block' : 'none' }};">
                                    <label for="forward_approver_id" class="form-label">{{ __('Majukan Kepada') }} <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select form-select-sm @error('forward_approver_id') is-invalid @enderror"
                                        id="forward_approver_id" name="forward_approver_id">
                                        <option value="">-- {{ __('Sila Pilih Pegawai') }} --</option>
                                        @isset($forwardableApprovers)
                                            @foreach ($forwardableApprovers as $approver)
                                                <option value="{{ $approver->id }}"
                                                    {{ old('forward_approver_id') == $approver->id ? 'selected' : '' }}>
                                                    {{ $approver->name }} ({{ $approver->position->name ?? 'N/A' }} -
                                                    {{ $approver->grade->name ?? 'N/A' }})</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('forward_approver_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Conditionally render quantity adjustment section only for Loan Applications --}}
                                @if ($approvableItem instanceof \App\Models\LoanApplication)
                                    <div class="mb-3" id="quantity_adjustment_section"
                                        style="display: {{ old('decision') == \App\Models\Approval::STATUS_APPROVED ? 'block' : 'none' }};">
                                        <h4 class="h6 fw-bold mb-2">{{ __('Pelarasan Kuantiti Kelulusan') }}</h4>
                                        <p class="small text-muted mb-2">
                                            {{ __('Sila laraskan kuantiti yang akan diluluskan untuk setiap peralatan. Kuantiti lalai adalah kuantiti yang diminta.') }}
                                        </p>
                                        @error('approved_quantities')
                                            <div class="alert alert-danger p-2 small">{{ $message }}</div>
                                        @enderror
                                        <div class="table-responsive small">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('Peralatan') }}</th>
                                                        <th class="text-center">{{ __('Diminta') }}</th>
                                                        <th class="text-center">{{ __('Diluluskan') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($approvableItem->requestedEquipment as $index => $item)
                                                        <tr>
                                                            <td>{{ $item->equipment_item->name ?? 'N/A' }}</td>
                                                            <td class="text-center">{{ $item->quantity }}</td>
                                                            <td class="text-center" style="width: 150px;">
                                                                <input type="number"
                                                                    name="approved_quantities[{{ $item->id }}]"
                                                                    class="form-control form-control-sm text-center @error('approved_quantities.' . $item->id) is-invalid @enderror"
                                                                    value="{{ old('approved_quantities.' . $item->id, $item->quantity) }}"
                                                                    min="0" max="{{ $item->quantity }}">
                                                                @error('approved_quantities.' . $item->id)
                                                                    <div class="invalid-feedback d-block">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="comments" class="form-label">
                                        {{ __('Catatan Pegawai') }}
                                        <span class="text-danger" id="comments_required_star" style="display: none;">*</span>
                                    </label>
                                    <textarea class="form-control form-control-sm @error('comments') is-invalid @enderror" id="comments"
                                        name="comments" rows="3" placeholder="{{ __('Sila masukkan catatan anda di sini...') }}">{{ old('comments') }}</textarea>
                                    @error('comments')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text small text-muted">
                                        {{ __('Catatan diperlukan jika permohonan ditolak atau dimajukan.') }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                        <i class="bi bi-send-fill me-2"></i> {{ __('Hantar Keputusan') }}
                                    </button>
                                </div>
                            </form>
                        @elseif ($approval->status === \App\Models\Approval::STATUS_APPROVED)
                            {{-- Specific display for APPROVED loan transactions --}}
                            @if ($approvableItem instanceof \App\Models\LoanApplication && $approvableItem->loanTransaction)
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light py-3">
                                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Transaksi Pinjaman') }}</h2>
                                    </div>
                                    <div class="card-body p-4">
                                        <dl class="row g-3 small">
                                            <dt class="col-sm-4 text-muted">{{ __('ID Transaksi') }}:</dt>
                                            <dd class="col-sm-8"><a
                                                    href="{{ route('resource-management.bpm.loan-transactions.show', $approvableItem->loanTransaction->id) }}"
                                                    class="text-decoration-none fw-semibold">#{{ $approvableItem->loanTransaction->id }}</a>
                                            </dd>
                                            <dt class="col-sm-4 text-muted">{{ __('Pegawai Pengeluar') }}:</dt>
                                            <dd class="col-sm-8">
                                                {{ $approvableItem->loanTransaction->issuer->name ?? __('Tidak Diketahui') }}
                                            </dd>
                                            <dt class="col-sm-4 text-muted">{{ __('Tarikh & Masa Isu Sebenar') }}</dt>
                                            <dd class="col-sm-8">
                                                {{ $approvableItem->loanTransaction->issue_timestamp?->translatedFormat('d M Y, h:i A') ?? __('common.not_available') }}
                                            </dd>
                                            <dt class="col-sm-4 text-muted">{{ __('Aksesori Dikeluarkan') }}:</dt>
                                            <dd class="col-sm-8">
                                                {{ $approvableItem->loanTransaction->accessories_checklist_on_issue ? implode(', ', $approvableItem->loanTransaction->accessories_checklist_on_issue) : '-' }}
                                            </dd>
                                            <dt class="col-sm-4 text-muted">{{ __('Catatan Isu') }}</dt>
                                            <dd class="col-sm-8" style="white-space: pre-wrap;">
                                                {{ $approvableItem->loanTransaction->issue_notes ?? '-' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="text-center mt-4">
                            <a href="{{ route('approvals.index') }}"
                                class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="bi bi-arrow-left-circle me-1"></i> {{ __('Kembali ke Senarai Tugasan') }}
                            </a>
                            @if ($approvableItem)
                                @can('view', $approvableItem)
                                    @php
                                        $backToOriginalRoute = '';
                                        if ($approvableItem instanceof \App\Models\LoanApplication) {
                                            $backToOriginalRoute = route('loan-applications.show', $approvableItem->id);
                                        } elseif ($approvableItem instanceof \App\Models\HelpdeskTicket) {
                                            $backToOriginalRoute = route('helpdesk.view', $approvableItem->id);
                                        }
                                    @endphp
                                    @if ($backToOriginalRoute)
                                        <a href="{{ $backToOriginalRoute }}"
                                            class="btn btn-outline-primary d-inline-flex align-items-center ms-2">
                                            <i class="bi bi-box-arrow-in-up-right me-1"></i> {{ __('Lihat Permohonan Asal') }}
                                        </a>
                                    @endif
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page-script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const decisionSelect = document.getElementById('decision');
                const forwardToSection = document.getElementById('forward_to_section');
                const commentsInput = document.getElementById('comments');
                const commentsRequiredStar = document.getElementById('comments_required_star');
                const quantityAdjustmentSection = document.getElementById('quantity_adjustment_section');

                if (decisionSelect) {
                    const toggleRequiredElements = () => {
                        // Toggle 'Forward To' section visibility
                        if (forwardToSection) {
                            forwardToSection.style.display = decisionSelect.value === '{{ \App\Models\Approval::STATUS_FORWARDED }}' ? 'block' : 'none';
                            // Make 'forward_approver_id' required only if decision is FORWARDED
                            const forwardApproverSelect = document.getElementById('forward_approver_id');
                            if (forwardApproverSelect) {
                                if (decisionSelect.value === '{{ \App\Models\Approval::STATUS_FORWARDED }}') {
                                    forwardApproverSelect.setAttribute('required', 'required');
                                } else {
                                    forwardApproverSelect.removeAttribute('required');
                                }
                            }
                        }

                        // Toggle 'comments' required status and star visibility
                        if (commentsInput && commentsRequiredStar) {
                             if (decisionSelect.value === '{{ \App\Models\Approval::STATUS_REJECTED }}' ||
                                 decisionSelect.value === '{{ \App\Models\Approval::STATUS_FORWARDED }}') {
                                commentsRequiredStar.style.display = 'inline';
                                commentsInput.setAttribute('required', 'required');
                            } else {
                                commentsRequiredStar.style.display = 'none';
                                commentsInput.removeAttribute('required');
                            }
                        }

                        // Toggle 'quantity adjustment section' visibility
                        // This section is only rendered if $approvableItem is a LoanApplication
                        if (quantityAdjustmentSection) {
                            if (decisionSelect.value === '{{ \App\Models\Approval::STATUS_APPROVED }}') {
                                quantityAdjustmentSection.style.display = 'block';
                            } else {
                                quantityAdjustmentSection.style.display = 'none';
                            }
                        }
                    };
                    decisionSelect.addEventListener('change', toggleRequiredElements);
                    toggleRequiredElements(); // Call on page load to set initial state
                }
            });
        </script>
    @endpush
@endsection
