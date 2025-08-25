{{-- resources/views/livewire/resource-management/approval/dashboard.blade.php --}}
<div>
    @php
        // Page title is now intended to be set via @section('title') in this Blade file
        // as the #[Title] attribute in the component class was commented out.
    @endphp

    @section('title')
        {{ __('Papan Pemuka Kelulusan') . ' - ' . __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC')) }}
    @endsection

    @include('_partials._alerts.alert-general')

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-check2-square me-2"></i>
            {{ __('Senarai Tugasan Kelulusan') }}
        </h4>
    </div>

    <div class="card mb-4 motac-card">
        <div class="card-body motac-card-body">
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label form-label-sm">{{ __('Tapis Mengikut Jenis') }}</label>
                    {{-- EDITED: Using short keys for filter values --}}
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Jenis') }}</option>
                        <option value="email_application">{{ __('Permohonan E-mel/ID') }}</option>
                        <option value="loan_application">{{ __('Permohonan Pinjaman ICT') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatusApproval" class="form-label form-label-sm">{{ __('Tapis Mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatusApproval" class="form-select form-select-sm">
                        <option value="all">{{ __('Semua Status') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_PENDING }}">{{ __('Menunggu Tindakan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_APPROVED }}">{{ __('Diluluskan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_REJECTED }}">{{ __('Ditolak') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchApprovals" class="form-label form-label-sm">{{ __('Carian Terperinci') }}</label>
                    <input wire:model.live.debounce.300ms="search" id="searchApprovals" type="text"
                           placeholder="{{ __('Cari ID Permohonan, Nama Pemohon...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="$set('search', '')" class="btn btn-sm btn-outline-secondary w-100">{{ __('Set Semula') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div wire:loading.delay.long class="w-100 text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
        </div>
        <p class="mt-2 fs-5">{{ __('Sedang memuatkan senarai kelulusan...') }}</p>
    </div>

    <div wire:loading.remove>
        @if ($this->approvalTasks->isEmpty())
            <div class="alert alert-info d-flex align-items-center">
                 <span class="alert-icon me-2"><i class="bi bi-info-circle-fill fs-4"></i></span>
                {{ __('Tiada tugasan kelulusan yang sepadan dengan tapisan semasa.') }}
            </div>
        @else
            <div class="card motac-card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Tugasan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis & ID Permohonan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peringkat Kelulusan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Tugasan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Diterima') }}</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($this->approvalTasks as $approvalTask)
                                @php $approvable = $approvalTask->approvable; @endphp
                                <tr wire:key="approval-task-{{ $approvalTask->id }}">
                                    <td class="px-3 py-2 small"><strong>#{{ $approvalTask->id }}</strong></td>
                                    <td class="px-3 py-2 small">
                                        @if ($approvable instanceof \App\Models\EmailApplication) <i class="bi bi-envelope-fill text-info me-1"></i>{{ __('E-mel/ID') }}
                                        @elseif ($approvable instanceof \App\Models\LoanApplication) <i class="bi bi-laptop text-primary me-1"></i>{{ __('Pinjaman ICT') }}
                                        @else <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>{{ __('Tidak Diketahui') }}
                                        @endif
                                        @if ($approvable) - #{{ $approvable->id }} @endif
                                    </td>
                                    <td class="px-3 py-2 small">{{ optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('N/A')) }}</td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->stage_translated }}</td>
                                    <td class="px-3 py-2 small">
                                        {{-- EDITED: Using model accessor for status badge class and translated text --}}
                                        <span class="badge {{ $approvalTask->status_color_class }}">{{ $approvalTask->status_translated }}</span>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->created_at->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                    <td class="text-center px-3 py-2">
                                        @if ($approvalTask->status === \App\Models\Approval::STATUS_PENDING)
                                            @can('update', $approvalTask)
                                                <button type="button" wire:click="openApprovalModal({{ $approvalTask->id }})"
                                                        class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-square me-1"></i>{{ __('Semak & Bertindak') }}
                                                </button>
                                            @else
                                                @can('view', $approvalTask)
                                                <a href="{{ route('approvals.show', $approvalTask->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Butiran') }}
                                                </a>
                                                @else
                                                <span class="text-muted small"><em>{{ __('Tiada kebenaran') }}</em></span>
                                                @endcan
                                            @endcan
                                        @else
                                             @if(method_exists($this, 'getViewApplicationRoute') && $this->getViewApplicationRoute($approvalTask))
                                                 <a href="{{ $this->getViewApplicationRoute($approvalTask) }}" class="btn btn-sm btn-outline-secondary">
                                                     <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Butiran') }}
                                                 </a>
                                             @else
                                                <a href="{{ route('approvals.show', $approvalTask->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat Tugasan') }}
                                                </a>
                                             @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($this->approvalTasks->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                        {{ $this->approvalTasks->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Approval Action Modal --}}
    @if ($showApprovalModal && $this->currentApprovalDetails)
        <div class="modal fade @if($showApprovalModal) show @endif" id="approvalActionBootstrapModal" tabindex="-1"
             style="display: @if($showApprovalModal) block; background-color: rgba(0,0,0,0.5); @else none; @endif"
             aria-labelledby="approvalActionModalLabel" @if($showApprovalModal) aria-modal="true" role="dialog" @else aria-hidden="true" @endif wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit.prevent="recordDecision">
                        <div class="modal-header">
                            <h5 class="modal-title d-flex align-items-center" id="approvalActionModalLabel">
                                <i class="bi bi-clipboard-check-fill me-2"></i>
                                {{ __('Semak Tugasan Kelulusan') }} #{{ $this->currentApprovalDetails->id }}
                                <small class="d-block text-muted fw-normal mt-1 ms-2">({{ $this->currentApprovalDetails->stage_translated }})</small>
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeApprovalModal" aria-label="{{__('Tutup')}}"></button>
                        </div>
                        <div class="modal-body">
                            @php $approvableItemFromDetails = $this->currentApprovalDetails->approvable; @endphp
                            @if ($approvableItemFromDetails)
                                <div class="mb-3 p-3 border rounded bg-light-subtle">
                                    <h6 class="mb-2 fw-semibold">{{ __('Butiran Permohonan') }}</h6>
                                    <dl class="row mb-0 small">
                                        <dt class="col-sm-4">{{ __('Jenis Permohonan') }}</dt>
                                        <dd class="col-sm-8">
                                            @if ($approvableItemFromDetails instanceof \App\Models\EmailApplication) <i class="bi bi-envelope-fill text-info me-1"></i>{{ __('Permohonan E-mel/ID') }}
                                            @elseif ($approvableItemFromDetails instanceof \App\Models\LoanApplication) <i class="bi bi-laptop text-primary me-1"></i>{{ __('Permohonan Pinjaman ICT') }}
                                            @endif
                                            (#{{ $approvableItemFromDetails->id }})
                                        </dd>
                                        <dt class="col-sm-4">{{ __('Pemohon') }}</dt>
                                        <dd class="col-sm-8">{{ optional($approvableItemFromDetails->user)->name ?? (optional($approvableItemFromDetails)->applicant_name ?? __('N/A')) }}</dd>

                                        @if ($approvableItemFromDetails instanceof \App\Models\EmailApplication)
                                            <dt class="col-sm-4">{{ __('E-mel Dicadang') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->proposed_email ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Tujuan/Catatan') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->application_reason_notes ?? __('N/A') }}</dd>
                                        @elseif ($approvableItemFromDetails instanceof \App\Models\LoanApplication)
                                            <dt class="col-sm-4">{{ __('Tujuan Pinjaman') }}</dt>
                                            <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $approvableItemFromDetails->purpose ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Lokasi Penggunaan') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->location ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Tempoh Pinjaman') }}</dt>
                                            <dd class="col-sm-8">
                                                {{ optional($approvableItemFromDetails->loan_start_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }} -
                                                {{ optional($approvableItemFromDetails->loan_end_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}
                                            </dd>
                                            {{-- Display Loan Application Items for Quantity Adjustment --}}
                                            @if ($this->approvalDecision === \App\Models\Approval::STATUS_APPROVED && $approvableItemFromDetails->loanApplicationItems->isNotEmpty())
                                                <dt class="col-sm-12 mt-3 fw-semibold">{{ __('Penyesuaian Kuantiti Item (Jika Perlu)') }}:</dt>
                                                <dd class="col-sm-12">
                                                    @foreach ($this->approvalItems as $index => $approvalItemData)
                                                        <div class="mb-2 p-2 border rounded-1 bg-white">
                                                            @php
                                                                $originalLoanItem = $approvableItemFromDetails->loanApplicationItems->firstWhere('id', $approvalItemData['loan_application_item_id']);
                                                            @endphp
                                                            @if($originalLoanItem)
                                                            <p class="mb-1 small">
                                                                <strong>{{ __('Item') }}:</strong> {{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[$originalLoanItem->equipment_type] ?? $originalLoanItem->equipment_type) }} <br>
                                                                <strong>{{ __('Kuantiti Dipohon') }}:</strong> {{ $originalLoanItem->quantity_requested }}
                                                            </p>
                                                            <label for="approvalItems_{{ $index }}_quantity_approved" class="form-label form-label-sm">{{ __('Kuantiti Diluluskan (Max: :qty)',['qty' => $originalLoanItem->quantity_requested]) }}:</label>
                                                            <input type="number"
                                                                   wire:model.lazy="approvalItems.{{ $index }}.quantity_approved"
                                                                   id="approvalItems_{{ $index }}_quantity_approved"
                                                                   class="form-control form-control-sm @error('approvalItems.'.$index.'.quantity_approved') is-invalid @enderror"
                                                                   min="0"
                                                                   max="{{ $originalLoanItem->quantity_requested }}">
                                                            @error('approvalItems.'.$index.'.quantity_approved')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </dd>
                                            @elseif ($approvableItemFromDetails->loanApplicationItems->isNotEmpty())
                                                <dt class="col-sm-12 mt-2">{{ __('Item Dipohon:') }}</dt>
                                                <dd class="col-sm-12">
                                                    <ul class="list-unstyled ps-3 mb-0">
                                                        @foreach ($approvableItemFromDetails->loanApplicationItems as $loanItem)
                                                            <li>- {{ $loanItem->equipment_type ? (\App\Models\Equipment::getAssetTypeOptions()[$loanItem->equipment_type] ?? Str::title(str_replace('_', ' ', $loanItem->equipment_type))) : 'N/A' }}
                                                                ({{ __('Kuantiti Dipohon') }}: {{ $loanItem->quantity_requested }})
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </dd>
                                            @endif
                                        @endif
                                        <dt class="col-sm-4 mt-2">{{ __('Tarikh Mohon') }}</dt>
                                        <dd class="col-sm-8 mt-2">{{ optional($approvableItemFromDetails->created_at)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}</dd>
                                    </dl>
                                    @if(method_exists($this, 'getViewApplicationRoute') && $this->getViewApplicationRoute($this->currentApprovalDetails))
                                        <a href="{{ $this->getViewApplicationRoute($this->currentApprovalDetails) }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('Lihat Permohonan Penuh') }}
                                        </a>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">{{ __('Butiran permohonan tidak dapat dimuatkan.') }}</p>
                            @endif
                            <hr>
                            <div class="mb-3">
                                <label for="approvalDecisionModal" class="form-label fw-semibold">{{ __('Keputusan Anda') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="approvalDecision" id="approvalDecisionModal" class="form-select @error('approvalDecision') is-invalid @enderror">
                                    <option value="">-- {{ __('Pilih Keputusan') }} --</option>
                                    @foreach(\App\Models\Approval::getDecisionStatuses() as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ __($statusLabel) }}</option>
                                    @endforeach
                                </select>
                                @error('approvalDecision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
                                <label for="approvalCommentsModal" class="form-label fw-semibold">
                                    {{ __('Ulasan') }}
                                    @if ($approvalDecision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">* {{ __('(Wajib jika ditolak)') }}</span>@endif
                                </label>
                                <textarea wire:model.defer="approvalComments" id="approvalCommentsModal" rows="3" class="form-control @error('approvalComments') is-invalid @enderror" placeholder="{{ __('Sila berikan ulasan anda...') }}"></textarea>
                                @error('approvalComments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div wire:loading wire:target="recordDecision" class="text-muted small mt-2">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{ __('Memproses keputusan...') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeApprovalModal">{{ __('Batal') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="recordDecision">
                                <i class="bi bi-send-check-fill me-1"></i>
                                {{ __('Hantar Keputusan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('livewire:initialized', () => {
                const approvalModalElement = document.getElementById('approvalActionBootstrapModal');
                let approvalModalInstance = null;

                if (approvalModalElement) {
                     approvalModalInstance = bootstrap.Modal.getInstance(approvalModalElement);
                     if (!approvalModalInstance) {
                        approvalModalInstance = new bootstrap.Modal(approvalModalElement, { backdrop: 'static', keyboard: false });
                     }
                }

                @this.on('showApprovalModalEvent', () => {
                    if(approvalModalInstance) {
                        approvalModalInstance.show();
                    }
                });
                @this.on('closeApprovalModalEvent', () => {
                    if(approvalModalInstance && approvalModalInstance._isShown) {
                        approvalModalInstance.hide();
                    }
                });

                if (approvalModalElement) {
                    approvalModalElement.addEventListener('hidden.bs.modal', (event) => {
                        if (@this.get('showApprovalModal')) {
                            @this.call('closeApprovalModal');
                        }
                    });
                }
            });
        </script>
    @endif
</div>
