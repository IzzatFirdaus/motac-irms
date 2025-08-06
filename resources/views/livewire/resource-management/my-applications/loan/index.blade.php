{{-- resources/views/livewire/resource-management/my-applications/loan/index.blade.php --}}
<div>
    @section('title', __('Status Permohonan Pinjaman Saya'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-card-checklist me-2"></i>
            {{ __('Senarai Permohonan Pinjaman Saya') }}
        </h1>
        <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0"> {{-- Added a div to wrap buttons for better spacing --}}
            @can('create', App\Models\LoanApplication::class)
                <a href="{{ route('loan-applications.create') }}"
                    class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold px-3 py-2 motac-btn-primary">
                    <i class="bi bi-plus-circle-fill {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                    {{ __('Mohon Pinjaman Baru') }}
                </a>
            @endcan
            {{-- NEW: Button to navigate to Helpdesk Create Ticket page --}}
            <a href="{{ route('helpdesk.create') }}"
                class="btn btn-info d-inline-flex align-items-center text-uppercase small fw-semibold px-3 py-2">
                <i class="bi bi-headset {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Buka Tiket Bantuan Baru') }}
            </a>
        </div>
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Filters and Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Penapisan & Carian Permohonan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 col-sm-12">
                    <label for="loanSearchTerm" class="form-label motac-form-label small fw-medium">{{ __('Carian (ID, Tujuan, Lokasi)') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="loanSearchTerm"
                        placeholder="{{ __('Taip kata kunci carian...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-5 col-sm-12">
                    <label for="loanFilterStatus" class="form-label motac-form-label small fw-medium">{{ __('Tapis mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="loanFilterStatus" class="form-select form-select-sm">
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="col-md-2 col-sm-12">
                    <button wire:click="resetFilters" wire:loading.attr="disabled"
                        class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline"
                        title="{{ __('Set Semula Penapis') }}">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span wire:loading wire:target="resetFilters" class="spinner-border spinner-border-sm"
                            role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="resetFilters">{{ __('Set Semula') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loan Applications Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Permohonan Dikemukakan') }}
            </h5>
            @if ($applications->total() > 0)
                <span class="text-muted small">
                    {{ __('Memaparkan :start - :end daripada :total rekod', [
                        'start' => $applications->firstItem(),
                        'end' => $applications->lastItem(),
                        'total' => $applications->total(),
                    ]) }}
                </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan Pinjaman') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Lokasi Penggunaan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Permohonan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($applications as $application)
                        <tr wire:key="loan-app-{{ $application->id }}">
                            <td class="px-3 py-2 small fw-semibold">#{{ $application->id }}</td>
                            <td class="px-3 py-2 small" style="max-width: 250px; white-space: normal;">{{ Str::limit($application->purpose, 70) }}</td>
                            <td class="px-3 py-2 small">{{ $application->definedLocation?->name ?? $application->requested_location ?? 'N/A' }}</td>
                            <td class="px-3 py-2 small">{{ $application->created_at->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                            <td class="px-3 py-2 small">
                                <span class="badge {{ $application->status_color_class }}">{{ $application->status_translated }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('loan-applications.show', $application->id) }}"
                                   class="btn btn-sm btn-info text-white d-inline-flex align-items-center"
                                   title="{{ __('Lihat Butiran') }}">
                                    <i class="bi bi-eye-fill me-1"></i>
                                    {{ __('Lihat') }}
                                </a>
                                @if($application->isPending())
                                    <button wire:click="confirmCancel({{ $application->id }})"
                                            class="btn btn-sm btn-danger d-inline-flex align-items-center ms-2"
                                            title="{{ __('Batal Permohonan') }}">
                                        <i class="bi bi-x-circle-fill me-1"></i>
                                        {{ __('Batal') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-folder-x fs-1 mb-2 text-secondary"></i>
                                    <p>{{ __('Tiada permohonan pinjaman ditemui berdasarkan tapisan semasa.') }}</p>
                                    @if ($searchTerm || $filterStatus !== 'all')
                                        <button wire:click="resetFilters" class="btn btn-link mt-2 p-0">{{ __('Set Semula Penapis') }}</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($applications->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    {{-- Cancel Confirmation Modal --}}
    @if ($showCancelModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="cancelConfirmationModalLabel">{{ __('Sahkan Pembatalan') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeCancelModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Adakah anda pasti ingin membatalkan permohonan pinjaman ini? Tindakan ini tidak boleh diundur.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCancelModal">{{ __('Tutup') }}</button>
                        <button type="button" class="btn btn-danger" wire:click="cancelApplication" wire:loading.attr="disabled" wire:target="cancelApplication">
                            <span wire:loading wire:target="cancelApplication" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="cancelApplication">{{ __('Ya, Batalkan') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" wire:ignore.self style="{{ $showCancelModal ? '' : 'display:none;' }}"></div>
    @endif
</div>
