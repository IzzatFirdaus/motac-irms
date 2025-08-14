{{-- resources/views/livewire/resource-management/admin/bpm/issued-loans.blade.php --}}
<div>
    <div class="card-body border-bottom">
        <div class="row">
            <div class="col-md-4">
                <label for="searchIssued" class="form-label">{{ __('Carian') }}</label>
                <input type="text" id="searchIssued" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="{{ __('Cari ID, Pemohon, Tag ID...') }}">
            </div>
        </div>
    </div>

    <div wire:loading.remove>
        @if ($this->issuedLoans->isEmpty())
            <div class="text-center p-5">
                <i class="bi bi-info-circle-fill fs-1 text-info"></i>
                <p class="mt-3">
                    @if(empty($searchTerm))
                        {{ __('Tiada peralatan yang sedang dipinjam pada masa ini.') }}
                    @else
                        {{ __('Tiada rekod ditemui untuk carian anda.') }}
                    @endif
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="min-width: 280px;">{{ __('Peralatan Dikeluarkan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Keluar') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Jangka Pulang') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                            <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span>{{ __('Tindakan') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->issuedLoans as $loanApplication)
                            <tr wire:key="issued-loan-app-{{ $loanApplication->id }}">
                                <td class="px-3 py-2 align-middle small text-dark fw-medium">#{{ $loanApplication->id }}</td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <span class="fw-medium text-dark">{{ $loanApplication->user?->name ?? __('Tidak Diketahui') }}</span>
                                    <span class="d-block" style="font-size: 0.75rem;">{{ $loanApplication->user?->department?->name ?? '' }}</span>
                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <ul class="list-unstyled ps-2 mb-0" style="font-size: 0.8rem;">
                                        @foreach ($loanApplication->loanApplicationItems as $item)
                                            @if ($item->quantity_issued > 0)
                                                <li><i class="bi bi-chevron-right text-secondary me-1" style="font-size: 0.7rem;"></i>{{ $item->equipment_type_name }} ({{ __('Qty:') }} {{ $item->quantity_issued }})
                                                    <ul class="list-unstyled ps-3 text-body-secondary" style="font-size: 0.75rem;">
                                                        @foreach ($item->loanTransactionItems->filter(fn($ti) => $ti->loanTransaction?->type === \App\Models\LoanTransaction::TYPE_ISSUE) as $transactionItem)
                                                            @if ($transactionItem->equipment)
                                                                <li><i class="bi bi-arrow-right-short text-info me-1" style="font-size: 0.8rem;"></i>{{ $transactionItem->equipment->tag_id ?? 'N/A' }} - {{ $transactionItem->equipment->brand ?? 'N/A' }} {{ $transactionItem->equipment->model ?? '' }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    {{ $loanApplication->loanTransactions->first()?->transaction_date?->translatedFormat('d M Y') ?? __('N/A') }}
                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    {{ $loanApplication->loan_end_date?->translatedFormat('d M Y') ?? __('N/A')}}
                                    @if ($loanApplication->isOverdue())
                                        <span class="d-block small fw-bold text-danger mt-1">{{ __('TERTUNGGAK') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 align-middle small"><x-resource-status-panel :resource="$loanApplication" statusAttribute="status" /></td>
                                <td class="px-3 py-2 align-middle text-end">
                                    {{-- ***** THIS IS THE FIX ***** --}}
                                    @php
                                        // Get the latest 'issue' transaction from the already loaded collection.
                                        $latestIssueTransaction = $loanApplication->loanTransactions->first();
                                        if ($latestIssueTransaction) {
                                            // Manually set the relation to prevent the lazy loading violation in the policy.
                                            // We can do this because we already have the $loanApplication object.
                                            $latestIssueTransaction->setRelation('loanApplication', $loanApplication);
                                        }
                                    @endphp
                                    @if ($latestIssueTransaction)
                                        <a href="{{ route('loan-transactions.show', $latestIssueTransaction->id) }}" class="btn btn-sm btn-outline-info border-0 p-1" title="{{ __('Lihat Detail Transaksi Keluar') }}"><i class="bi bi-file-earmark-text fs-6 lh-1"></i></a>
                                        @if (!$loanApplication->isClosed())
                                            @can('processReturn', $latestIssueTransaction)
                                                <a href="{{ route('loan-transactions.return.form', ['loanTransaction' => $latestIssueTransaction->id]) }}" class="btn btn-sm btn-outline-success border-0 p-1 ms-1" title="{{ __('Proses Pemulangan') }}"><i class="bi bi-arrow-return-left fs-6 lh-1"></i></a>
                                            @endcan
                                        @endif
                                    @else
                                        <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-sm btn-outline-primary border-0 p-1" title="{{ __('Lihat Detail Permohonan') }}"><i class="bi bi-eye fs-6 lh-1"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($this->issuedLoans->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2">{{ $this->issuedLoans->links() }}</div>
            @endif
        @endif
    </div>
</div>
