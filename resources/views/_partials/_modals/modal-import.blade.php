<div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple">
    <div class="modal-content p-0 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{ __('Import File') }}</h3>
          <p class="text-muted">{{ __('Please select the Excel (.xlsx) file to upload.') }}</p>
          {{-- Add a link to a template or instructions if available --}}
          <p class="small">
            {{-- __('Ensure your file follows the required format. ') }} --}}
            {{-- <a href="#" wire:click.prevent="downloadImportTemplate" title="{{__('Download Template')}}">{{__('Download Template')}}</a> --}}
             <small>{{__('(Refer to documentation for file structure)')}}</small>
          </p>
        </div>
        <form wire:submit.prevent="importFromExcel" class="row g-3">
          <div class="col-12 mb-4">
            <label for="importFile" class="form-label visually-hidden">{{__('File')}}</label>
            <input wire:model='file' id="importFile" class="form-control @error('file') is-invalid @enderror" type="file" accept=".xlsx" />
            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div wire:loading wire:target="file" class="text-primary small mt-1">{{__('Uploading file...')}}</div>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1" wire:loading.attr="disabled" wire:target="importFromExcel, file">
              <span wire:loading.remove wire:target="importFromExcel, file">{{ __('Submit') }}</span>
              <span wire:loading wire:target="importFromExcel, file">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Processing...') }}
              </span>
            </button>
            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close" wire:loading.attr="disabled">{{ __('Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
