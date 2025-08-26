{{-- resources/views/_partials/_modals/modal-category.blade.php --}}
{{-- Modal for creating/editing equipment categories --}}

@push('custom-css')
    {{-- No custom CSS needed for this basic modal if using global MOTAC theme --}}
@endpush

<div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    {{-- Use standard Bootstrap modal sizing with MOTAC theme --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content motac-modal-content"> {{-- Reduced default padding, specific padding in header/body/footer --}}
            <div class="modal-header motac-modal-header"> {{-- Modal header for structure --}}
                <h5 class="modal-title" id="categoryModalLabel">
                    {{-- Icon for edit/new based on Design Language principles --}}
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-lg' }} me-2" aria-hidden="true"></i>
                    {{ $isEdit ? __('Kemaskini Kategori') : __('Kategori Baharu') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Tutup') }}"></button>
            </div>
            <div class="modal-body p-3 p-md-4"> {{-- Modal body with responsive padding --}}
                <form wire:submit.prevent="submitCategory" class="row g-3">
                    <div class="col-12 mb-3">
                        <label for="categoryNameInput" class="form-label w-100">{{ __('Nama Kategori') }} <span
                                class="text-danger">*</span></label>
                        <input wire:model='categoryName' id="categoryNameInput"
                            class="form-control @error('categoryName') is-invalid @enderror" type="text"
                            placeholder="{{ __('Cth: Komputer & Laptop') }}" />
                        @error('categoryName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-center mt-2 motac-modal-footer">
                        <button type="button" class="motac-btn-outline me-2" data-bs-dismiss="modal"
                            aria-label="{{ __('Batal') }}">
                            <i class="bi bi-x-lg me-1" aria-hidden="true"></i>{{ __('Batal') }}
                        </button>
                        <button type="submit" class="motac-btn-primary" aria-label="{{ __('Hantar') }}">
                            <span wire:loading wire:target="submitCategory"
                                class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <i class="bi bi-check-lg me-1" wire:loading.remove aria-hidden="true"></i>
                            {{ __('Hantar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
    {{-- No specific scripts needed for this basic modal --}}
@endpush
