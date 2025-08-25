{{-- resources/views/livewire/resource-management/admin/grades/grade-index.blade.php --}}
<div>
    @section('title', __('Pengurusan Gred Jawatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-award-fill me-2"></i>
            {{ __('Senarai Gred Jawatan MOTAC') }}
        </h1>
        @can('create', App\Models\Grade::class)
            <button wire:click="openCreateModal" type="button"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="bi bi-plus-lg {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
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
    <div class="card mb-4 motac-card">
        <div class="card-header py-3 motac-card-header">
            <h5 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i>{{ __('Carian') }}
            </h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <label for="searchTermGrade" class="form-label form-label-sm">{{ __('Carian Nama Gred atau Tahap') }}</label>
            <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTermGrade"
                placeholder="{{ __('Masukkan nama gred atau tahap...') }}"
                class="form-control form-control-sm">
        </div>
    </div>

    {{-- Grades Table --}}
    <div class="card motac-card">
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
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0" style="border:none;">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($gradesList as $grade)
                        <tr wire:key="grade-row-{{ $grade->id }}">
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $grade->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $grade->level ?? '-' }}</td>
                            <td class="px-3 py-2 small">
                                @if($grade->is_approver_grade)
                                    <span class="badge rounded-pill bg-success-lt text-dark">{{ __('Ya') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-lt text-dark">{{ __('Tidak') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 small text-muted">{{ $grade->minApprovalGrade?->name ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $grade->updater->name ?? ($grade->creator->name ?? __('Sistem')) }}
                                <span class="d-block" style="font-size: 0.75rem;">{{ $grade->updated_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-3 py-2 text-end">
                                @can('update', $grade)
                                <button wire:click="openEditModal({{ $grade->id }})" type="button" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1" title="{{ __('Kemaskini') }}">
                                    <i class="bi bi-pencil-fill fs-6 lh-1"></i>
                                </button>
                                @endcan
                                @can('delete', $grade)
                                <button wire:click="openDeleteModal({{ $grade->id }})" type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
                                    <i class="bi bi-trash3-fill fs-6 lh-1"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-table fs-1 mb-2 text-secondary"></i>
                                    <h5 class="mb-1 mx-2">{{ __('Tiada Gred Ditemui') }}</h5>
                                    <p class="mb-3 mx-2 text-muted">
                                      {{ __('Sila tambah gred baharu untuk memulakan.') }}
                                    </p>
                                    @if(empty($searchTerm))
                                     @can('create', App\Models\Grade::class)
                                        <button wire:click="openCreateModal" type="button" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>{{ __('Tambah Gred Baru') }}
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
         @if ($gradesList->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                {{ $gradesList->links() }}
            </div>
        @endif
    </div>

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
                            <i class="bi {{ $showEditModal ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2"></i>
                            {{ $showEditModal ? __('Kemaskini Gred') : __('Tambah Gred Baru') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="modal_grade_name" class="form-label">{{ __('Nama Gred (Cth: N19, 41, JUSA C)') }}<span class="text-danger">*</span></label>
                                <input type="text" wire:model.defer="name" id="modal_grade_name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Masukkan nama unik untuk gred') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="modal_grade_level" class="form-label">{{ __('Tahap Numerik (Untuk susunan & perbandingan)') }}</label>
                                <input type="number" wire:model.defer="level" id="modal_grade_level" min="0" step="1" class="form-control @error('level') is-invalid @enderror" placeholder="{{ __('Cth: 19, 41, 54') }}">
                                <div class="form-text small">{{__('Contoh: Gred 54 lebih tinggi dari 41. Tahap lebih tinggi menandakan senioriti/keutamaan.')}}</div>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="modal_min_approval_grade_id" class="form-label">{{ __('Min. Gred Kelulusan Diperlukan') }}</label>
                                <select wire:model.defer="min_approval_grade_id" id="modal_min_approval_grade_id" class="form-select @error('min_approval_grade_id') is-invalid @enderror">
                                    <option value="">{{ __('- Tiada (Tidak Tertakluk pada Kelulusan Gred Lain) -') }}</option>
                                    @foreach($availableGradesForDropdown as $id => $gradeName)
                                        @if(!($editingGrade && $editingGrade->id == $id))
                                            <option value="{{ $id }}">{{ $gradeName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="form-text small">{{__('Jika gred ini perlukan kelulusan dari gred lain yang lebih tinggi.')}}</div>
                                @error('min_approval_grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check mt-2">
                                    <input wire:model.defer="is_approver_grade" id="modal_is_approver_grade" type="checkbox" class="form-check-input @error('is_approver_grade') is-invalid @enderror" value="1">
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
                            <span wire:loading.remove>
                                <i class="bi bi-save-fill me-1"></i> {{ $showEditModal ? __('Kemaskini Gred') : __('Simpan Gred') }}
                            </span>
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
    <div class="modal fade @if($showDeleteModal && $deletingGrade) show d-block @endif"
         id="deleteGradeModal" tabindex="-1" aria-labelledby="deleteGradeModalLabel"
         @if(!($showDeleteModal && $deletingGrade)) aria-hidden="true" @endif
         @if($showDeleteModal && $deletingGrade) style="background-color: rgba(0,0,0,0.5);" @endif
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteGradeModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Anda Pasti Ingin Memadam Gred Ini?') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-danger me-3 d-none d-sm-block"></i>
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
                        <span wire:loading.remove>
                            <i class="bi bi-trash3-fill me-1"></i>{{ __('Ya, Padam Gred Ini') }}
                        </span>
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
