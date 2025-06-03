{{-- resources/views/livewire/resource-management/admin/bpm/issued-loans.blade.php --}}
<div>
    @section('title', __('Senarai Pinjaman Telah Dikeluarkan'))

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">
            {{ __('Senarai Peralatan ICT Telah Dikeluarkan') }}</h1>
        {{-- Add any action buttons here if needed, e.g., Export --}}
    </div>

    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" class="mb-4"/>
    @elseif (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="min-width: 280px;">{{ __('Peralatan Dikeluarkan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Keluar Sebenar') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Jangka Pulang') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Permohonan') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="7" class="p-0">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-valuetext="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($this->issuedLoans as $loanApplication)
                        <tr wire:key="issued-loan-app-{{ $loanApplication->id }}">
                            <td class="px-3 py-2 align-middle small text-dark fw-medium">#{{ $loanApplication->id }}</td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                <span class="fw-medium text-dark">{{ $loanApplication->user?->name ?? __('Tidak Diketahui') }}</span>
                                <span class="d-block" style="font-size: 0.75rem;">{{ $loanApplication->user?->department?->name ?? '' }}</span>
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                @if ($loanApplication->applicationItems->isNotEmpty())
                                    <ul class="list-unstyled ps-2 mb-0" style="font-size: 0.8rem;">
                                        @foreach ($loanApplication->applicationItems as $item)
                                            @if ($item->quantity_issued > 0)
                                                <li>
                                                    <i class="bi bi-chevron-right text-secondary me-1" style="font-size: 0.7rem;"></i>
                                                    {{ $item->equipment_type ? (\App\Models\Equipment::$ASSET_TYPES_LABELS[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) : __('Jenis Tidak Dinyatakan') }}
                                                    ({{ __('Qty:') }} {{ $item->quantity_issued }})
                                                    @if ($item->loanTransactionItems->isNotEmpty())
                                                        <ul class="list-unstyled ps-3 text-body-secondary" style="font-size: 0.75rem;">
                                                            @foreach ($item->loanTransactionItems->filter(fn($transactionItem) => $transactionItem->loanTransaction?->type === \App\Models\LoanTransaction::TYPE_ISSUE) as $transactionItem)
                                                                @if ($transactionItem->equipment)
                                                                    <li>
                                                                        <i class="bi bi-arrow-right-short text-info me-1" style="font-size: 0.8rem;"></i>
                                                                        {{ $transactionItem->equipment->tag_id ?? 'N/A Tag' }} -
                                                                        {{ $transactionItem->equipment->brand ?? 'N/A Brand' }}
                                                                        {{ $transactionItem->equipment->model ?? 'N/A Model' }}
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    {{ __('Tiada item peralatan.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                @php
                                    $latestIssueTransactionDate = $loanApplication->loanTransactions
                                        ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE)
                                        ->sortByDesc('transaction_date')
                                        ->first()?->transaction_date;
                                @endphp
                                {{ $latestIssueTransactionDate ? $latestIssueTransactionDate->translatedFormat(config('app.datetime_format_my', 'd M Y, h:i A')) : __('N/A') }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $loanApplication->loan_end_date ? $loanApplication->loan_end_date->translatedFormat(config('app.date_format_my', 'd M Y')) : __('N/A')}}
                                @if (
                                    $loanApplication->loan_end_date &&
                                    $loanApplication->loan_end_date->isPast() &&
                                    !in_array($loanApplication->status, [
                                        \App\Models\LoanApplication::STATUS_RETURNED,
                                        \App\Models\LoanApplication::STATUS_CANCELLED
                                    ])
                                )
                                    <span class="d-block small fw-bold text-danger mt-1">{{ __('TERTUNGGAK') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 align-middle small">
                                <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($loanApplication->status, 'bootstrap_badge') }}">
                                    {{ $loanApplication->status_translated ?? __(Str::title(str_replace('_', ' ', $loanApplication->status))) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 align-middle text-end">
                                @php
                                    $latestIssueTransaction = $loanApplication->loanTransactions
                                        ->where('type', \App\Models\LoanTransaction::TYPE_ISSUE)
                                        ->sortByDesc('id')
                                        ->first();
                                @endphp
                                @if ($latestIssueTransaction)
                                    <a href="{{ route('resource-management.bpm.loan-transactions.show', $latestIssueTransaction->id) }}"
                                        class="btn btn-sm btn-outline-info border-0 p-1"
                                        title="{{ __('Lihat Detail Transaksi Keluar') }}">
                                        <i class="bi bi-file-earmark-text fs-6 lh-1"></i>
                                    </a>
                                    @if (!in_array($loanApplication->status, [\App\Models\LoanApplication::STATUS_RETURNED, \App\Models\LoanApplication::STATUS_CANCELLED]))
                                        @can('processReturn', $loanApplication)
                                            {{-- ***** EDITED LINE ***** --}}
                                            <a href="{{ route('resource-management.bpm.loan-transactions.return.form', ['loanTransaction' => $latestIssueTransaction->id]) }}"
                                                class="btn btn-sm btn-outline-success border-0 p-1 ms-1"
                                                title="{{ __('Proses Pemulangan') }}">
                                                <i class="bi bi-arrow-return-left fs-6 lh-1"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @else
                                    <a href="{{ route('loan-applications.show', $loanApplication->id) }}"
                                        class="btn btn-sm btn-outline-primary border-0 p-1"
                                        title="{{ __('Lihat Detail Permohonan') }}">
                                        <i class="bi bi-eye fs-6 lh-1"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-folder-x fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada rekod pinjaman yang telah dikeluarkan ditemui.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($this->issuedLoans->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                {{ $this->issuedLoans->links() }}
            </div>
        @endif
    </x-card>
</div>
