<div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"> {{-- Standard centered modal --}}
    <div class="modal-content p-0">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">
            <i class="bi bi-file-earmark-arrow-up-fill me-2"></i> {{-- Icon for import --}}
            {{ __('Import Fail') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{__('Tutup')}}"></button>
      </div>
      <div class="modal-body p-3 p-md-4">
        {{-- <p class="text-muted text-center mb-3">{{ __('Sila pilih fail Excel (.xlsx) untuk dimuat naik.') }}</p> --}}
        <p class="small text-center mb-3">
             {{__('Pastikan fail anda mengikut format yang diperlukan.')}}
             {{-- Example for template download link --}}
             {{-- <a href="#" wire:click.prevent="downloadImportTemplate" title="{{__('Muat Turun Templat')}}"><i class="bi bi-file-earmark-arrow-down"></i> {{__('Muat Turun Templat')}}</a> --}}
             <small class="d-block">{{__('(Rujuk dokumentasi untuk struktur fail)')}}</small>
        </p>
        <form wire:submit.prevent="importFromExcel" class="row g-3">
          <div class="col-12 mb-3">
            <label for="importFile" class="form-label visually-hidden">{{__('Fail')}}</label>
            <input wire:model='file' id="importFile" class="form-control @error('file') is-invalid @enderror" type="file" accept=".xlsx, .csv" /> {{-- Added .csv as common import format --}}
            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div wire:loading wire:target="file" class="text-primary small mt-1">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{__('Memuatnaik fail...')}}
            </div>
          </div>
          <div class="col-12 text-center mt-2">
            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal" aria-label="{{__('Batal')}}" wire:loading.attr="disabled">
                <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
            </button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="importFromExcel, file">
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
