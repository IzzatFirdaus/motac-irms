{{-- resources/views/livewire/resource-management/approval/dashboard.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @section('title', __('Papan Pemuka Kelulusan'))

    @include('layouts._partials._alerts.alert-general')

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0">{{ __('Senarai Tugasan Kelulusan') }}</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label">{{ __('Tapis Mengikut Jenis') }}</label>
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        <option value="{{ addslashes(App\Models\EmailApplication::class) }}">{{ __('Permohonan E-mel/ID') }}</option>
                        <option value="{{ addslashes(App\Models\LoanApplication::class) }}">{{ __('Permohonan Pinjaman ICT') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatusApproval" class="form-label">{{ __('Tapis Mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatusApproval" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_PENDING }}">{{ __('Menunggu Tindakan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_APPROVED }}">{{ __('Diluluskan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_REJECTED }}">{{ __('Ditolak') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchApprovals" class="form-label">{{ __('Carian Terperinci') }}</label>
                    <input wire:model.live.debounce.300ms="search" id="searchApprovals" type="text"
                           placeholder="{{ __('Cari ID Permohonan, Nama Pemohon...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100">{{ __('Set Semula') }}</button>
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
        @if ($this->pendingApprovalTasks->isEmpty())
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <span class="alert-icon me-2"><i class="ti ti-info-circle ti-md"></i></span>
                <div>{{ __('Tiada tugasan kelulusan yang sepadan dengan tapisan semasa.') }}</div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ID Tugasan') }}</th>
                                <th>{{ __('Jenis & ID Permohonan') }}</th>
                                <th>{{ __('Pemohon') }}</th>
                                <th>{{ __('Peringkat Kelulusan') }}</th>
                                <th>{{ __('Status Tugasan') }}</th>
                                <th>{{ __('Tarikh Diterima') }}</th>
                                <th class="text-center">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($this->pendingApprovalTasks as $approvalTask)
                                @php $approvable = $approvalTask->approvable; @endphp
                                <tr wire:key="approval-task-{{ $approvalTask->id }}">
                                    <td><strong>#{{ $approvalTask->id }}</strong></td>
                                    <td>
                                        @if ($approvable instanceof \App\Models\EmailApplication) <i class="ti ti-mail text-info me-1"></i>{{ __('E-mel/ID') }}
                                        @elseif ($approvable instanceof \App\Models\LoanApplication) <i class="ti ti-device-laptop text-primary me-1"></i>{{ __('Pinjaman ICT') }}
                                        @else <i class="ti ti-file-invoice text-secondary me-1"></i>{{ __('Tidak Diketahui') }}
                                        @endif
                                        @if ($approvable) - #{{ $approvable->id }} @endif
                                    </td>
                                    <td>{{ optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('N/A')) }}</td>
                                    <td>{{ __(App\Models\Approval::getStageDisplayName($approvalTask->stage)) }}</td>
                                    <td><span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass('approval_'.$approvalTask->status) }}">{{ __(Str::title(str_replace('_', ' ', $approvalTask->status))) }}</span></td>
                                    <td>{{ $approvalTask->created_at->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                    <td class="text-center">
                                        @if ($approvalTask->status === \App\Models\Approval::STATUS_PENDING)
                                            @can('update', $approvalTask)
                                                <button type="button" wire:click="openApprovalModal({{ $approvalTask->id }})"
                                                        class="btn btn-sm btn-primary">
                                                    <i class="ti ti-edit ti-xs me-1"></i>{{ __('Semak & Bertindak') }}
                                                </button>
                                            @else
                                                <span class="text-muted small"><em>{{ __('Tiada tindakan') }}</em></span>
                                            @endcan
                                        @else
                                             <a href="{{ $this->getViewApplicationRoute($approvalTask) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="ti ti-eye ti-xs me-1"></i>{{ __('Lihat Butiran') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($this->pendingApprovalTasks->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $this->pendingApprovalTasks->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if ($showApprovalModal && $this->currentApprovalDetails)
        <div class="modal fade @if($showApprovalModal) show @endif" id="approvalActionBootstrapModal" tabindex="-1"
             style="display: @if($showApprovalModal) block; background-color: rgba(0,0,0,0.5); @else none; @endif"
             aria-labelledby="approvalActionModalLabel" @if($showApprovalModal) aria-modal="true" role="dialog" @else aria-hidden="true" @endif wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit.prevent="recordDecision">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approvalActionModalLabel">
                                {{ __('Semak Tugasan Kelulusan') }} #{{ $this->currentApprovalDetails->id }}
                                <small class="d-block text-muted">{{ __(App\Models\Approval::getStageDisplayName($this->currentApprovalDetails->stage)) }}</small>
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeApprovalModal" aria-label="{{__('Tutup')}}"></button>
                        </div>
                        <div class="modal-body">
                            @php $item = $this->currentApprovable; @endphp
                            @if ($item)
                                <div class="mb-3 p-3 border rounded bg-light-subtle">
                                    <h6 class="mb-2 fw-semibold">{{ __('Butiran Permohonan') }}</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">{{ __('Jenis Permohonan') }}</dt>
                                        <dd class="col-sm-8">
                                            @if ($item instanceof \App\Models\EmailApplication) {{ __('Permohonan E-mel/ID') }}
                                            @elseif ($item instanceof \App\Models\LoanApplication) {{ __('Permohonan Pinjaman ICT') }}
                                            @endif
                                            (#{{ $item->id }})
                                        </dd>
                                        <dt class="col-sm-4">{{ __('Pemohon') }}</dt>
                                        <dd class="col-sm-8">{{ optional($item->user)->name ?? (optional($item)->applicant_name ?? __('N/A')) }}</dd>
                                        @if ($item instanceof \App\Models\EmailApplication)
                                            <dt class="col-sm-4">{{ __('E-mel Dicadang') }}</dt>
                                            <dd class="col-sm-8">{{ $item->proposed_email ?? __('N/A') }}</dd>
                                        @elseif ($item instanceof \App\Models\LoanApplication)
                                            <dt class="col-sm-4">{{ __('Tujuan Pinjaman') }}</dt>
                                            <dd class="col-sm-8">{{ Str::limit($item->purpose, 150) ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Lokasi Penggunaan') }}</dt>
                                            <dd class="col-sm-8">{{ $item->location ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Tempoh Pinjaman') }}</dt>
                                            <dd class="col-sm-8">
                                                {{ optional($item->loan_start_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }} -
                                                {{ optional($item->loan_end_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}
                                            </dd>
                                            @if ($item->applicationItems && $item->applicationItems->count() > 0)
                                                <dt class="col-sm-12 mt-2">{{ __('Item Dipohon:') }}</dt>
                                                <dd class="col-sm-12">
                                                    <ul class="list-unstyled ps-3">
                                                        @foreach ($item->applicationItems as $loanItem)
                                                            <li>- {{ $loanItem->equipment_type }} ({{ __('Kuantiti') }}: {{ $loanItem->quantity_requested }})</li>
                                                        @endforeach
                                                    </ul>
                                                </dd>
                                            @endif
                                        @endif
                                        <dt class="col-sm-4 mt-2">{{ __('Tarikh Mohon') }}</dt>
                                        <dd class="col-sm-8 mt-2">{{ optional($item->created_at)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}</dd>
                                    </dl>
                                    <a href="{{ $this->getViewApplicationRoute($this->currentApprovalDetails) }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                        <i class="ti ti-external-link ti-xs me-1"></i>{{ __('Lihat Permohonan Penuh') }}
                                    </a>
                                </div>
                            @else
                                <p class="text-muted">{{ __('Butiran permohonan tidak dapat dimuatkan.') }}</p>
                            @endif
                            <hr>
                            <div class="mb-3">
                                <label for="approvalDecisionModal" class="form-label fw-semibold">{{ __('Keputusan Anda') }} <span class="text-danger">*</span></label>
                                <select wire:model.lazy="approvalDecision" id="approvalDecisionModal" class="form-select @error('approvalDecision') is-invalid @enderror">
                                    <option value="">-- {{ __('Pilih Keputusan') }} --</option>
                                    <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ __('Luluskan') }}</option>
                                    <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ __('Tolak') }}</option>
                                </select>
                                @error('approvalDecision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
                                <label for="approvalCommentsModal" class="form-label fw-semibold">{{ __('Ulasan') }} @if ($approvalDecision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">*</span>@endif</label>
                                <textarea wire:model.lazy="approvalComments" id="approvalCommentsModal" rows="3" class="form-control @error('approvalComments') is-invalid @enderror" placeholder="{{ __('Sila berikan ulasan anda...') }}"></textarea>
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
                                {{ __('Hantar Keputusan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('livewire:initialized', () => { // Changed from livewire:load
                const approvalModalElement = document.getElementById('approvalActionBootstrapModal');
                let approvalModalInstance = null;
                if (approvalModalElement) {
                     approvalModalInstance = new bootstrap.Modal(approvalModalElement);
                }

                @this.on('showApprovalModalJs', () => {
                    if(approvalModalInstance) approvalModalInstance.show();
                });
                @this.on('hideApprovalModalJs', () => {
                    if(approvalModalInstance) approvalModalInstance.hide();
                });
            });
        </script>
    @endif
</div>
