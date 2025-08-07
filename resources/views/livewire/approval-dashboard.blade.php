{{-- resources/views/livewire/approval-dashboard.blade.php --}}
<div>
    <x-alert-manager />

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-semibold text-primary"><i class="bi bi-list-check me-2"></i>{{ __('Tugasan Kelulusan Anda') }}</h6>
            <div>
                <button wire:click="refreshDataEvent" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i> {{ __('Muat Semula') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3 g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">{{ __('Status Kelulusan') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Status') }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_PENDING }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_PENDING] ?? 'Pending' }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_APPROVED] ?? 'Approved' }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_REJECTED] ?? 'Rejected' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterType" class="form-label">{{ __('Jenis Permohonan') }}</label>
                    <select wire:model.live="filterType" id="filterType" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Jenis') }}</option>
                        <option value="{{ \App\Models\LoanApplication::class }}">{{ __('Permohonan Pinjaman ICT') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchTerm" class="form-label">{{ __('Carian') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTerm" class="form-control form-control-sm" placeholder="{{ __('Cari ID Permohonan, Nama Pemohon...') }}">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Set Semula') }}</button>
                </div>
            </div>

            <div wire:loading.delay.long class="text-center my-3 py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
                </div>
                <p class="mt-2 text-muted">{{__('Sedang memuatkan tugasan kelulusan...')}}</p>
            </div>

            <div wire:loading.remove class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Kelulusan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Mohon') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peringkat') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Kelulusan') }}</th>
                            <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->approvalTasks as $approval)
                            <tr wire:key="approval-list-item-{{ $approval->id }}">
                                <td class="px-3 py-2 small">#{{ $approval->id }}</td>
                                <td class="px-3 py-2 small">
                                    @if($approval->approvable_type === \App\Models\LoanApplication::class)
                                        <a href="{{ route('loan-applications.show', $approval->approvable_id) }}" wire:navigate>#{{ $approval->approvable_id }}</a>
                                    @else
                                        #{{ $approval->approvable_id }}
                                    @endif
                                </td>
                                <td class="px-3 py-2 small">
                                    @if($approval->approvable_type === \App\Models\LoanApplication::class)
                                        <i class="bi bi-laptop text-primary me-1"></i>{{ __('Pinjaman ICT') }}
                                    @endif
                                </td>
                                <td class="px-3 py-2 small">{{ $approval->approvable?->user?->name ?? __('N/A') }}</td>
                                <td class="px-3 py-2 small">{{ $approval->approvable?->created_at?->translatedFormat(config('app.datetime_format_my_short', 'd M Y, H:i')) ?? 'N/A' }}</td>
                                <td class="px-3 py-2 small">{{ $approval->stage_translated ?? __(Str::title(str_replace('_', ' ', $approval->stage))) }}</td>
                                <td class="px-3 py-2 small">
                                    <span class="badge {{ App\Helpers\Helpers::getStatusColorClass($approval->status) }} rounded-pill">
                                        {{ $approval->status_translated ?? __(Str::title(str_replace('_', ' ', $approval->status))) }}
                                    </span>
                                </td>
                                <td class="text-center px-3 py-2">
                                    @if($approval->status === \App\Models\Approval::STATUS_PENDING)
                                        @can('update', $approval)
                                            <button wire:click="openApprovalActionModal({{ $approval->id }})" class="btn btn-xs btn-primary">
                                                <i class="bi bi-pencil-square me-1"></i> {{ __('Tindakan') }}
                                            </button>
                                        @else
                                             <span class="text-muted small"><em>{{__('Tiada kebenaran')}}</em></span>
                                        @endcan
                                    @else
                                    <button class="btn btn-xs btn-secondary" disabled>
                                        <i class="bi bi-check2-all me-1"></i> {{ __('Selesai') }}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-emoji-frown fs-2 text-muted mb-2 d-block"></i>
                                    {{ __('Tiada tugasan kelulusan ditemui.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->approvalTasks->hasPages())
                <div wire:loading.remove class="mt-3 d-flex justify-content-center card-footer bg-light border-top py-2">
                    {{ $this->approvalTasks->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Approval Action Modal --}}
    @if($showApprovalActionModal && $selectedApproval)
    <div class="modal fade show" id="approvalActionModal" tabindex="-1" aria-labelledby="approvalActionModalLabel" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalActionModalLabel">{{ __('Tindakan Kelulusan untuk Permohonan #') }}{{ $selectedApproval->approvable_id }}
                        <small class="d-block text-muted fw-normal">{{ $selectedApproval->stage_translated ?? __(Str::title(str_replace('_', ' ', $selectedApproval->stage))) }}</small>
                    </h5>
                    <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="submitDecision">
                    <div class="modal-body">
                        @php $approvableItem = $selectedApproval->approvable; @endphp
                        @if($approvableItem)
                            <div class="mb-3 p-3 border rounded bg-light-subtle">
                                <h6 class="mb-2 fw-semibold">{{ __('Butiran Permohonan') }}</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-4">{{ __('Pemohon:') }}</dt>
                                    <dd class="col-sm-8">{{ $approvableItem->user?->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">{{ __('Jenis Permohonan:') }}</dt>
                                    <dd class="col-sm-8">
                                        @if($approvableItem instanceof \App\Models\LoanApplication) <i class="bi bi-laptop text-primary me-1"></i>{{ __('Permohonan Pinjaman ICT') }}
                                        @endif
                                    </dd>

                                    @if($approvableItem instanceof \App\Models\LoanApplication)
                                        <dt class="col-sm-4">{{ __('Tujuan Pinjaman:') }}</dt>
                                        <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $approvableItem->purpose ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">{{ __('Lokasi Penggunaan:') }}</dt>
                                        <dd class="col-sm-8">{{ $approvableItem->location ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">{{ __('Tempoh Pinjam:') }}</dt>
                                        <dd class="col-sm-8">
                                            {{ $approvableItem->loan_start_date?->translatedFormat(config('app.datetime_format_my_short')) }} - {{ $approvableItem->loan_end_date?->translatedFormat(config('app.datetime_format_my_short')) }}
                                        </dd>
                                        @if($approvableItem->loanApplicationItems->count())
                                        <dt class="col-sm-12 mt-2">{{__('Item Dimohon:')}}</dt>
                                        <dd class="col-sm-12">
                                            <ul class="list-unstyled ps-1 mb-0">
                                            @foreach($approvableItem->loanApplicationItems as $loanItem)
                                                <li>• {{ $loanItem->equipment_type ? (\App\Models\Equipment::$ASSET_TYPES_LABELS[$loanItem->equipment_type] ?? Str::title(str_replace('_', ' ', $loanItem->equipment_type))) : 'N/A' }} (Qty: {{ $loanItem->quantity_requested }})</li>
                                            @endforeach
                                            </ul>
                                        </dd>
                                        @endif
                                    @endif
                                     <dt class="col-sm-4 mt-2">{{ __('Tarikh Mohon:') }}</dt>
                                     <dd class="col-sm-8 mt-2">{{ $approvableItem->created_at?->translatedFormat(config('app.datetime_format_my')) ?? 'N/A' }}</dd>
                                </dl>
                                @if($this->getViewApplicationRouteForSelected())
                                <a href="{{ $this->getViewApplicationRouteForSelected() }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                    <i class="bi bi-box-arrow-up-right me-1" style="font-size: .75em;"></i>{{ __('Lihat Permohonan Penuh') }}
                                </a>
                                @endif
                            </div>
                        @else
                            <p class="text-danger">{{__('Ralat: Butiran permohonan asal tidak dapat dimuatkan.')}}</p>
                        @endif

                        <div class="mb-3">
                            <label for="decision" class="form-label fw-semibold">{{ __('Keputusan Anda') }} <span class="text-danger">*</span></label>
                            <select wire:model.live="decision" id="decision" class="form-select @error('decision') is-invalid @enderror">
                                <option value="">-- {{ __('Sila Pilih Keputusan') }} --</option>
                                <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_APPROVED] ?? 'Approved' }}</option>
                                <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_REJECTED] ?? 'Rejected' }}</option>
                            </select>
                            @error('decision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label fw-semibold">{{ __('Ulasan/Sebab') }} @if($decision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">* {{ __('(Wajib jika ditolak)') }}</span> @endif</label>
                            <textarea wire:model.defer="comments" id="comments" rows="4" class="form-control @error('comments') is-invalid @enderror" placeholder="{{ __('Nyatakan ulasan atau sebab keputusan anda...') }}"></textarea>
                            @error('comments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitDecision">
                            <span wire:loading wire:target="submitDecision" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="submitDecision"><i class="bi bi-check-lg me-1"></i></span>
                            {{ __('Hantar Keputusan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('page-script')
    <script>
        // Handles the modal open/close with Livewire events and Bootstrap
        document.addEventListener('livewire:initialized', () => {
            const approvalModalElement = document.getElementById('approvalActionModal');
            let approvalModalInstance = null;

            if (approvalModalElement) {
                approvalModalInstance = bootstrap.Modal.getInstance(approvalModalElement);
                if (!approvalModalInstance) {
                    approvalModalInstance = new bootstrap.Modal(approvalModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            }

            @this.on('openApprovalActionModalEvent', () => {
                if(approvalModalInstance) {
                    approvalModalInstance.show();
                } else {
                    console.warn('Approval modal instance not found for open event.');
                }
            });
            @this.on('closeApprovalActionModalEvent', () => {
                if(approvalModalInstance) {
                    approvalModalInstance.hide();
                } else {
                    console.warn('Approval modal instance not found for close event.');
                }
            });
        });
    </script>
    @endpush
</div>
