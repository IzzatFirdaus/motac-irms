{{-- resources/views/livewire/resource-management/admin/grades/index.blade.php (Assumed path) --}}
<div>
    @section('title', __('Pengurusan Gred Jawatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Senarai Gred Jawatan MOTAC') }}</h1>
        @can('create', App\Models\Grade::class)
            <button wire:click="openCreateModal" type="button"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="ti ti-plus {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Tambah Gred Baru') }}
            </button>
        @endcan
    </div>

    {{-- Alerts --}}
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" :dismissible="true" class="mb-3"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" :dismissible="true" class="mb-3"/>
    @endif

    {{-- Search Input --}}
    <x-card class="mb-4">
        <div class="p-3"> {{-- Simplified card structure if no title needed --}}
            <label for="searchTermGrade" class="form-label">{{ __('Carian Nama Gred atau Tahap') }}</label>
            <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTermGrade"
                placeholder="{{ __('Masukkan nama gred atau tahap...') }}"
                class="form-control form-control-sm">
        </div>
    </x-card>

    {{-- Grades Table --}}
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Gred') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tahap (Level)') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Boleh Melulus?') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Min. Gred Kelulusan Diperlukan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dikemaskini Oleh') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Livewire loading indicator for table body --}}
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0">
                            {{-- Progress bar fills the entire cell, ensure styling is appropriate or use a more subtle row overlay --}}
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-valuenow="100" aria-busy="true">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($gradesList as $grade)
                        <tr wire:key="grade-row-{{ $grade->id }}"> {{-- Ensure unique wire:key --}}
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $grade->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $grade->level ?? '-' }}</td>
                            <td class="px-3 py-2 small">
                                {{-- System Design: grades.is_approver_grade (boolean) --}}
                                @if($grade->is_approver_grade)
                                    <span class="badge rounded-pill bg-success-lt">{{ __('Ya') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-lt">{{ __('Tidak') }}</span>
                                @endif
                            </td>
                            {{-- System Design: grades.min_approval_grade_id (links to grades.id) --}}
                            <td class="px-3 py-2 small text-muted">{{ $grade->minApprovalGrade?->name ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{-- Displaying updater or creator name as per BlameableObserver --}}
                                {{ $grade->updater->name ?? ($grade->creator->name ?? __('Sistem')) }}
                                <span class="d-block" style="font-size: 0.75rem;">{{ $grade->updated_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-3 py-2 text-end">
                                @can('update', $grade)
                                <button wire:click="openEditModal({{ $grade->id }})" type="button" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1" title="{{ __('Kemaskini') }}">
                                    <i class="ti ti-pencil fs-6 lh-1"></i>
                                </button>
                                @endcan
                                @can('delete', $grade)
                                <button wire:click="openDeleteModal({{ $grade->id }})" type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
                                    <i class="ti ti-trash fs-6 lh-1"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-mood-empty fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada rekod gred ditemui.') }}
                                    @if(empty($searchTerm))
                                     @can('create', App\Models\Grade::class)
                                        <button wire:click="openCreateModal" type="button" class="btn btn-sm btn-outline-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>{{ __('Tambah Gred Pertama') }}
                                        </button>
                                     @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Pagination --}}
    @if ($gradesList->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $gradesList->links() }}
        </div>
    @endif

    {{-- Create/Edit Grade Modal --}}
    {{-- The modal structure is good and handles Livewire state for visibility. --}}
    {{-- Ensure fields in the modal align with grades table: name, level, is_approver_grade, min_approval_grade_id --}}
    <div class="modal fade @if($showCreateModal || $showEditModal) show d-block @endif"
         id="gradeFormModal" tabindex="-1" aria-labelledby="gradeFormModalLabel"
         @if(!($showCreateModal || $showEditModal)) aria-hidden="true" @endif
         @if($showCreateModal || $showEditModal) style="background-color: rgba(0,0,0,0.5);" @endif
         wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $showEditModal ? 'updateGrade' : 'createGrade' }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gradeFormModalLabel">
                            {{ $showEditModal ? __('Kemaskini Gred') : __('Tambah Gred Baru') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-12">
                                <label for="modal_grade_name" class="form-label">{{ __('Nama Gred (Cth: N19, 41, JUSA C)') }}<span class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="name" id="modal_grade_name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Masukkan nama unik untuk gred') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            {{-- Level --}}
                            <div class="col-md-6">
                                <label for="modal_grade_level" class="form-label">{{ __('Tahap Numerik (Untuk susunan & perbandingan)') }}</label>
                                <input type="number" wire:model.defer="level" id="modal_grade_level" min="0" step="1" class="form-control @error('level') is-invalid @enderror" placeholder="{{ __('Cth: 19, 41, 54') }}">
                                <div class="form-text small">{{__('Contoh: Gred 54 lebih tinggi dari 41. Tahap lebih tinggi menandakan senioriti/keutamaan.')}}</div>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            {{-- Min Approval Grade ID --}}
                            <div class="col-md-6">
                                <label for="modal_min_approval_grade_id" class="form-label">{{ __('Min. Gred Kelulusan Diperlukan') }}</label>
                                <select wire:model.defer="min_approval_grade_id" id="modal_min_approval_grade_id" class="form-select @error('min_approval_grade_id') is-invalid @enderror">
                                    <option value="">{{ __('- Tiada (Tidak Tertakluk pada Kelulusan Gred Lain) -') }}</option>
                                    {{-- $availableGradesForDropdown should exclude the current $editingGrade if it exists --}}
                                    @foreach($availableGradesForDropdown as $id => $gradeName)
                                        @if(!($editingGrade && $editingGrade->id == $id))
                                            <option value="{{ $id }}">{{ $gradeName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="form-text small">{{__('Jika gred ini perlukan kelulusan dari gred lain yang lebih tinggi.')}}</div>
                                @error('min_approval_grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            {{-- Is Approver Grade --}}
                            <div class="col-12">
                                <div class="form-check mt-2">
                                    <input wire:model.defer="is_approver_grade" id="modal_is_approver_grade" type="checkbox" class="form-check-input @error('is_approver_grade') is-invalid @enderror" value="1"> {{-- Ensure value="1" for checkbox to correctly cast to boolean --}}
                                    <label for="modal_is_approver_grade" class="form-check-label">{{ __('Gred Ini Mempunyai Kuasa Melulus?') }}</label>
                                    <div class="form-text small">{{__('Tandakan jika pengguna dengan gred ini boleh meluluskan permohonan (cth: Email, ICT Loan).')}}</div>
                                    @error('is_approver_grade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.attr="disabled">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="ti ti-device-floppy me-1"></i> {{ $showEditModal ? __('Kemaskini Gred') : __('Simpan Gred') }}</span>
                            <span wire:loading class="d-inline-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-1" role="status"></div>
                                {{ __('Memproses...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    {{-- This modal structure is good. --}}
    <div class="modal fade @if($showDeleteModal && $deletingGrade) show d-block @endif"
         id="deleteGradeModal" tabindex="-1" aria-labelledby="deleteGradeModalLabel"
         @if(!($showDeleteModal && $deletingGrade)) aria-hidden="true" @endif
         @if($showDeleteModal && $deletingGrade) style="background-color: rgba(0,0,0,0.5);" @endif
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteGradeModalLabel">{{ __('Anda Pasti Ingin Memadam Gred Ini?') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center"> {{-- Changed to align items center for better icon placement --}}
                        <i class="ti ti-alert-triangle fs-1 text-danger me-3"></i> {{-- Increased icon size --}}
                        <div>
                            <p class="mb-2">
                                {{ __('Anda akan memadam gred:') }}
                                @if($deletingGrade)
                                    <strong class="d-block mt-1 fs-5 text-dark">{{ $deletingGrade->name }}</strong>
                                @endif
                            </p>
                            <p class="small text-muted mb-0">{{ __('Tindakan ini adalah muktamad dan tidak boleh diundur. Memadam gred mungkin memberi kesan kepada pengguna dan proses kelulusan sedia ada.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.attr="disabled">{{ __('Batal') }}</button>
                    <button wire:click="deleteGrade" type="button" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="ti ti-trash me-1"></i>{{ __('Ya, Padam Gred Ini') }}</span>
                        <span wire:loading class="d-inline-flex align-items-center">
                             <div class="spinner-border spinner-border-sm me-1" role="status"></div>
                            {{ __('Memadam...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const gradeFormModalEl = document.getElementById('gradeFormModal');
        const deleteGradeModalEl = document.getElementById('deleteGradeModal');
        // Initialize Bootstrap modals once
        const gradeFormModalInstance = gradeFormModalEl ? new bootstrap.Modal(gradeFormModalEl) : null;
        const deleteGradeModalInstance = deleteGradeModalEl ? new bootstrap.Modal(deleteGradeModalEl) : null;

        @this.on('show-create-edit-modal', () => {
            if(gradeFormModalInstance) gradeFormModalInstance.show();
        });

        @this.on('show-delete-modal', () => {
            if(deleteGradeModalInstance) deleteGradeModalInstance.show();
        });

        @this.on('hide-modal', () => {
            // Check if modal is currently shown before trying to hide
            if(gradeFormModalInstance && bootstrap.Modal.getInstance(gradeFormModalEl)?._isShown) {
                 gradeFormModalInstance.hide();
            }
            if(deleteGradeModalInstance && bootstrap.Modal.getInstance(deleteGradeModalEl)?._isShown) {
                deleteGradeModalInstance.hide();
            }
        });

        // Sync Livewire state when Bootstrap closes modal via backdrop or ESC
        if (gradeFormModalEl) {
            gradeFormModalEl.addEventListener('hidden.bs.modal', (event) => {
                // Only call closeModal if Livewire still thinks the modal should be open
                if (@this.get('showCreateModal') || @this.get('showEditModal')) {
                    @this.call('closeModal');
                }
            });
        }
        if (deleteGradeModalEl) {
            deleteGradeModalEl.addEventListener('hidden.bs.modal', (event) => {
                 if (@this.get('showDeleteModal')) {
                    @this.call('closeModal');
                }
            });
        }
    });
</script>
@endpush
