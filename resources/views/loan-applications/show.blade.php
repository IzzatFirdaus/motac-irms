@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('loan-applications.title_with_id', ['id' => $loanApplication->id]))
=======
@section('title', __('Butiran Permohonan Pinjaman ICT #') . $loanApplication->id)
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
<<<<<<< HEAD
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold text-body mb-1 d-flex align-items-center">
                        <i class="bi bi-file-earmark-medical-fill text-primary me-2"></i>{{ __('loan-applications.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('loan-applications.labels.application_id') }}: #{{ $loanApplication->id }}</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <x-resource-status-panel :resource="$loanApplication" statusAttribute="status" type="loan-application"
                        class="fs-5 px-3 py-2 shadow-sm" :showIcon="true" />
                </div>
            </div>

            @include('_partials._alerts.alert-general')

            @if ($loanApplication->isRejected() && $loanApplication->rejection_reason)
                <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 mb-4 shadow-sm rounded-3">
                    <h5 class="alert-heading h6 text-danger-emphasis fw-semibold">
                        <i class="bi bi-x-octagon-fill me-2"></i>{{ __('loan-applications.labels.rejection_reason') }}:
                    </h5>
                    <p class="mb-0 text-danger-emphasis small" style="white-space: pre-wrap;">{{ $loanApplication->rejection_reason }}</p>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-sm-4 p-3 vstack gap-4">
                    {{-- Section 1: Applicant Information --}}
                    <section aria-labelledby="applicant-information">
                        <h2 id="applicant-information" class="h5 fw-semibold text-body mb-3 border-bottom pb-2">{{ __('loan-applications.sections.applicant') }}</h2>
                        @if ($loanApplication->user)
                            <x-user-info-card :user="$loanApplication->user" :application="$loanApplication" title="" />
                        @else
                            <p class="text-muted small">N/A</p>
                        @endif
                    </section>

                    {{-- Loan Details --}}
                    <section aria-labelledby="loan-details">
                        <h2 id="loan-details" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.application_details') }}</h2>
                        <dl class="row g-3 small">
                            <dt class="col-sm-12 fw-medium text-muted">{{ __('loan-applications.labels.purpose') }}</dt>
                            <dd class="col-sm-12 text-body bg-body-secondary p-2 rounded-2 border" style="white-space: pre-wrap;">{{ e($loanApplication->purpose ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.usage_location') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ e($loanApplication->location ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.return_location') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ e($loanApplication->effective_return_location ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.loan_datetime') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->loan_start_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.return_datetime') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.submitted_date') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->submitted_at)->translatedFormat('d M Y, h:i A') ?? optional($loanApplication->created_at)->translatedFormat('d M Y, h:i A') . ($loanApplication->isDraft() ? ' (' . $loanApplication->status_label . ')' : '') }}</dd>
                        </dl>
                    </section>

                    {{-- Section 3: Requested Equipment --}}
                    <section aria-labelledby="equipment-requested">
                        <h2 id="equipment-requested" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.equipment_details') }}</h2>
                        @if ($loanApplication->loanApplicationItems->count() > 0)
                            <div class="table-responsive shadow-sm border rounded-3">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">#</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('loan-applications.labels.equipment_type') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.requested_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.approved_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.issued_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('loan-applications.labels.applicant_notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->loanApplicationItems as $item)
                                            <tr>
                                                <td class="small text-muted ps-3">{{ $loop->iteration }}.</td>
                                                <td class="small text-body">{{ e(\App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_requested ?? '0' }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_approved ?? '-' }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_issued ?? '0' }}</td>
                                                <td class="small text-muted" style="white-space: pre-wrap;">{{ e($item->notes ?? '-') }}</td>
=======

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <h1 class="h2 fw-bold text-dark mb-2 mb-md-0">
                    {{ __('Butiran Permohonan Pinjaman ICT') }} #{{ $loanApplication->id }}
                </h1>
                <div>
                    {{-- Ensure x-resource-status-panel renders a Bootstrap badge --}}
                    <x-resource-status-panel :resource="$loanApplication" statusAttribute="status" class="fs-5 px-3 py-1" />
                </div>
            </div>

            {{-- Session Messages --}}
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>
                    <strong>{{ __('Berjaya') }}:</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <strong>{{ __('Ralat') }}:</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Rejection Reason --}}
            @if ($loanApplication->status === \App\Models\LoanApplication::STATUS_REJECTED && $loanApplication->rejection_reason)
                <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 mb-4">
                    <h5 class="alert-heading h6 text-danger">{{ __('Sebab Penolakan') }}:</h5>
                    <p class="mb-0 text-danger-emphasis" style="white-space: pre-wrap;">{{ $loanApplication->rejection_reason }}</p>
                </div>
            @endif

            <div class="card shadow-lg rounded-3">
                <div class="card-body p-3 p-sm-4">
                    {{-- Section 1: Applicant Information --}}
                    <section aria-labelledby="applicant-information" class="mb-4">
                        <h3 id="applicant-information" class="h5 fw-semibold text-dark mb-3 border-bottom pb-2">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h3>
                        @if($loanApplication->user)
                            {{-- Ensure x-user-info-card renders Bootstrap-styled content --}}
                            <x-user-info-card :user="$loanApplication->user" title=""/>
                        @else
                            <p class="text-muted small">{{ __('Maklumat pemohon tidak tersedia.') }}</p>
                        @endif
                    </section>

                    {{-- Loan Details (Part 1 continued) --}}
                    <section aria-labelledby="loan-details" class="mb-4">
                        <h3 id="loan-details" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BUTIRAN PINJAMAN') }}</h3>
                        <div class="row g-3 small">
                            <div class="col-md-12">
                                <div class="fw-medium text-muted">{{ __('Tujuan Permohonan:') }}</div>
                                <p class="text-dark" style="white-space: pre-wrap;">{{ $loanApplication->purpose ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Lokasi Penggunaan Peralatan:') }}</div>
                                <p class="text-dark">{{ $loanApplication->location ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Lokasi Pemulangan:') }}</div>
                                <p class="text-dark">{{ $loanApplication->return_location ?? ($loanApplication->location ?? 'N/A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Tarikh Pinjaman:') }}</div>
                                <p class="text-dark">{{ optional($loanApplication->loan_start_date)->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Tarikh Dijangka Pulang:') }}</div>
                                <p class="text-dark">{{ optional($loanApplication->loan_end_date)->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Responsible Officer --}}
                    <section aria-labelledby="responsible-officer-information" class="mb-4">
                        <h3 id="responsible-officer-information" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h3>
                        @if ($loanApplication->responsibleOfficer && $loanApplication->responsibleOfficer->id !== $loanApplication->user_id)
                            <x-user-info-card :user="$loanApplication->responsibleOfficer" title=""/>
                        @else
                            <p class="text-muted small">{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</p>
                        @endif
                    </section>

                    @if($loanApplication->supportingOfficer)
                    <section aria-labelledby="supporting-officer-info" class="mb-4">
                        <h3 id="supporting-officer-info" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h3>
                        <x-user-info-card :user="$loanApplication->supportingOfficer" title=""/>
                    </section>
                    @endif

                    {{-- Section 3: Requested Equipment --}}
                    <section aria-labelledby="equipment-requested" class="mb-4">
                        <h3 id="equipment-requested" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON') }}</h3>
                        @if ($loanApplication->applicationItems->count() > 0)
                            <div class="table-responsive shadow-sm border rounded">
                                <table class="table table-sm table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-medium ps-3">Bil.</th>
                                            <th class="small text-uppercase text-muted fw-medium">Jenis Peralatan</th>
                                            <th class="small text-uppercase text-muted fw-medium text-center">Kuantiti Dimohon</th>
                                            <th class="small text-uppercase text-muted fw-medium text-center">Kuantiti Diluluskan</th>
                                            <th class="small text-uppercase text-muted fw-medium text-center">Kuantiti Dikeluarkan</th>
                                            <th class="small text-uppercase text-muted fw-medium">Catatan Pemohon</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->applicationItems as $item)
                                            <tr>
                                                <td class="small text-muted ps-3 align-middle">{{ $loop->iteration }}</td>
                                                <td class="small text-dark align-middle">{{ $item->equipment_type ?? 'N/A' }}</td>
                                                <td class="small text-dark text-center align-middle">{{ $item->quantity_requested ?? 'N/A' }}</td>
                                                <td class="small text-dark text-center align-middle">{{ $item->quantity_approved ?? '-' }}</td>
                                                <td class="small text-dark text-center align-middle">{{ $item->quantity_issued ?? '0' }}</td>
                                                <td class="small text-muted align-middle" style="white-space: pre-wrap;">{{ $item->notes ?? '-' }}</td>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
<<<<<<< HEAD
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('loan-applications.labels.no_equipment_requested') }}</p>
                        @endif
                    </section>

                    {{-- Approval History --}}
                    @if($loanApplication->approvals->isNotEmpty())
                    <section aria-labelledby="approval-history">
                        <h2 id="approval-history" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.approval_history') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="small">{{ __('loan-applications.labels.stage') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.officer') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.status') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.action_date') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.comments') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loanApplication->approvals as $approval)
                                        <tr>
                                            <td class="small">{{ $approval->stage_translated }}</td>
                                            <td class="small">{{ $approval->officer->name ?? '-' }}</td>
                                            <td class="small"><span class="badge {{ $approval->status_color_class }}">{{ $approval->status_translated }}</span></td>
                                            <td class="small">{{ optional($approval->approval_timestamp)->translatedFormat('d/m/Y h:i A') }}</td>
                                            <td class="small text-muted">{{ $approval->comments ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                    @endif

                    {{-- Loan Transaction History --}}
                    @if($loanApplication->loanTransactions->isNotEmpty())
                    <section aria-labelledby="transaction-history">
                        <h2 id="transaction-history" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.transaction_history') }}</h2>
                        <div class="vstack gap-3">
                            @foreach($loanApplication->loanTransactions as $transaction)
                            <div class="p-3 border rounded">
                                <h6 class="fw-semibold"><i class="bi {{ $transaction->isIssue() ? 'bi-box-arrow-up' : 'bi-box-arrow-down' }} me-2"></i>{{ __('loan-applications.labels.transaction') }} #{{$transaction->id}} ({{$transaction->type_label}})</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-4">{{ __('loan-applications.labels.transaction_date') }}</dt>
                                    <dd class="col-sm-8">{{ $transaction->transaction_date->translatedFormat('d M Y, h:i A') }}</dd>

                                    @if($transaction->isIssue())
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.issuing_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->issuingOfficer->name ?? '-' }}</dd>
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.receiving_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->receivingOfficer->name ?? '-' }}</dd>
                                    @else
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.returning_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->returningOfficer->name ?? '-' }}</dd>
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.return_receiving_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->returnAcceptingOfficer->name ?? '-' }}</dd>
                                    @endif
                                </dl>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-center gap-2">
                        @can('update', $loanApplication)
                            <a href="{{ route('loan-applications.edit', $loanApplication) }}" class="btn btn-secondary"><i class="bi bi-pencil-fill me-1"></i> {{ __('loan-applications.update_draft') }}</a>
                        @endcan
                        @can('submit', $loanApplication)
                            <form action="{{ route('loan-applications.submit', $loanApplication) }}" method="POST" onsubmit="return confirm('{{ __('loan-applications.submit_confirm_message') }}')">
                                @csrf
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send-check-fill me-1"></i> {{ __('loan-applications.submit_application') }}</button>
                            </form>
                        @endcan

                        @if ($loanApplication->latest_issue_transaction)
                            @can('processReturn', $loanApplication->latest_issue_transaction)
                                <a href="{{ route('loan-transactions.return.form', $loanApplication->latest_issue_transaction) }}" class="btn btn-success"><i class="bi bi-arrow-down-left-circle-fill me-1"></i> {{ __('loan-applications.process_return') }}</a>
                            @endcan
                        @endif
=======
                            <p class="text-muted small">{{ __('Tiada item peralatan dimohon untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Section 4: Applicant Confirmation --}}
                    <section aria-labelledby="applicant-confirmation" class="mb-4">
                        <h3 id="applicant-confirmation" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON') }}</h3>
                        <div class="small">
                            <span class="fw-medium text-muted">{{ __('Status Pengesahan:') }}</span>
                            @if ($loanApplication->applicant_confirmation_timestamp)
                                <span class="fw-semibold text-success">{{ __('Telah Disahkan') }}</span>
                                <span class="text-dark">pada {{ optional($loanApplication->applicant_confirmation_timestamp)->format('d M Y, H:i A') }}</span>
                            @else
                                <span class="fw-semibold text-danger">{{ __('Belum Disahkan') }}</span>
                            @endif
                        </div>
                    </section>

                    {{-- Approval History --}}
                    <section aria-labelledby="approval-history" class="mb-4">
                        <h3 id="approval-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('SEJARAH KELULUSAN & TINDAKAN') }}</h3>
                         @if ($loanApplication->approvals->count() > 0)
                            <div class="table-responsive shadow-sm border rounded">
                                <table class="table table-sm table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-medium">Peringkat</th>
                                            <th class="small text-uppercase text-muted fw-medium">Pegawai</th>
                                            <th class="small text-uppercase text-muted fw-medium">Status</th>
                                            <th class="small text-uppercase text-muted fw-medium">Catatan</th>
                                            <th class="small text-uppercase text-muted fw-medium">Tarikh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->approvals as $approval)
                                            <tr>
                                                <td class="small text-dark align-middle">{{ Str::title(str_replace('_', ' ', $approval->stage ?? 'N/A')) }}</td>
                                                <td class="small text-dark align-middle">{{ optional($approval->officer)->name ?? 'N/A' }}</td>
                                                <td class="small align-middle">
                                                    {{-- Ensure x-approval-status-badge renders Bootstrap badge --}}
                                                    <x-approval-status-badge :status="$approval->status" />
                                                </td>
                                                <td class="small text-muted align-middle" style="white-space: pre-wrap;">{{ $approval->comments ?? '-' }}</td>
                                                <td class="small text-muted align-middle">{{ optional($approval->approval_timestamp)->format('d M Y, H:i A') ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small">{{ __('Tiada sejarah kelulusan untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Loan Transaction History --}}
                    <section aria-labelledby="transaction-history" class="mb-4">
                        <h3 id="transaction-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('SEJARAH TRANSAKSI PINJAMAN') }}</h3>
                        @if($loanApplication->loanTransactions->count() > 0)
                            <div class="vstack gap-3">
                            @foreach($loanApplication->loanTransactions as $transaction)
                                <div class="card shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="h6 card-title mb-0 text-dark">
                                                {{ __('Transaksi') }} #{{ $transaction->id }} - <span class="fw-normal">{{ __(Str::title($transaction->type)) }}</span>
                                            </h5>
                                            {{-- Ensure x-resource-status-panel renders Bootstrap badge --}}
                                            <x-resource-status-panel :resource="$transaction" statusAttribute="status" />
                                        </div>
                                        <p class="small text-muted mb-2">Tarikh Transaksi: {{ optional($transaction->transaction_date)->format('d M Y, H:i A') }}</p>
                                        <div class="small text-muted mb-2">
                                            @if($transaction->isIssue())
                                                <p><span class="fw-medium">{{ __('Pegawai Pengeluar:') }}</span> {{ optional($transaction->issuingOfficer)->name ?? 'N/A' }}</p>
                                                <p><span class="fw-medium">{{ __('Pegawai Penerima:') }}</span> {{ optional($transaction->receivingOfficer)->name ?? 'N/A' }}</p>
                                            @elseif($transaction->isReturn())
                                                <p><span class="fw-medium">{{ __('Pegawai Pemulang:') }}</span> {{ optional($transaction->returningOfficer)->name ?? 'N/A' }}</p>
                                                <p><span class="fw-medium">{{ __('Pegawai Terima Pulangan:') }}</span> {{ optional($transaction->returnAcceptingOfficer)->name ?? 'N/A' }}</p>
                                            @endif
                                        </div>

                                        @if($transaction->loanTransactionItems->count() > 0)
                                            <h6 class="small fw-bold text-uppercase text-muted mb-1 mt-2">{{ __('Item Terlibat:') }}</h6>
                                            <ul class="list-unstyled small text-dark space-y-1">
                                                @foreach($transaction->loanTransactionItems as $txItem)
                                                    <li>
                                                        <i class="bi bi-caret-right-fill text-secondary me-1"></i>
                                                        {{ optional($txItem->equipment)->tag_id ?? 'N/A' }} - {{ optional($txItem->equipment)->brand }} {{ optional($txItem->equipment)->model }}
                                                        (Qty: {{ $txItem->quantity_transacted }})
                                                        @if($transaction->isReturn())
                                                            <span class="ms-2 text-muted">({{ __('Condition') }}: {{ $txItem->condition_on_return ? Str::title(str_replace('_', ' ', $txItem->condition_on_return)) : 'N/A' }})</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if($transaction->issue_notes && $transaction->isIssue())
                                         <p class="mt-2 small text-muted"><span class="fw-medium">{{ __('Nota Pengeluaran:') }}</span> {{ $transaction->issue_notes }}</p>
                                        @endif
                                        @if($transaction->return_notes && $transaction->isReturn())
                                         <p class="mt-2 small text-muted"><span class="fw-medium">{{ __('Nota Pemulangan:') }}</span> {{ $transaction->return_notes }}</p>
                                        @endif
                                        <div class="text-end mt-2">
                                             <a href="{{ route('resource-management.admin.loan-transactions.show', $transaction->id) }}"
                                                class="btn btn-sm btn-link text-decoration-none p-0 small">
                                                {{ __('Lihat Butiran Transaksi') }} <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @else
                            <p class="text-muted small">{{ __('Tiada sejarah transaksi untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2">
                        <a href="{{ route('resource-management.my-applications.loan.index') }}"
                           class="btn btn-secondary d-inline-flex align-items-center">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                        </a>

                        @can('update', $loanApplication)
                            <a href="{{ route('resource-management.my-applications.loan-applications.edit', $loanApplication) }}"
                               class="btn btn-info d-inline-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i> {{ __('Edit Draf') }}
                            </a>
                        @endcan

                        @can('submit', $loanApplication)
                            <form action="{{-- route('loan-applications.submit', $loanApplication) --}}" method="POST" class="d-inline" onsubmit="return confirm('Adakah anda pasti untuk menghantar permohonan ini?');">
                                 @csrf
                                {{-- <input type="hidden" name="loan_application_id_to_submit" value="{{ $loanApplication->id }}"> --}}
                                <button type="submit" class="btn btn-success d-inline-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ __('Hantar Permohonan') }}
                                </button>
                            </form>
                        @endcan

                        @can('processIssuance', $loanApplication)
                             <a href="{{ route('resource-management.admin.loan-transactions.issue.form', $loanApplication) }}"
                               class="btn btn-warning d-inline-flex align-items-center">
                                <i class="bi bi-box-arrow-up-right me-1"></i> {{ __('Proses Pengeluaran Peralatan') }}
                            </a>
                        @endcan

                        @php
                            $relevantIssueTransaction = $loanApplication->loanTransactions()
                                ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE)
                                ->orderBy('transaction_date', 'desc')
                                ->first();
                        @endphp
                        @if ($relevantIssueTransaction)
                            @can('processReturn', $loanApplication)
                                 <a href="{{ route('resource-management.admin.loan-transactions.return.form', $relevantIssueTransaction) }}"
                                   class="btn btn-purple d-inline-flex align-items-center"> {{-- Assuming .btn-purple is custom or replace with standard Bootstrap --}}
                                    <i class="bi bi-box-arrow-in-left me-1"></i> {{ __('Proses Pemulangan Peralatan') }}
                                </a>
                            @endcan
                        @endif

                        @can('delete', $loanApplication)
                            <form action="{{ route('resource-management.my-applications.loan-applications.destroy', $loanApplication) }}" method="POST" class="d-inline" onsubmit="return confirm('Adakah anda pasti untuk memadam draf permohonan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger d-inline-flex align-items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Draf') }}
                                </button>
                            </form>
                        @endcan
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
