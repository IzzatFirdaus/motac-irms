{{-- resources/views/_partials/_modals/modal-import.blade.php --}}
{{-- Generic import modal for Excel/CSV file uploads --}}

<div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-0">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">
            <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>
            {{ __('Import Fail') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{__('Tutup')}}"></button>
      </div>
      <div class="modal-body p-3 p-md-4">
        <p class="small text-center mb-3">
             {{__('Pastikan fail anda mengikut format yang diperlukan.')}}
             <small class="d-block">{{__('(Rujuk dokumentasi untuk struktur fail)')}}</small>
        </p>
        {{-- File import form with validation and loading states --}}
        <form wire:submit.prevent="importFromExcel" class="row g-3">
          <div class="col-12 mb-3">
            <label for="importFile" class="form-label visually-hidden">{{__('Fail')}}</label>
            <input wire:model='file' id="importFile" class="form-control @error('file') is-invalid @enderror" type="file" accept=".xlsx, .csv" />
            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            {{-- Loading indicator while file is being uploaded --}}
            <div wire:loading wire:target="file" class="text-primary small mt-1">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{__('Memuatnaik fail...')}}
            </div>
          </div>
          <div class="col-12 text-center mt-2">
            <button type="button" class="btn btn-outline-secondary motac-btn-outline me-2" data-bs-dismiss="modal" aria-label="{{__('Batal')}}" wire:loading.attr="disabled">
                <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
            </button>
            <button type="submit" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled" wire:target="importFromExcel, file">
              {{-- Dynamic button content based on loading state --}}
              <span wire:loading.remove wire:target="importFromExcel, file">
                <i class="bi bi-upload me-1"></i> {{ __('Hantar & Proses') }}
              </span>
              <span wire:loading wire:target="importFromExcel, file">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ __('Memproses...') }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
