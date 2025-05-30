@extends('layouts.app')

@section('title', __('Butiran Permohonan Pinjaman ICT #') . $loanApplication->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <h1 class="h2 fw-bold text-dark mb-2 mb-md-0">
                    {{ __('Butiran Permohonan Pinjaman ICT') }} #{{ $loanApplication->id }}
                </h1>
                <div>
                    {{-- System Design Ref: 6.3 Reusable Blade Components [cite: 158, 172] --}}
                    <x-resource-status-panel :status="$loanApplication->status" class="fs-5 px-3 py-1" />
                </div>
            </div>

            @include('partials.alert-messages') {{-- Assuming you have a partial for session messages --}}

            @if ($loanApplication->status === \App\Models\LoanApplication::STATUS_REJECTED && $loanApplication->rejection_reason)
                <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 mb-4 shadow-sm">
                    <h5 class="alert-heading h6 text-danger fw-semibold"><i class="bi bi-x-octagon-fill me-2"></i>{{ __('Sebab Penolakan') }}:</h5>
                    <p class="mb-0 text-danger-emphasis" style="white-space: pre-wrap;">{{ $loanApplication->rejection_reason }}</p>
                </div>
            @endif

            <div class="card shadow-lg rounded-3 border-0">
                <div class="card-body p-sm-4 p-3">
                    {{-- Section 1: Applicant Information --}}
                    <section aria-labelledby="applicant-information" class="mb-4">
                        <h3 id="applicant-information" class="h5 fw-semibold text-dark mb-3 border-bottom pb-2">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h3>
                        @if($loanApplication->user)
                            {{-- System Design Ref: 6.3 Reusable Blade Components [cite: 158, 172] --}}
                            <x-user-info-card :user="$loanApplication->user" :application="$loanApplication" title=""/>
                        @else
                            <p class="text-muted small">{{ __('Maklumat pemohon tidak tersedia.') }}</p>
                        @endif
                    </section>

                    {{-- Loan Details (Part 1 continued) --}}
                    <section aria-labelledby="loan-details" class="mb-4">
                        <h3 id="loan-details" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BUTIRAN PERMOHONAN PINJAMAN') }}</h3>
                        <div class="row g-3 small">
                            <div class="col-md-12">
                                <div class="fw-medium text-muted">{{ __('Tujuan Permohonan:') }}</div>
                                <p class="text-dark bg-light p-2 rounded-2" style="white-space: pre-wrap;">{{ $loanApplication->purpose ?? 'N/A' }}</p>
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
                                <div class="fw-medium text-muted">{{ __('Tarikh & Masa Pinjaman:') }}</div>
                                <p class="text-dark">{{ optional($loanApplication->loan_start_date)->format('d M Y, H:i A') ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Tarikh & Masa Dijangka Pulang:') }}</div>
                                <p class="text-dark">{{ optional($loanApplication->loan_end_date)->format('d M Y, H:i A') ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-medium text-muted">{{ __('Tarikh Permohonan Dihantar:') }}</div>
                                <p class="text-dark">{{ optional($loanApplication->submitted_at)->format('d M Y, H:i A') ?? (optional($loanApplication->created_at)->format('d M Y, H:i A'). ' (Draf)') }}</p>
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Responsible Officer --}}
                    <section aria-labelledby="responsible-officer-information" class="mb-4">
                        <h3 id="responsible-officer-information" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h3>
                        @if ($loanApplication->responsibleOfficer && $loanApplication->responsibleOfficer->id !== $loanApplication->user_id)
                             <x-user-info-card :user="$loanApplication->responsibleOfficer" title="" />
                        @elseif($loanApplication->responsibleOfficer && $loanApplication->responsibleOfficer->id === $loanApplication->user_id)
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</p>
                        @else
                             <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Maklumat Pegawai Bertanggungjawab tidak dinyatakan atau sama dengan pemohon.') }}</p>
                        @endif
                    </section>

                    @if($loanApplication->supportingOfficer)
                    <section aria-labelledby="supporting-officer-info" class="mb-4">
                        <h3 id="supporting-officer-info" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h3>
                        <x-user-info-card :user="$loanApplication->supportingOfficer" title=""/>
                    </section>
                    @else
                    <section aria-labelledby="supporting-officer-info" class="mb-4">
                        <h3 id="supporting-officer-info" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}</h3>
                        <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Tiada Pegawai Penyokong ditetapkan.') }}</p>
                    </section>
                    @endif


                    {{-- Section 3: Requested Equipment --}}
                    <section aria-labelledby="equipment-requested" class="mb-4">
                        <h3 id="equipment-requested" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON') }}</h3>
                        @if ($loanApplication->applicationItems->count() > 0)
                            <div class="table-responsive shadow-sm border rounded-3">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">Bil.</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Jenis Peralatan') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('Dimohon') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('Diluluskan') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('Dikeluarkan') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Catatan Pemohon') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->applicationItems as $item)
                                            <tr>
                                                <td class="small text-muted ps-3">{{ $loop->iteration }}</td>
                                                <td class="small text-dark">{{ \App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type)) }}</td>
                                                <td class="small text-dark text-center">{{ $item->quantity_requested ?? '0' }}</td>
                                                <td class="small text-dark text-center">{{ $item->quantity_approved ?? '-' }}</td>
                                                <td class="small text-dark text-center">{{ $item->quantity_issued ?? '0' }}</td>
                                                <td class="small text-muted" style="white-space: pre-wrap;">{{ $item->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Tiada item peralatan dimohon untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Section 4: Applicant Confirmation --}}
                    <section aria-labelledby="applicant-confirmation" class="mb-4">
                        <h3 id="applicant-confirmation" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON') }}</h3>
                        <div class="small">
                            @if ($loanApplication->applicant_confirmation_timestamp)
                                <span class="fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ __('Telah Disahkan') }}</span>
                                <span class="text-dark">pada {{ optional($loanApplication->applicant_confirmation_timestamp)->format('d M Y, H:i A') }}</span>
                            @else
                                <span class="fw-semibold text-danger"><i class="bi bi-x-circle-fill me-1"></i>{{ __('Belum Disahkan') }}</span>
                            @endif
                        </div>
                    </section>

                    {{-- Approval History [cite: 96] --}}
                    <section aria-labelledby="approval-history" class="mb-4">
                        <h3 id="approval-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('SEJARAH KELULUSAN & TINDAKAN') }}</h3>
                         @if ($loanApplication->approvals->count() > 0)
                            <div class="table-responsive shadow-sm border rounded-3">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">{{ __('Peringkat') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Pegawai') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Status') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Catatan') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('Tarikh Tindakan') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->approvals->sortBy('created_at') as $approval)
                                            <tr>
                                                <td class="small text-dark ps-3">{{ \App\Models\Approval::getStageLabel($approval->stage) ?? 'N/A' }}</td>
                                                <td class="small text-dark">{{ optional($approval->officer)->name ?? 'N/A' }}</td>
                                                <td class="small">
                                                    <x-approval-status-badge :status="$approval->status" />
                                                </td>
                                                <td class="small text-muted" style="white-space: pre-wrap;">{{ $approval->comments ?? '-' }}</td>
                                                <td class="small text-muted">{{ optional($approval->approval_timestamp ?? $approval->updated_at)->format('d M Y, H:i A') ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Tiada sejarah kelulusan untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Loan Transaction History [cite: 89] --}}
                    <section aria-labelledby="transaction-history" class="mb-4">
                        <h3 id="transaction-history" class="h5 fw-semibold text-dark mb-3 mt-4 border-bottom pb-2">{{ __('SEJARAH TRANSAKSI PINJAMAN') }}</h3>
                        @if($loanApplication->loanTransactions->count() > 0)
                            <div class="vstack gap-3">
                            @foreach($loanApplication->loanTransactions->sortBy('transaction_date') as $transaction)
                                <div class="card shadow-sm border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="h6 card-title mb-0 text-dark">
                                                {{ __('Transaksi') }} #{{ $transaction->id }} - <span class="fw-normal">{{ __(\App\Models\LoanTransaction::getTypeLabel($transaction->type)) }}</span>
                                            </h5>
                                            <x-resource-status-panel :status="$transaction->status" :options="\App\Models\LoanTransaction::getStatusOptions()" />
                                        </div>
                                        <p class="small text-muted mb-2">Tarikh Transaksi: {{ optional($transaction->transaction_date)->format('d M Y, H:i A') }}</p>
                                        <div class="small text-muted mb-2">
                                            @if($transaction->isIssue())
                                                <p class="mb-1"><span class="fw-medium">{{ __('Pegawai Pengeluar (BPM):') }}</span> {{ optional($transaction->issuingOfficer)->name ?? 'N/A' }}</p>
                                                <p class="mb-0"><span class="fw-medium">{{ __('Pegawai Penerima (Pemohon/Wakil):') }}</span> {{ optional($transaction->receivingOfficer)->name ?? 'N/A' }}</p>
                                            @elseif($transaction->isReturn())
                                                <p class="mb-1"><span class="fw-medium">{{ __('Pegawai Pemulang (Pemohon/Wakil):') }}</span> {{ optional($transaction->returningOfficer)->name ?? 'N/A' }}</p>
                                                <p class="mb-0"><span class="fw-medium">{{ __('Pegawai Terima Pulangan (BPM):') }}</span> {{ optional($transaction->returnAcceptingOfficer)->name ?? 'N/A' }}</p>
                                            @endif
                                        </div>

                                        @if($transaction->loanTransactionItems->count() > 0)
                                            <h6 class="small fw-bold text-uppercase text-muted mb-1 mt-3">{{ __('Item Terlibat:') }}</h6>
                                            <ul class="list-unstyled small text-dark ps-0">
                                                @foreach($transaction->loanTransactionItems as $txItem)
                                                    <li class="border-bottom py-1">
                                                        <i class="bi bi-hdd-stack text-secondary me-1"></i>
                                                        {{ optional($txItem->equipment)->brand_model_serial ?? (optional($txItem->equipment)->tag_id ?? __('Peralatan ID: ') . $txItem->equipment_id) }}
                                                        (Qty: {{ $txItem->quantity_transacted }})
                                                        @if($transaction->isReturn())
                                                            <span class="ms-2 text-muted">({{ __('Keadaan Semasa Pulang') }}: {{ $txItem->condition_on_return ? \App\Models\Equipment::getConditionOptions()[$txItem->condition_on_return] ?? Str::title(str_replace('_', ' ',$txItem->condition_on_return)) : 'N/A' }})</span>
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
                                        {{-- According to web.php, admin views this. Regular users might not have this route. --}}
                                        @can('view', $transaction) {{-- Assuming LoanTransactionPolicy exists --}}
                                        <div class="text-end mt-2">
                                             <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}"
                                                class="btn btn-sm btn-link text-decoration-none p-0 small">
                                                {{ __('Lihat Butiran Transaksi Penuh') }} <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        </div>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @else
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('Tiada sejarah transaksi untuk permohonan ini.') }}</p>
                        @endif
                    </section>

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('loan-applications.index') }}" {{-- Or specific MyApplications index --}}
                           class="btn btn-secondary d-inline-flex align-items-center">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                        </a>

                        @can('update', $loanApplication) {{-- Policy: LoanApplicationPolicy@update [cite: 225] --}}
                            <a href="{{ route('loan-applications.edit', $loanApplication) }}" {{-- Or specific MyApplications edit --}}
                               class="btn btn-info d-inline-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i> {{ __('Kemaskini Draf') }}
                            </a>
                        @endcan

                        @can('submit', $loanApplication) {{-- Policy: LoanApplicationPolicy@submit [cite: 225] --}}
                            <form action="{{ route('loan-applications.submit', $loanApplication) }}" method="POST" class="d-inline" onsubmit="return confirm('{{__('Adakah anda pasti untuk menghantar permohonan ini?')}}');">
                                 @csrf
                                <button type="submit" class="btn btn-success d-inline-flex align-items-center">
                                    <i class="bi bi-send-check-fill me-1"></i> {{ $loanApplication->status === \App\Models\LoanApplication::STATUS_REJECTED ? __('Hantar Semula Permohonan') : __('Hantar Permohonan') }}
                                </button>
                            </form>
                        @endcan

                        @can('processIssuance', $loanApplication) {{-- Policy: LoanApplicationPolicy@processIssuance [cite: 225] --}}
                             <a href="{{ route('resource-management.bpm.loan-transactions.issue.form', $loanApplication) }}"
                               class="btn btn-warning d-inline-flex align-items-center">
                                <i class="bi bi-box-arrow-up-right me-1"></i> {{ __('Proses Pengeluaran Peralatan') }}
                            </a>
                        @endcan

                        @php
                            // Find the latest relevant issue transaction to link to a return form
                            $relevantIssueTransaction = $loanApplication->loanTransactions()
                                ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE)
                                ->whereIn('status', [\App\Models\LoanTransaction::STATUS_ISSUED, \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST]) // Only allow return if items were issued
                                ->orderBy('transaction_date', 'desc')
                                ->first();
                        @endphp
                        @if ($relevantIssueTransaction && $loanApplication->status !== \App\Models\LoanApplication::STATUS_RETURNED)
                            @can('processReturn', $loanApplication) {{-- Policy: LoanApplicationPolicy@processReturn [cite: 225] --}}
                                 <a href="{{ route('resource-management.bpm.loan-transactions.return.form', $relevantIssueTransaction) }}"
                                   class="btn btn-purple d-inline-flex align-items-center"> {{-- Custom class or use Bootstrap standard like .btn-info or .btn-success --}}
                                    <i class="bi bi-box-arrow-in-left me-1"></i> {{ __('Proses Pemulangan Peralatan') }}
                                </a>
                            @endcan
                        @endif

                        @can('delete', $loanApplication) {{-- Policy: LoanApplicationPolicy@delete [cite: 225] --}}
                            <form action="{{ route('loan-applications.destroy', $loanApplication) }}" method="POST" class="d-inline" onsubmit="return confirm('{{__('Adakah anda pasti untuk memadam draf permohonan ini?')}}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger d-inline-flex align-items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Draf') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
