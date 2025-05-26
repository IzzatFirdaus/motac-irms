<div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Tugasan Kelulusan Anda') }}</h6>
            <div>
                <button wire:click="refreshDataEvent" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sync"></i> {{ __('Muat Semula') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filterStatus" class="form-label">{{ __('Status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Status') }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_PENDING }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_PENDING] ?? 'Pending' }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_APPROVED] ?? 'Approved' }}</option>
                        <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_REJECTED] ?? 'Rejected' }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterType" class="form-label">{{ __('Jenis Permohonan') }}</label>
                    <select wire:model.live="filterType" id="filterType" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Jenis') }}</option>
                        <option value="{{ \App\Models\EmailApplication::class }}">{{ __('Permohonan Emel') }}</option>
                        <option value="{{ \App\Models\LoanApplication::class }}">{{ __('Permohonan Pinjaman ICT') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchTerm" class="form-label">{{ __('Carian') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTerm" class="form-control form-control-sm" placeholder="{{ __('ID Permohonan, Pemohon...') }}">
                </div>
            </div>

            <div wire:loading class="text-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div wire:loading.remove class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>{{ __('ID Kelulusan') }}</th>
                            <th>{{ __('ID Permohonan') }}</th>
                            <th>{{ __('Jenis') }}</th>
                            <th>{{ __('Pemohon') }}</th>
                            <th>{{ __('Tarikh Mohon') }}</th>
                            <th>{{ __('Peringkat') }}</th>
                            <th>{{ __('Status Kelulusan') }}</th>
                            <th>{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->approvalTasks as $approval)
                            <tr>
                                <td>#{{ $approval->id }}</td>
                                <td>
                                    @if($approval->approvable_type === \App\Models\EmailApplication::class)
                                        <a href="{{ route('email-applications.show', $approval->approvable_id) }}">#{{ $approval->approvable_id }}</a>
                                    @elseif($approval->approvable_type === \App\Models\LoanApplication::class)
                                        <a href="{{ route('loan-applications.show', $approval->approvable_id) }}">#{{ $approval->approvable_id }}</a>
                                    @else
                                        #{{ $approval->approvable_id }}
                                    @endif
                                </td>
                                <td>{{ $approval->approvable_type == \App\Models\EmailApplication::class ? __('Emel') : __('Pinjaman ICT') }}</td>
                                <td>{{ $approval->approvable?->user?->name ?? __('N/A') }}</td>
                                <td>{{ $approval->approvable?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                <td>{{ $approval->stage_translated }}</td>
                                <td>
                                    <span class="badge {{ App\Helpers\Helpers::getBootstrapStatusColorClass($approval->status) }} rounded-pill">
                                        {{ $approval->status_translated }}
                                    </span>
                                </td>
                                <td>
                                    @if($approval->status === \App\Models\Approval::STATUS_PENDING)
                                    <button wire:click="openApprovalActionModal({{ $approval->id }})" class="btn btn-xs btn-primary">
                                        <i class="fas fa-edit"></i> {{ __('Tindakan') }}
                                    </button>
                                    @else
                                    <button class="btn btn-xs btn-secondary" disabled>
                                        <i class="fas fa-check"></i> {{ __('Selesai') }}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('Tiada tugasan kelulusan ditemui.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div wire:loading.remove class="mt-3">
                {{ $this->approvalTasks->links() }}
            </div>
        </div>
    </div>

    {{-- Approval Action Modal --}}
    @if($showApprovalActionModal && $selectedApproval)
    <div class="modal fade show" id="approvalActionModal" tabindex="-1" aria-labelledby="approvalActionModalLabel" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalActionModalLabel">{{ __('Tindakan Kelulusan untuk Permohonan #') }}{{ $selectedApproval->approvable_id }}</h5>
                    <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="submitDecision">
                    <div class="modal-body">
                        <p><strong>{{ __('Pemohon:') }}</strong> {{ $selectedApproval->approvable?->user?->name ?? 'N/A' }}</p>
                        <p><strong>{{ __('Jenis Permohonan:') }}</strong> {{ $selectedApproval->approvable_type == \App\Models\EmailApplication::class ? __('Permohonan Emel') : __('Permohonan Pinjaman ICT') }}</p>
                        <p><strong>{{ __('Peringkat Semasa:') }}</strong> {{ $selectedApproval->stage_translated }}</p>

                        <div class="mb-3">
                            <label for="decision" class="form-label">{{ __('Keputusan Anda') }}</label>
                            <select wire:model.live="decision" id="decision" class="form-select @error('decision') is-invalid @enderror">
                                <option value="">-- {{ __('Sila Pilih') }} --</option>
                                <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_APPROVED] ?? 'Approved' }}</option>
                                <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ \App\Models\Approval::$STATUSES_LABELS[\App\Models\Approval::STATUS_REJECTED] ?? 'Rejected' }}</option>
                            </select>
                            @error('decision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label">{{ __('Ulasan/Sebab') }} @if($decision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">*</span> @endif</label>
                            <textarea wire:model="comments" id="comments" rows="4" class="form-control @error('comments') is-invalid @enderror"></textarea>
                            @error('comments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submitDecision" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {{ __('Hantar Keputusan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" id="backdrop" style="display: @if($showApprovalActionModal) block @else none @endif;"></div>
    @endif

    @push('page-script')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('openApprovalActionModalEvent', (event) => {
                var modal = new bootstrap.Modal(document.getElementById('approvalActionModal'));
                modal.show();
            });
            Livewire.on('closeApprovalActionModalEvent', (event) => {
                var modalElement = document.getElementById('approvalActionModal');
                if (modalElement) {
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        });
    </script>
    @endpush
</div>
