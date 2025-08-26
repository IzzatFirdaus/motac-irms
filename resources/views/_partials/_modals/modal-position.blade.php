{{-- resources/views/_partials/_modals/modal-position.blade.php --}}

@pushOnce('custom-css')
<!-- Select2 CSS for enhanced dropdowns -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endPushOnce

<div wire:ignore.self class="modal fade" id="positionModal" tabindex="-1" aria-labelledby="positionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center" id="positionModalLabel">
          <i class="bi bi-diagram-3-fill me-2 fs-5"></i>
          {{-- Dynamic title based on edit mode --}}
          {{ $isEditMode ? __('Kemaskini Jawatan') : __('Tambah Jawatan Baharu') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
      </div>
      <div class="modal-body p-md-4">
        {{-- Position Form --}}
        <form wire:submit.prevent="submitPosition" id="positionFormModal" class="row g-3">
          <div class="col-md-6">
            <label for="positionNameModal" class="form-label w-100">
              {{ __('Nama Jawatan') }} <span class="text-danger">*</span>
            </label>
            <input wire:model.defer='positionName' id="positionNameModal" class="form-control @error('positionName') is-invalid @enderror" type="text" placeholder="{{ __('Cth: Pegawai Teknologi Maklumat') }}" />
            @error('positionName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div wire:ignore class="col-md-6">
            <label for="select2PositionGradeIdModal" class="form-label w-100">
              {{ __('Gred (Pilihan)') }}
            </label>
            <select wire:model='positionGradeId' id="select2PositionGradeIdModal" class="select2-position-grade form-select @error('positionGradeId') is-invalid @enderror" data-placeholder="{{ __('Pilih Gred') }}">
              <option value="">{{ __('Pilih Gred (Pilihan)') }}</option>
              {{-- Grades list fetched from parent component --}}
              @foreach($gradesForSelection ?? [] as $grade)
                @if(is_object($grade))
                  <option value="{{ $grade->id }}">{{ e($grade->name) }}{{ $grade->level ? e(' (L'.$grade->level.')') : '' }}</option>
                @endif
              @endforeach
            </select>
            @error('positionGradeId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="positionDescriptionModal" class="form-label w-100">
              {{ __('Deskripsi (Pilihan)') }}
            </label>
            <textarea wire:model.defer='positionDescription' id="positionDescriptionModal" class="form-control @error('positionDescription') is-invalid @enderror" rows="3" placeholder="{{ __('Terangkan skop atau peranan utama jawatan ini...') }}"></textarea>
            @error('positionDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input wire:model.defer='positionIsActive' class="form-check-input" type="checkbox" id="positionIsActiveModal" />
              <label class="form-check-label" for="positionIsActiveModal">
                {{ __('Jadikan Jawatan Ini Aktif') }}
              </label>
            </div>
            @error('positionIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <!-- Cancel Button -->
        <button type="button" class="btn btn-outline-secondary motac-btn-outline" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
        </button>
        <!-- Save/Submit Button, changes label depending on mode -->
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center motac-btn-primary" form="positionFormModal"
                wire:loading.attr="disabled" wire:target="submitPosition">
          <span wire:loading wire:target="submitPosition" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          <i class="bi {{ $isEditMode ? 'bi-save-fill' : 'bi-check-lg' }} me-1" wire:loading.remove wire:target="submitPosition"></i>
          {{ $isEditMode ? __('Simpan Perubahan') : __('Tambah Jawatan') }}
        </button>
      </div>
    </div>
  </div>
</div>

@pushOnce('custom-scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/i18n/ms.js') }}"></script>
<script>
  // Initialize Select2 for grade selection in modal
  document.addEventListener('livewire:initialized', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const positionModalElement = document.getElementById('positionModal');
        const selectEl = $('#select2PositionGradeIdModal');

        // Function to initialize Select2 dropdown
        const initGradeSelect2InModal = () => {
            if (selectEl.length) {
                 if (selectEl.hasClass("select2-hidden-accessible")) {
                    selectEl.select2('destroy');
                }
                selectEl.select2({
                    placeholder: "{{ __('Pilih Gred (Pilihan)') }}",
                    dropdownParent: $(positionModalElement),
                    allowClear: true,
                    language: "ms"
                });
                let currentGradeId = @this.get('positionGradeId');
                selectEl.val(currentGradeId).trigger('change.select2');
            }
        };

        if (positionModalElement && selectEl.length) {
            $(positionModalElement).on('shown.bs.modal', function () {
                initGradeSelect2InModal();
            });
            selectEl.on('change', function (e) {
                @this.set('positionGradeId', $(this).val());
            });
        }

        // Livewire event to re-initialize Select2 when requested
        Livewire.on('positionModalOpened', () => {
             if (selectEl.data('select2')) {
                let currentGradeId = @this.get('positionGradeId');
                selectEl.val(currentGradeId).trigger('change.select2');
            }
        });
    } else {
        console.error("jQuery or Select2 is not loaded for position modal.");
    }
  });
</script>
@endPushOnce
