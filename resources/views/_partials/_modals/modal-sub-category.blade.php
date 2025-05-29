@push('custom-css')
{{-- If using Select2 for Equipment Category --}}
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endpush

<div wire:ignore.self class="modal fade" id="subCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple"> {{-- Adjusted size for more fields --}}
    <div class="modal-content p-0 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{ $isEdit ? __('Update SubCategory') : __('New SubCategory') }}</h3>
          <p class="text-muted">{{ __('Please fill out the following information') }}</p>
        </div>
        <form wire:submit.prevent="submitSubCategory" class="row g-3">

          {{-- Equipment Category Dropdown --}}
          {{-- Assuming $equipmentCategories is passed from Livewire component --}}
          <div class="col-md-12 mb-3">
            <label for="select2SubCategoryEquipmentCategoryId" class="form-label w-100">{{ __('Parent Equipment Category') }} <span class="text-danger">*</span></label>
            <div wire:ignore> {{-- wire:ignore for Select2 compatibility --}}
                <select wire:model.defer='subCategoryEquipmentCategoryId' id="select2SubCategoryEquipmentCategoryId" class="select2 form-select @error('subCategoryEquipmentCategoryId') is-invalid @enderror">
                  <option value="">{{ __('Select Parent Category') }}</option>
                  @foreach($equipmentCategoriesForSelection ?? [] as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
            </div>
            @error('subCategoryEquipmentCategoryId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          {{-- SubCategory Name --}}
          <div class="col-md-12 mb-3">
            <label for="subCategoryNameInput" class="form-label w-100">{{ __('SubCategory Name') }} <span class="text-danger">*</span></label>
            <input wire:model.defer='subCategoryName' id="subCategoryNameInput" class="form-control @error('subCategoryName') is-invalid @enderror" type="text" />
            @error('subCategoryName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Description --}}
          <div class="col-12 mb-3">
            <label for="subCategoryDescriptionInput" class="form-label w-100">{{ __('Description') }}</label>
            <textarea wire:model.defer='subCategoryDescription' id="subCategoryDescriptionInput" class="form-control @error('subCategoryDescription') is-invalid @enderror" rows="3"></textarea>
            @error('subCategoryDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Is Active Checkbox --}}
          <div class="col-12 mb-4">
            <div class="form-check">
              <input wire:model.defer='subCategoryIsActive' class="form-check-input" type="checkbox" id="subCategoryIsActive" />
              <label class="form-check-label" for="subCategoryIsActive">
                {{ __('Is Active') }}
              </label>
            </div>
             @error('subCategoryIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          {{-- Action Buttons --}}
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1" wire:loading.attr="disabled" wire:target="submitSubCategory">
                <span wire:loading.remove wire:target="submitSubCategory">{{ __('Submit') }}</span>
                <span wire:loading wire:target="submitSubCategory">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Processing...') }}
                </span>
            </button>
            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">{{ __('Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
{{-- If using Select2 for Equipment Category --}}
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
  document.addEventListener('livewire:load', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const initParentCategorySelect2 = () => {
            const selectEl = $('#select2SubCategoryEquipmentCategoryId');
            if (selectEl.length) {
                if (selectEl.data('select2')) { // Destroy if already initialized
                    selectEl.select2('destroy');
                }
                selectEl.select2({ // Removed wrap() as it can cause issues if re-initialized multiple times without unwrapping
                    placeholder: "{{ __('Select Parent Category') }}",
                    dropdownParent: $('#subCategoryModal'), // Attach to modal
                    allowClear: true
                }).on('change', function (e) {
                    @this.set('subCategoryEquipmentCategoryId', $(this).val());
                });
            }
        };

        initParentCategorySelect2(); // Initial call

        // Listen for an event from Livewire if you need to re-initialize or update Select2
        // For example, after the modal is opened or data is loaded.
        Livewire.on('subCategoryModalOpened', () => { // Example event name
            initParentCategorySelect2();
            // If subCategoryEquipmentCategoryId is set in Livewire, trigger change for Select2
            let currentParentCategoryId = @this.get('subCategoryEquipmentCategoryId');
            if(currentParentCategoryId) {
                 $('#select2SubCategoryEquipmentCategoryId').val(currentParentCategoryId).trigger('change.select2');
            } else {
                 $('#select2SubCategoryEquipmentCategoryId').val(null).trigger('change.select2');
            }
        });

        // Re-initialize Select2 when the Bootstrap modal is shown
        // This helps if the modal's content is dynamically rendered or if Select2 loses its state
        $('#subCategoryModal').on('shown.bs.modal', function () {
            initParentCategorySelect2();
            let currentParentCategoryId = @this.get('subCategoryEquipmentCategoryId'); // Ensure this reflects current state
            if(currentParentCategoryId) {
                 $('#select2SubCategoryEquipmentCategoryId').val(currentParentCategoryId).trigger('change.select2');
            } else {
                 $('#select2SubCategoryEquipmentCategoryId').val(null).trigger('change.select2');
            }
        });

    } else {
        console.error("jQuery or Select2 is not loaded for sub-category modal.");
    }
  });
</script>
@endpush
