<div>
    {{-- Page Header and Create Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Pengurusan Jawatan</h3>
        <button wire:click="create" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Cipta Jawatan Baru
        </button>
    </div>

    {{-- Search and Sort Controls --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari jawatan..." class="form-control">
        </div>
        <div class="col-md-6 text-right">
            {{-- Optional: Add sort field/direction selectors here if needed outside table headers --}}
        </div>
    </div>

    {{-- Positions Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('name')" role="button">
                                Nama Jawatan
                                @if ($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('grade_id')" role="button">
                                Gred Berkaitan
                                @if ($sortField === 'grade_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('is_active')" role="button">
                                Status
                                @if ($sortField === 'is_active')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1"></i>
                                @endif
                            </th>
                            <th class="text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($positions as $position)
                            <tr>
                                <td>{{ $position->name }}</td>
                                <td>{{ $position->grade->name ?? 'N/A' }}</td> {{-- Display grade name --}}
                                <td>
                                    <span class="badge {{ $position->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $position->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button wire:click="edit({{ $position->id }})" class="btn btn-sm btn-outline-primary mr-2" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmPositionDeletion({{ $position->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tiada jawatan ditemui.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light border-top py-3 d-flex justify-content-center">
            {{ $positions->links() }}
        </div>
    </div>

    {{-- Position Form Modal (Create/Edit) --}}
    <div x-data="{ show: @entangle('showModal').live }" x-show="show" class="modal fade" tabindex="-1" style="display: {{ $showModal ? 'block' : 'none' }};" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Edit Jawatan' : 'Cipta Jawatan Baru' }}</h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="savePosition">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Nama Jawatan<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Contoh: Pegawai Teknologi Maklumat" wire:model.blur="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="grade_id">Gred Berkaitan</label>
                            <select class="form-control @error('grade_id') is-invalid @enderror" id="grade_id" wire:model.blur="grade_id">
                                <option value="">- Pilih Gred -</option>
                                @foreach($gradeOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('grade_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Penerangan (Pilihan)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Penerangan tambahan tentang jawatan..." wire:model.blur="description"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_active" wire:model.live="is_active">
                            <label class="form-check-label" for="is_active">Aktif</label>
                            @error('is_active') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditMode ? 'Kemaskini' : 'Cipta' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ show: @entangle('showDeleteConfirmationModal').live }" x-show="show" class="modal fade" tabindex="-1" style="display: {{ $showDeleteConfirmationModal ? 'block' : 'none' }};" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sahkan Pemadaman Jawatan</h5>
                    <button type="button" class="close" wire:click="closeDeleteConfirmationModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Adakah anda pasti ingin memadam jawatan "<strong>{{ $positionNameToDelete }}</strong>"? Tindakan ini tidak boleh diundur.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteConfirmationModal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePosition">Padam</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Livewire Flash Messages --}}
    @if (session()->has('message') || session()->has('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div class="alert alert-{{ session()->has('message') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ session('message') ?? session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // To ensure modals correctly show/hide with Bootstrap's JS if needed
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-position-modal', () => {
            var modal = new bootstrap.Modal(document.getElementById('positionFormModal'));
            modal.show();
        });

        Livewire.on('hide-position-modal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('positionFormModal'));
            if (modal) {
                modal.hide();
            }
        });

        Livewire.on('show-delete-modal', () => {
            var modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();
        });

        Livewire.on('hide-delete-modal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
            if (modal) {
                modal.hide();
            }
        });
    });
</script>
@endpush
