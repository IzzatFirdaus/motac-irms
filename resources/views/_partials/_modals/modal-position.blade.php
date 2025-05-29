@push('custom-css')
{{-- If using Select2 for Grade --}}
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endpush

<div wire:ignore.self class="modal fade" id="positionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple"> {{-- Increased size for more fields --}}
    <div class="modal-content p-0 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{ $isEdit ? __('Update Position') : __('New Position') }}</h3>
          <p class="text-muted">{{ __('Please fill out the following information') }}</p>
        </div>
        <form wire:submit.prevent="submitPosition" class="row g-3">
          <div class="col-md-6 mb-3">
            <label for="positionName" class="form-label w-100">{{ __('Name') }} <span class="text-danger">*</span></label>
            <input wire:model.defer='positionName' id="positionName" class="form-control @error('positionName') is-invalid @enderror" type="text" />
            @error('positionName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Assuming $grades is passed from Livewire component for Grade selection --}}
          <div wire:ignore class="col-md-6 mb-3">
            <label for="select2PositionGradeId" class="form-label w-100">{{ __('Grade') }}</label>
            <select wire:model.defer='positionGradeId' id="select2PositionGradeId" class="select2 form-select @error('positionGradeId') is-invalid @enderror">
              <option value="">{{ __('Select Grade (Optional)') }}</option>
              @foreach($gradesForSelection ?? [] as $grade)
                <option value="{{ $grade->id }}">{{ $grade->name }} {{ $grade->level ? ' (L'.$grade->level.')' : '' }}</option>
              @endforeach
            </select>
            @error('positionGradeId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 mb-3">
            <label for="positionDescription" class="form-label w-100">{{ __('Description') }}</label>
            <textarea wire:model.defer='positionDescription' id="positionDescription" class="form-control @error('positionDescription') is-invalid @enderror" rows="3"></textarea>
            @error('positionDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- vacanciesCount was removed as it's not in the DB design for positions table.
               If needed, the DB schema (Section 4.1) should be updated first. --}}

          <div class="col-12 mb-4">
            <div class="form-check">
              <input wire:model.defer='positionIsActive' class="form-check-input" type="checkbox" id="positionIsActive" checked />
              <label class="form-check-label" for="positionIsActive">
                {{ __('Is Active') }}
              </label>
            </div>
             @error('positionIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>


          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">
              <span wire:loading wire:target="submitPosition" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              {{ __('Submit') }}</button>
            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">{{ __('Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
{{-- If using Select2 for Grade --}}
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
  document.addEventListener('livewire:load', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const initGradeSelect2 = () => {
            const selectEl = $('#select2PositionGradeId');
            if (selectEl.length) {
                 if (selectEl.data('select2')) { // Destroy if already initialized
                    selectEl.select2('destroy');
                }
                selectEl.wrap('<div class="position-relative"></div>').select2({
                    placeholder: "{{ __('Select Grade (Optional)') }}",
                    dropdownParent: $('#positionModal'), // Attach to modal
                    allowClear: true
                }).on('change', function (e) {
                    @this.set('positionGradeId', $(this).val());
                });
            }
        };

        initGradeSelect2(); // Initial call

        Livewire.on('positionModalOpened', () => { // Or a more specific event
            initGradeSelect2();
            let currentGradeId = @this.get('positionGradeId');
            if(currentGradeId) {
                 $('#select2PositionGradeId').val(currentGradeId).trigger('change.select2');
            } else {
                 $('#select2PositionGradeId').val(null).trigger('change.select2');
            }
        });
         // Re-initialize Select2 when the modal is shown
        $('#positionModal').on('shown.bs.modal', function () {
            initGradeSelect2();
            let currentGradeId = @this.get('positionGradeId'); // Ensure this reflects current state
            if(currentGradeId) {
                 $('#select2PositionGradeId').val(currentGradeId).trigger('change.select2');
            } else {
                 $('#select2PositionGradeId').val(null).trigger('change.select2');
            }
        });

    } else {
        console.error("jQuery or Select2 is not loaded for position modal.");
    }
  });
</script>
@endpush
