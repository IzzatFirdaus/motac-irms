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

    {{-- Alerts: Assuming you have a standard alert component --}}
    {{-- @include('layouts.sections.components.alert-general-bootstrap') --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- Search Input --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div>
                <label for="searchTermGrade" class="form-label">{{ __('Carian Nama Gred atau Tahap') }}</label>
                <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTermGrade"
                    placeholder="{{ __('Masukkan nama gred atau tahap...') }}"
                    class="form-control form-control-sm">
            </div>
        </div>
    </div>

    {{-- Grades Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Gred') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tahap (Level)') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Boleh Melulus?') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Min. Gred Kelulusan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dikemaskini Oleh') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0">

                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($gradesList as $grade)
                        <tr wire:key="grade-{{ $grade->id }}">
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $grade->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $grade->level ?? '-' }}</td>
                            <td class="px-3 py-2 small">
                                @if($grade->is_approver_grade)
                                    <span class="badge rounded-pill {{ \App\Helpers\Helpers::getBootstrapStatusColorClass('active') }}">{{ __('Ya') }}</span>
                                @else
                                    <span class="badge rounded-pill {{ \App\Helpers\Helpers::getBootstrapStatusColorClass('inactive') }}">{{ __('Tidak') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 small text-muted">{{ $grade->minApprovalGrade?->name ?: '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $grade->updater->name ?? $grade->creator->name ?? '-' }}
                                <span class="d-block" style="font-size: 0.75rem;">{{ $grade->updated_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-3 py-2 text-end">
                                @can('update', $grade)
                                <button wire:click="openEditModal({{ $grade->id }})" type="button" class="btn btn-sm btn-outline-secondary border-0 p-1" title="{{ __('Kemaskini') }}">
                                    <i class="ti ti-pencil fs-6"></i>
                                </button>
                                @endcan
                                @can('delete', $grade)
                                <button wire:click="openDeleteModal({{ $grade->id }})" type="button" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1" title="{{ __('Padam') }}">
                                    <i class="ti ti-trash fs-6"></i>
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
                                        <button wire:click="openCreateModal" type="button" class="btn btn-sm btn-primary mt-3">
                                            {{ __('Tambah Gred Pertama') }}
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
    </div>

    {{-- Pagination --}}
    @if ($gradesList->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $gradesList->links() }}
        </div>
    @endif

    {{-- Create/Edit Grade Modal --}}
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
                            <div class="col-12">
                                <label for="modal_grade_name" class="form-label">{{ __('Nama Gred (Cth: N19, 41, JUSA C)') }}<span class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="name" id="modal_grade_name" class="form-control @error('name') is-invalid @enderror">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="modal_grade_level" class="form-label">{{ __('Tahap Numerik (Untuk susunan & perbandingan)') }}</label>
                                <input type="number" wire:model.defer="level" id="modal_grade_level" min="1" max="100" class="form-control @error('level') is-invalid @enderror">
                                <div class="form-text small">{{__('Contoh: Gred 54 lebih tinggi dari 41. Tahap lebih tinggi menandakan senioriti.')}}</div>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="modal_min_approval_grade_id" class="form-label">{{ __('Min. Gred Diperlukan untuk Meluluskan Permohonan Gred Ini') }}</label>
                                <select wire:model.defer="min_approval_grade_id" id="modal_min_approval_grade_id" class="form-select @error('min_approval_grade_id') is-invalid @enderror">
                                    <option value="">{{ __('- Tiada (Tidak Memerlukan Kelulusan Gred Lain) -') }}</option>
                                    @foreach($availableGradesForDropdown as $id => $gradeName)
                                        @if(!($editingGrade && $editingGrade->id == $id)) {{-- Prevent selecting self --}}
                                            <option value="{{ $id }}">{{ $gradeName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('min_approval_grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check mt-2">
                                    <input wire:model.defer="is_approver_grade" id="modal_is_approver_grade" type="checkbox" class="form-check-input @error('is_approver_grade') is-invalid @enderror" value="1">
                                    <label for="modal_is_approver_grade" class="form-check-label">{{ __('Gred Ini Boleh Meluluskan Permohonan Lain?') }}</label>
                                    <div class="form-text small">{{__('Tandakan jika pengguna dengan gred ini mempunyai tanggungjawab meluluskan permohonan.')}}</div>
                                    @error('is_approver_grade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.attr="disabled">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $showEditModal ? __('Kemaskini') : __('Simpan') }}</span>
                            <span wire:loading class="d-inline-flex align-items-center">
                                {{ __('Memproses...') }} <div class="spinner-border spinner-border-sm ms-1" role="status"><span class="visually-hidden">Loading...</span></div>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade @if($showDeleteModal && $deletingGrade) show d-block @endif"
         id="deleteGradeModal" tabindex="-1" aria-labelledby="deleteGradeModalLabel"
         @if(!($showDeleteModal && $deletingGrade)) aria-hidden="true" @endif
         @if($showDeleteModal && $deletingGrade) style="background-color: rgba(0,0,0,0.5);" @endif
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteGradeModalLabel">{{ __('Padam Gred') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-triangle fs-2 text-danger me-3"></i>
                        <div>
                            <p class="mb-1">
                                {{ __('Adakah anda pasti ingin memadam gred') }}
                                @if($deletingGrade)
                                    <strong class="d-block mt-1 fs-5">{{ $deletingGrade->name }}</strong>?
                                @endif
                            </p>
                            <p class="small text-muted">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.attr="disabled">{{ __('Batal') }}</button>
                    <button wire:click="deleteGrade" type="button" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Padam') }}</span>
                        <span wire:loading class="d-inline-flex align-items-center">
                            {{ __('Memadam...') }} <div class="spinner-border spinner-border-sm ms-1" role="status"><span class="visually-hidden">Loading...</span></div>
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
        let gradeFormModalInstance = null;
        let deleteGradeModalInstance = null;

        if (gradeFormModalEl) {
            gradeFormModalInstance = new bootstrap.Modal(gradeFormModalEl);
        }
        if (deleteGradeModalEl) {
            deleteGradeModalInstance = new bootstrap.Modal(deleteGradeModalEl);
        }

        @this.on('show-create-edit-modal', () => {
            if(gradeFormModalInstance) gradeFormModalInstance.show();
        });

        @this.on('show-delete-modal', () => {
            if(deleteGradeModalInstance) deleteGradeModalInstance.show();
        });

        @this.on('hide-modal', () => {
            if(gradeFormModalInstance) {
                 const livewireModalInstance = bootstrap.Modal.getInstance(gradeFormModalEl);
                 if (livewireModalInstance && livewireModalInstance._isShown) {
                    livewireModalInstance.hide();
                 }
            }
            if(deleteGradeModalInstance) {
                const livewireDeleteModalInstance = bootstrap.Modal.getInstance(deleteGradeModalEl);
                if (livewireDeleteModalInstance && livewireDeleteModalInstance._isShown) {
                    livewireDeleteModalInstance.hide();
                }
            }
        });

        // Ensure Livewire is aware when Bootstrap closes the modal via backdrop click or ESC
        if (gradeFormModalEl) {
            gradeFormModalEl.addEventListener('hidden.bs.modal', (event) => {
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
