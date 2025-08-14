{{-- resources/views/_partials/_modals/modal-sub-category.blade.php --}}

@pushOnce('custom-css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endPushOnce

<div wire:ignore.self class="modal fade" id="subCategoryModal" tabindex="-1" aria-labelledby="subCategoryModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title d-flex align-items-center" id="subCategoryModalLabel">
            <i class="bi bi-diagram-2-fill me-2 fs-5"></i>
            {{ $isEditMode ? __('Kemaskini Subkategori') : __('Tambah Subkategori Baharu') }} {{-- Assuming $isEditMode from parent --}}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{__('Tutup')}}"></button>
      </div>
      <div class="modal-body p-md-4">
        <form wire:submit.prevent="submitSubCategory" id="subCategoryFormModal" class="row g-3">

          <div class="col-md-12">
            <label for="select2SubCategoryEquipmentCategoryIdModal" class="form-label w-100">{{ __('Kategori Induk Peralatan') }} <span class="text-danger">*</span></label>
            <div wire:ignore>
                <select wire:model='subCategoryEquipmentCategoryId' id="select2SubCategoryEquipmentCategoryIdModal" class="select2-parent-category form-select @error('subCategoryEquipmentCategoryId') is-invalid @enderror" data-placeholder="{{ __('-- Pilih Kategori Induk --') }}">
                  <option value="">{{ __('-- Pilih Kategori Induk --') }}</option>
                  {{-- $equipmentCategoriesForSelection should be passed from the parent Livewire component --}}
                  @foreach($equipmentCategoriesForSelection ?? [] as $category)
                    @if(is_object($category)) {{-- Safeguard for linter and runtime safety --}}
                        <option value="{{ $category->id }}">{{ e($category->name) }}</option>
                    @endif
                  @endforeach
                </select>
            </div>
            @error('subCategoryEquipmentCategoryId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-12">
            <label for="subCategoryNameModalInput" class="form-label w-100">{{ __('Nama Subkategori') }} <span class="text-danger">*</span></label>
            <input wire:model.defer='subCategoryName' id="subCategoryNameModalInput" class="form-control @error('subCategoryName') is-invalid @enderror" type="text" placeholder="{{__('Cth: Projektor LCD Mudah Alih, Laptop Kegunaan Am')}}" />
            @error('subCategoryName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="subCategoryDescriptionModalInput" class="form-label w-100">{{ __('Deskripsi (Pilihan)') }}</label>
            <textarea wire:model.defer='subCategoryDescription' id="subCategoryDescriptionModalInput" class="form-control @error('subCategoryDescription') is-invalid @enderror" rows="3" placeholder="{{__('Terangkan mengenai subkategori ini...')}}"></textarea>
            @error('subCategoryDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input wire:model.defer='subCategoryIsActive' class="form-check-input" type="checkbox" id="subCategoryIsActiveModal" />
              <label class="form-check-label" for="subCategoryIsActiveModal">
                {{ __('Jadikan Subkategori Ini Aktif') }}
              </label>
            </div>
             @error('subCategoryIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
        </button>
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center" form="subCategoryFormModal"
                wire:loading.attr="disabled" wire:target="submitSubCategory">
            <span wire:loading wire:target="submitSubCategory" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            <i class="bi {{ $isEditMode ? 'bi-save-fill' : 'bi-check-lg' }} me-1" wire:loading.remove wire:target="submitSubCategory"></i>
            {{ $isEditMode ? __('Simpan Perubahan') : __('Tambah Subkategori') }}
        </button>
      </div>
    </div>
  </div>
</div>

@pushOnce('custom-scripts')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/i18n/ms.js')}}"></script>
<script>
  document.addEventListener('livewire:initialized', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const subCategoryModalElement = document.getElementById('subCategoryModal');
        const selectEl = $('#select2SubCategoryEquipmentCategoryIdModal');

        const initParentCategorySelect2InModal = () => {
            if (selectEl.length) {
                if (selectEl.hasClass("select2-hidden-accessible")) {
                    selectEl.select2('destroy');
                }
                selectEl.select2({
                    placeholder: "{{ __('Pilih Kategori Induk') }}",
                    dropdownParent: $(subCategoryModalElement),
                    allowClear: false, // Usually a parent category is required
                    language: "ms"
                });
                let currentParentCategoryId = @this.get('subCategoryEquipmentCategoryId');
                selectEl.val(currentParentCategoryId).trigger('change.select2');
            }
        };

        if (subCategoryModalElement && selectEl.length) {
            $(subCategoryModalElement).on('shown.bs.modal', function () {
                initParentCategorySelect2InModal();
            });
            selectEl.on('change', function (e) {
                @this.set('subCategoryEquipmentCategoryId', $(this).val());
            });
        }

        Livewire.on('subCategoryModalOpened', () => {
             if (selectEl.data('select2')) {
                let currentParentCategoryId = @this.get('subCategoryEquipmentCategoryId');
                selectEl.val(currentParentCategoryId).trigger('change.select2');
            }
        });
    } else {
        console.error("jQuery or Select2 is not loaded for sub-category modal.");
    }
  });
</script>
@endPushOnce
