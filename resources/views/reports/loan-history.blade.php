{{-- resources/views/reports/loan-history.blade.php --}}
<x-app-layout>
    @section('title', __('Laporan Sejarah Pinjaman ICT')) {{-- Added title --}}

    <div class="container-fluid py-4"> {{-- Added container-fluid and padding --}}
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i>{{-- Bootstrap Icon --}}
                        {{ __('Laporan Sejarah Transaksi Pinjaman ICT') }}
                    </h3>
                    @if (Route::has('admin.reports.index')) {{-- Ensure this route name 'admin.reports.index' is correct --}}
                        <div class="mt-2 mt-sm-0 flex-shrink-0">
                            <a href="{{ route('admin.reports.index') }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                                <i class="bi bi-arrow-left me-1"></i> {{-- Bootstrap Icon --}}
                                {{ __('Kembali ke Senarai Laporan') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-body">
                {{-- Corrected path to your general alert partial --}}
                @include('_partials._alerts.alert-general')

                <div class="table-responsive">
                    {{-- The Ignition output shows $loanHistory is passed, but the view uses $loanTransactions --}}
                    {{-- Assuming $loanTransactions is the correct variable from the controller for this view --}}
                    @if ($loanTransactions->count())
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Peralatan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Pengguna') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Jenis Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Tarikh Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Status Transaksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanTransactions as $transaction)
                                    <tr>
                                        <td class="px-3 py-2 small fw-medium text-dark">#{{ $transaction->id }}</td>
                                        <td class="px-3 py-2 small">
                                            {{-- Ensure loanApplication relationship is loaded or handle potential N+1 --}}
                                            @if($transaction->loanApplication)
                                            <a href="{{ route('loan-applications.show', $transaction->loanApplication->id) }} ">
                                                #{{ $transaction->loan_application_id }}
                                            </a>
                                            @else
                                            #{{ $transaction->loan_application_id }} ({{ __('Data Permohonan Tidak Dijumpai') }})
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{-- Ensure loanTransactionItems and equipment relationships are loaded or handle potential N+1 --}}
                                            @if($transaction->loanTransactionItems->isNotEmpty())
                                                @foreach($transaction->loanTransactionItems as $item)
                                                    {{ $item->equipment->brand_model_serial ?? $item->equipment->tag_id ?? __('Peralatan ID: ') . $item->equipment_id }}@if(!$loop->last), @endif
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- Ensure loanApplication and its user relationship are loaded or handle potential N+1 --}}
                                        <td class="px-3 py-2 small text-muted">{{ $transaction->loanApplication->user->name ?? ($transaction->loanApplication->user->full_name ?? 'N/A') }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            <span class="badge rounded-pill {{ $transaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary-emphasis' }} fw-normal">
                                                {{-- Assuming your LoanTransaction model has a type_label accessor or similar --}}
                                                {{ __(optional($transaction)->type_label ?? Str::title(str_replace('_', ' ', $transaction->type))) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ $transaction->transaction_date?->translatedFormat('d M Y, H:i A') ?? ($transaction->created_at?->translatedFormat('d M Y, H:i A')) }}</td>
                                        <td class="px-3 py-2 small">
                                            {{-- Ensure App\Helpers\Helpers::getStatusColorClass exists and handles these statuses --}}
                                            {{-- Or use a dedicated component like <x-loan-transaction-status-badge :status="$transaction->status" /> --}}
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transaction->status) }} fw-normal">
                                                {{ __(Str::title(str_replace('_', ' ', $transaction->status))) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($loanTransactions->hasPages())
                            <div class="mt-3 pt-3 border-top d-flex justify-content-center">
                                {{ $loanTransactions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info d-flex align-items-center text-center" role="alert"> {{-- Added text-center --}}
                           <i class="bi bi-info-circle-fill me-2"></i> {{-- Bootstrap Icon --}}
                            <div>
                                {{ __('Tiada sejarah transaksi pinjaman ditemui.') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
