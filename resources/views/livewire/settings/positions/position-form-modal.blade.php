<div x-data="{ showModal: @entangle('showModal').live }" x-show="showModal"
    class="modal fade" tabindex="-1" style="display: {{ $showModal ? 'block' : 'none' }};" aria-modal="true" role="dialog">
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
