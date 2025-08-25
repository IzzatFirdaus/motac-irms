{{-- resources/views/livewire/settings/positions/position-form-modal.blade.php --}}
{{-- Modal form for creating/updating a Position (Jawatan).
     Used as a partial in positions-index.blade.php.
     Expects Livewire entanglement for $showModal, $isEditMode, and form fields.
--}}

<div x-data="{ show: @entangle('showModal').live }" x-show="show" x-cloak class="modal fade" :class="{'show': show}" style="display: none;" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content motac-modal-content">
            <div class="modal-header motac-modal-header">
                <h5 class="modal-title">{{ $isEditMode ? __('Kemaskini Jawatan') : __('Cipta Jawatan Baru') }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="savePosition">
                <div class="modal-body">
                    {{-- Nama Jawatan --}}
                    <div class="form-group mb-3">
                        <label for="name">{{ __('Nama Jawatan') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                               placeholder="Contoh: Pegawai Teknologi Maklumat" wire:model.defer="name">
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    {{-- Gred Berkaitan --}}
                    <div class="form-group mb-3">
                        <label for="grade_id">{{ __('Gred Berkaitan') }}</label>
                        <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" wire:model.defer="grade_id">
                            <option value="">{{ __('- Pilih Gred -') }}</option>
                            @foreach($gradeOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('grade_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    {{-- Penerangan --}}
                    <div class="form-group mb-3">
                        <label for="description">{{ __('Penerangan (Pilihan)') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3"
                                  placeholder="{{ __('Penerangan tambahan tentang jawatan...') }}" wire:model.defer="description"></textarea>
                        @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    {{-- Aktif --}}
                    <div class="form-group form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_active" wire:model.defer="is_active">
                        <label class="form-check-label" for="is_active">{{ __('Aktif') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEditMode ? __('Kemaskini') : __('Cipta') }}</span>
                        <span wire:loading>{{ __('Menyimpan...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
