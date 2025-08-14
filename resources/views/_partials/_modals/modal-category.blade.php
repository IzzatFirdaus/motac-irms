@push('custom-css')
    {{-- No custom CSS needed for this basic modal if using global MOTAC theme --}}
@endpush

<div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    {{-- Removed modal-simple, rely on standard Bootstrap modal sizing or custom MOTAC theme --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-0"> {{-- Reduced default padding, specific padding in header/body/footer --}}
            <div class="modal-header"> {{-- Added modal-header for structure --}}
                <h5 class="modal-title" id="categoryModalLabel">
                    {{-- Icon added based on Design Language principles for titles --}}
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-lg' }} me-2"></i>
                    {{ $isEdit ? __('Kemaskini Kategori') : __('Kategori Baharu') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Tutup') }}"></button>
            </div>
            <div class="modal-body p-3 p-md-4"> {{-- Adjusted padding --}}
                {{-- Removed text-center for title as it's now in header --}}
                {{-- <p class="text-muted text-center mb-4">{{ __('Sila lengkapkan maklumat berikut') }}</p> --}}
                <form wire:submit.prevent="submitCategory" class="row g-3">
                    <div class="col-12 mb-3"> {{-- Adjusted margin --}}
                        <label for="categoryNameInput" class="form-label w-100">{{ __('Nama Kategori') }} <span
                                class="text-danger">*</span></label>
                        <input wire:model='categoryName' id="categoryNameInput"
                            class="form-control @error('categoryName') is-invalid @enderror" type="text" />
                        @error('categoryName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-center mt-2"> {{-- Adjusted margin --}}
                        {{-- Changed btn-label-secondary to btn-outline-secondary as per Design Doc for standard Bootstrap buttons --}}
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal"
                            aria-label="{{ __('Batal') }}">
                            <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="submitCategory"
                                class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <i class="bi bi-check-lg me-1" wire:loading.remove></i>
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
