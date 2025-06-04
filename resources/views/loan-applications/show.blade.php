{{-- resources/views/loan-applications/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Permohonan Pinjaman ICT #') . $loanApplication->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold text-dark mb-1">
                            {{ __('Butiran Permohonan Pinjaman ICT') }}
                        </h1>
                        <p class="text-muted mb-0">{{ __('ID Permohonan') }}: #{{ $loanApplication->id }}</p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        {{-- Ensure x-resource-status-panel expects :resource and optionally statusAttribute --}}
                        {{-- UPDATED: Added type="loan_application" --}}
                        <x-resource-status-panel :resource="$loanApplication" statusAttribute="status" type="loan_application" {{-- This model has getStatusLabelAttribute --}}
                            class="fs-5 px-3 py-2 shadow-sm" :showIcon="true" />
                    </div>
                </div>

                {{-- Removed: @include('partials.alert-messages') --}}
                {{-- Assuming session messages are handled globally by _partials._alerts.alert-general.blade.php --}}

                @if ($loanApplication->status === \App\Models\LoanApplication::STATUS_REJECTED && $loanApplication->rejection_reason)
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 mb-4 shadow-sm rounded-3">
                        <h5 class="alert-heading h6 text-danger-emphasis fw-semibold"><i
                                class="bi bi-x-octagon-fill me-2"></i>{{ __('Sebab Penolakan') }}:</h5>
                        <p class="mb-0 text-danger-emphasis small" style="white-space: pre-wrap;">
                            {{ e($loanApplication->rejection_reason) }}</p>
                    </div>
                @endif

                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-sm-4 p-3 vstack gap-4">
                        {{-- Section 1: Applicant Information --}}
                        <section aria-labelledby="applicant-information">
                            <h2 id="applicant-information" class="h5 fw-semibold text-dark mb-3 border-bottom pb-2">
                                {{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h2>
                            @if ($loanApplication->user)
                                {{-- Ensure resources/views/components/user-info-card.blade.php exists --}}
                                <x-user-info-card :user="$loanApplication->user" :application="$loanApplication" title="" />
                            @else
                                <p class="text-muted small">{{ __('Maklumat pemohon tidak tersedia.') }}</p>
                            @endif
                        </section>

                        {{-- Loan Details (Part 1 continued) --}}
                        <section aria-labelledby="loan-details">
                            <h2 id="loan-details" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('BUTIRAN PERMOHONAN PINJAMAN') }}</h2>
                            <dl class="row g-3 small">
                                <dt class="col-sm-12 fw-medium text-muted">{{ __('Tujuan Permohonan:') }}</dt>
                                <dd class="col-sm-12 text-dark bg-light p-2 rounded-2 border"
                                    style="white-space: pre-wrap;">{{ e($loanApplication->purpose ?? 'N/A') }}</dd>

                                <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('Lokasi Penggunaan Peralatan:') }}
                                </dt>
                                <dd class="col-md-8 col-lg-9 text-dark">{{ e($loanApplication->location ?? 'N/A') }}</dd>

                                <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('Lokasi Pemulangan:') }}</dt>
                                <dd class="col-md-8 col-lg-9 text-dark">
                                    {{ e($loanApplication->return_location ?? (e($loanApplication->location) ?: 'N/A')) }}
                                </dd>

                                <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh & Masa Pinjaman:') }}</dt>
                                <dd class="col-md-8 col-lg-9 text-dark">
                                    {{ optional($loanApplication->loan_start_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}
                                </dd>

                                <dt class="col-md-4 col-lg-3 fw-medium text-muted">
                                    {{ __('Tarikh & Masa Dijangka Pulang:') }}</dt>
                                <dd class="col-md-8 col-lg-9 text-dark">
                                    {{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}
                                </dd>

                                <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('Tarikh Permohonan Dihantar:') }}
                                </dt>
                                <dd class="col-md-8 col-lg-9 text-dark">
                                    {{ optional($loanApplication->submitted_at)->translatedFormat('d M Y, h:i A') ?? optional($loanApplication->created_at)->translatedFormat('d M Y, h:i A') . ($loanApplication->isDraft() ? ' (Draf)' : ' (Belum Dihantar)') }}
                                </dd>
                            </dl>
                        </section>

                        {{-- Section 2: Responsible Officer --}}
                        <section aria-labelledby="responsible-officer-information">
                            <h2 id="responsible-officer-information"
                                class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h2>
                            @if ($loanApplication->responsibleOfficer && $loanApplication->responsibleOfficer->id !== $loanApplication->user_id)
                                <x-user-info-card :user="$loanApplication->responsibleOfficer" title="" />
                            @elseif(optional($loanApplication->responsibleOfficer)->id === $loanApplication->user_id ||
                                    !$loanApplication->responsibleOfficer)
                                <p class="text-muted small"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}
                                </p>
                            @else
                                <p class="text-muted small"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Maklumat Pegawai Bertanggungjawab tidak dinyatakan.') }}
                                </p>
                            @endif
                        </section>

                        {{-- Supporting Officer --}}
                        <section aria-labelledby="supporting-officer-info">
                            <h2 id="supporting-officer-info" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h2>
                            @if ($loanApplication->supportingOfficer)
                                <x-user-info-card :user="$loanApplication->supportingOfficer" title="" />
                            @else
                                <p class="text-muted small"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Tiada Pegawai Penyokong ditetapkan untuk permohonan ini.') }}
                                </p>
                            @endif
                        </section>

                        {{-- Section 3: Requested Equipment --}}
                        <section aria-labelledby="equipment-requested">
                            <h2 id="equipment-requested" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON') }}</h2>
                            @if ($loanApplication->loanApplicationItems->count() > 0)
                                <div class="table-responsive shadow-sm border rounded-3">
                                    <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">Bil.</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Jenis Peralatan') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2 text-center">
                                                    {{ __('Dimohon') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2 text-center">
                                                    {{ __('Diluluskan') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2 text-center">
                                                    {{ __('Dikeluarkan') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Catatan Pemohon') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($loanApplication->loanApplicationItems as $item)
                                                <tr>
                                                    <td class="small text-muted ps-3">{{ $loop->iteration }}.</td>
                                                    <td class="small text-dark">
                                                        {{ e(\App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) }}
                                                    </td>
                                                    <td class="small text-dark text-center">
                                                        {{ $item->quantity_requested ?? '0' }}</td>
                                                    <td class="small text-dark text-center">
                                                        {{ $item->quantity_approved ?? '-' }}</td>
                                                    <td class="small text-dark text-center">
                                                        {{ $item->quantity_issued ?? '0' }}</td>
                                                    <td class="small text-muted" style="white-space: pre-wrap;">
                                                        {{ e($item->notes ?? '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted small"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Tiada item peralatan dimohon untuk permohonan ini.') }}
                                </p>
                            @endif
                        </section>

                        {{-- Section 4: Applicant Confirmation --}}
                        <section aria-labelledby="applicant-confirmation">
                            <h2 id="applicant-confirmation" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON') }}</h2>
                            <div class="small">
                                @if ($loanApplication->applicant_confirmation_timestamp)
                                    <span class="fw-semibold text-success"><i
                                            class="bi bi-check-circle-fill me-1"></i>{{ __('Telah Disahkan oleh Pemohon') }}</span>
                                    <span class="text-dark"> {{ __('pada') }}
                                        {{ optional($loanApplication->applicant_confirmation_timestamp)->translatedFormat('d M Y, h:i A') }}</span>
                                @else
                                    <span class="fw-semibold text-warning"><i
                                            class="bi bi-hourglass-split me-1"></i>{{ __('Belum Disahkan oleh Pemohon') }}</span>
                                @endif
                            </div>
                        </section>

                        {{-- Approval History --}}
                        <section aria-labelledby="approval-history">
                            <h2 id="approval-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('SEJARAH KELULUSAN & TINDAKAN') }}</h2>
                            @if ($loanApplication->approvals->count() > 0)
                                <div class="table-responsive shadow-sm border rounded-3">
                                    <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">
                                                    {{ __('Peringkat') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Pegawai') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Status') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Catatan') }}</th>
                                                <th class="small text-uppercase text-muted fw-semibold py-2">
                                                    {{ __('Tarikh Tindakan') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($loanApplication->approvals->sortBy('created_at') as $approval)
                                                <tr>
                                                    {{-- Ensure \App\Models\Approval::getStageDisplayName() method exists --}}
                                                    <td class="small text-dark ps-3">
                                                        {{ e(\App\Models\Approval::getStageDisplayName($approval->stage)) ?? __('N/A') }}
                                                    </td>
                                                    <td class="small text-dark">
                                                        {{ e(optional($approval->officer)->name ?? 'N/A') }}</td>
                                                    <td class="small">
                                                        {{-- Ensure resources/views/components/approval-status-badge.blade.php exists --}}
                                                        <x-approval-status-badge :status="$approval->status" />
                                                    </td>
                                                    <td class="small text-muted" style="white-space: pre-wrap;">
                                                        {{ e($approval->comments ?? '-') }}</td>
                                                    <td class="small text-muted">
                                                        {{ optional($approval->approval_timestamp ?? $approval->updated_at)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted small fst-italic"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Tiada sejarah kelulusan untuk permohonan ini.') }}
                                </p>
                            @endif
                        </section>

                        {{-- Loan Transaction History --}}
                        <section aria-labelledby="transaction-history">
                            <h2 id="transaction-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">
                                {{ __('SEJARAH TRANSAKSI PINJAMAN') }}</h2>
                            @if ($loanApplication->loanTransactions->count() > 0)
                                <div class="vstack gap-3">
                                    @foreach ($loanApplication->loanTransactions->sortBy('transaction_date') as $transaction)
                                        <div class="card shadow-sm border">
                                            <div class="card-body p-3">
                                                <div
                                                    class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                                    <h3 class="h6 card-title mb-0 text-dark fw-medium">
                                                        {{ __('Transaksi') }} #{{ $transaction->id }} - <span
                                                            class="fw-normal">{{-- Ensure \App\Models\LoanTransaction::getTypeLabel() method exists --}}{{ e(__(\App\Models\LoanTransaction::getTypeLabel($transaction->type))) }}</span>
                                                    </h3>
                                                    {{-- UPDATED: Added type="loan_transaction" --}}
                                                    <x-resource-status-panel :resource="$transaction" statusAttribute="status"
                                                        type="loan_transaction" class="fs-6 px-2 py-1" :showIcon="true" />
                                                </div>
                                                <p class="small text-muted mb-2">{{ __('Tarikh Transaksi') }}:
                                                    {{ optional($transaction->transaction_date)->translatedFormat('d M Y, h:i A') }}
                                                </p>
                                                <div class="small text-muted mb-2">
                                                    @if ($transaction->isIssue())
                                                        {{-- Ensure isIssue() method on LoanTransaction model --}}
                                                        <p class="mb-1"><strong
                                                                class="fw-medium">{{ __('Pegawai Pengeluar (BPM):') }}</strong>
                                                            {{ e(optional($transaction->issuingOfficer)->name ?? 'N/A') }}
                                                        </p>
                                                        <p class="mb-0"><strong
                                                                class="fw-medium">{{ __('Pegawai Penerima (Pemohon/Wakil):') }}</strong>
                                                            {{ e(optional($transaction->receivingOfficer)->name ?? 'N/A') }}
                                                        </p>
                                                    @elseif($transaction->isReturn())
                                                        {{-- Ensure isReturn() method on LoanTransaction model --}}
                                                        <p class="mb-1"><strong
                                                                class="fw-medium">{{ __('Pegawai Pemulang (Pemohon/Wakil):') }}</strong>
                                                            {{ e(optional($transaction->returningOfficer)->name ?? 'N/A') }}
                                                        </p>
                                                        <p class="mb-0"><strong
                                                                class="fw-medium">{{ __('Pegawai Terima Pulangan (BPM):') }}</strong>
                                                            {{ e(optional($transaction->returnAcceptingOfficer)->name ?? 'N/A') }}
                                                        </p>
                                                    @endif
                                                </div>

                                                @if ($transaction->loanTransactionItems->count() > 0)
                                                    <h4 class="small fw-bold text-uppercase text-muted mb-1 mt-3">
                                                        {{ __('Item Terlibat:') }}</h4>
                                                    <ul class="list-unstyled small text-dark ps-0 mb-0">
                                                        @foreach ($transaction->loanTransactionItems as $txItem)
                                                            <li class="border-bottom py-1">
                                                                <i class="bi bi-hdd-stack text-secondary me-1"></i>
                                                                {{ e(optional($txItem->equipment)->brand_model_serial ?? (optional($txItem->equipment)->tag_id ?? __('Peralatan ID: ') . $txItem->equipment_id)) }}
                                                                (Qty: {{ $txItem->quantity_transacted }})
                                                                @if ($transaction->isReturn() && $txItem->condition_on_return)
                                                                    {{-- Ensure \App\Models\Equipment::getConditionStatusOptions() method exists --}}
                                                                    <span
                                                                        class="ms-2 text-muted">({{ __('Keadaan Semasa Pulang') }}:
                                                                        {{ e(\App\Models\Equipment::getConditionStatusOptions()[$txItem->condition_on_return] ?? Str::title(str_replace('_', ' ', $txItem->condition_on_return))) }})</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                @if ($transaction->issue_notes && $transaction->isIssue())
                                                    <p class="mt-2 small text-muted mb-1"><strong
                                                            class="fw-medium">{{ __('Nota Pengeluaran:') }}</strong> <span
                                                            style="white-space: pre-wrap;">{{ e($transaction->issue_notes) }}</span>
                                                    </p>
                                                @endif
                                                @if ($transaction->return_notes && $transaction->isReturn())
                                                    <p class="mt-2 small text-muted mb-1"><strong
                                                            class="fw-medium">{{ __('Nota Pemulangan:') }}</strong> <span
                                                            style="white-space: pre-wrap;">{{ e($transaction->return_notes) }}</span>
                                                    </p>
                                                @endif
                                                @can('view', $transaction)
                                                    <div class="text-end mt-2">
                                                        <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}"
                                                            class="btn btn-sm btn-link text-decoration-none p-0 small">
                                                            {{ __('Lihat Butiran Transaksi Penuh') }} <i
                                                                class="bi bi-arrow-right-short"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small fst-italic"><i
                                        class="bi bi-info-circle me-1"></i>{{ __('Tiada sejarah transaksi untuk permohonan ini.') }}
                                </p>
                            @endif
                        </section>

                        {{-- Action Buttons --}}
                        <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-center gap-2">
                            <a href="{{ route('loan-applications.index') }}"
                                class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="bi bi-arrow-left-circle me-1"></i> {{ __('Kembali ke Senarai') }}
                            </a>

                            @can('update', $loanApplication)
                                @if ($loanApplication->isDraft() || $loanApplication->isRejected())
                                    <a href="{{ route('loan-applications.edit', $loanApplication) }}"
                                        class="btn btn-info d-inline-flex align-items-center">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        {{ $loanApplication->isRejected() ? __('Edit & Hantar Semula') : __('Kemaskini Draf') }}
                                    </a>
                                @endif
                            @endcan

                            @can('submit', $loanApplication)
                                @if ($loanApplication->isDraft() || $loanApplication->isRejected())
                                    <form action="{{ route('loan-applications.submit', $loanApplication) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('{{ __('Adakah anda pasti untuk menghantar permohonan ini untuk kelulusan?') }}');">
                                        @csrf
                                        <button type="submit" class="btn btn-success d-inline-flex align-items-center">
                                            <i class="bi bi-send-check-fill me-1"></i>
                                            {{ $loanApplication->isRejected() ? __('Hantar Semula Permohonan') : __('Hantar untuk Kelulusan') }}
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('processIssuance', $loanApplication)
                                @if ($loanApplication->canBeIssued())
                                    <a href="{{ route('resource-management.bpm.loan-transactions.issue.form', $loanApplication) }}"
                                        class="btn btn-warning d-inline-flex align-items-center">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> {{ __('Proses Pengeluaran Peralatan') }}
                                    </a>
                                @endif
                            @endcan

                            @php
                                $relevantIssueTransaction = $loanApplication->getRelevantIssueTransactionForReturn();
                            @endphp
                            @if ($relevantIssueTransaction && $loanApplication->canBeReturned())
                                @can('processReturn', $loanApplication)
                                    <a href="{{ route('resource-management.bpm.loan-transactions.return.form', $relevantIssueTransaction) }}"
                                        class="btn btn-purple d-inline-flex align-items-center">
                                        <i class="bi bi-box-arrow-in-left me-1"></i> {{ __('Proses Pemulangan Peralatan') }}
                                    </a>
                                @endcan
                            @endif

                            @can('delete', $loanApplication)
                                @if ($loanApplication->isDraft())
                                    <form action="{{ route('loan-applications.destroy', $loanApplication) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('{{ __('Adakah anda pasti untuk memadam draf permohonan ini?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center">
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
    </div>
@endsection
