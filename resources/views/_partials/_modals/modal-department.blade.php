{{-- resources/views/_partials/_modals/modal-department.blade.php --}}
{{-- Modal for creating/editing department records with Select2 integration --}}

@pushOnce('custom-css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endPushOnce

<div wire:ignore.self class="modal fade" id="departmentModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="departmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="departmentModalLabel">
            <i class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-building-fill-add' }} me-2 fs-5"></i>
            {{ $isEditMode ? __('Kemaskini Jabatan') : __('Tambah Jabatan Baharu') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{__('Tutup')}}"></button>
      </div>
      <div class="modal-body p-3 p-md-4">
        {{-- Department form with responsive grid layout --}}
        <form wire:submit.prevent="submitDepartment" id="departmentFormModal" class="row g-3">
          <div class="col-md-6">
            <label for="departmentNameModal" class="form-label w-100">{{ __('Nama Jabatan') }} <span class="text-danger">*</span></label>
            <input wire:model.defer='departmentName' id="departmentNameModal" class="form-control @error('departmentName') is-invalid @enderror" type="text" placeholder="{{__('Cth: Bahagian Kewangan')}}"/>
            @error('departmentName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="departmentCodeModal" class="form-label w-100">{{ __('Kod Jabatan') }}</label>
            <input wire:model.defer='departmentCode' id="departmentCodeModal" class="form-control @error('departmentCode') is-invalid @enderror" type="text" placeholder="{{__('Cth: KEW, ICT')}}"/>
            @error('departmentCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label for="departmentBranchTypeModal" class="form-label w-100">{{ __('Jenis Cawangan') }} <span class="text-danger">*</span></label>
            <select wire:model.defer='departmentBranchType' id="departmentBranchTypeModal" class="form-select @error('departmentBranchType') is-invalid @enderror">
              <option value="">{{ __('-- Pilih Jenis Cawangan --') }}</option>
              {{-- Get branch type options from Department model --}}
              @foreach(\App\Models\Department::getBranchTypeOptions() as $value => $label)
                <option value="{{ $value }}">{{ e($label) }}</option>
              @endforeach
            </select>
            @error('departmentBranchType') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Select2 dropdown for department head selection --}}
          <div wire:ignore class="col-md-6">
            <label for="select2DepartmentHeadIdModal" class="form-label w-100">{{ __('Ketua Jabatan (Pilihan)') }}</label>
            <select wire:model.defer='departmentHeadId' id="select2DepartmentHeadIdModal" class="select2-department-head form-select @error('departmentHeadId') is-invalid @enderror" data-placeholder="{{ __('Pilih Ketua Jabatan') }}">
              <option value="">{{ __('Pilih Ketua Jabatan (Pilihan)') }}</option>
              @foreach($usersForHodSelection ?? [] as $user)
                @if(is_object($user))
                    <option value="{{ $user->id }}">{{ e($user->name) }} ({{ e($user->email) }})</option>
                @endif
              @endforeach
            </select>
            @error('departmentHeadId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="departmentDescriptionModal" class="form-label w-100">{{ __('Keterangan (Pilihan)') }}</label>
            <textarea wire:model.defer='departmentDescription' id="departmentDescriptionModal" class="form-control @error('departmentDescription') is-invalid @enderror" rows="3" placeholder="{{__('Terangkan fungsi utama jabatan ini...')}}"></textarea>
            @error('departmentDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input wire:model.defer='departmentIsActive' class="form-check-input" type="checkbox" id="departmentIsActiveModal" />
              <label class="form-check-label" for="departmentIsActiveModal">
                {{ __('Jadikan Jabatan Ini Aktif') }}
              </label>
            </div>
             @error('departmentIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
        </button>
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center" form="departmentFormModal"
                wire:loading.attr="disabled" wire:target="submitDepartment">
            <span wire:loading wire:target="submitDepartment" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            <i class="bi {{ $isEditMode ? 'bi-save-fill' : 'bi-check-lg' }} me-1" wire:loading.remove wire:target="submitDepartment"></i>
            {{ $isEditMode ? __('Simpan Perubahan') : __('Tambah Jabatan') }}
        </button>
      </div>
    </div>
  </div>
</div>

@pushOnce('custom-scripts')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/i18n/ms.js')}}"></script>
<script>
  // Initialize Select2 for department head selection in the modal
  document.addEventListener('livewire:initialized', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const departmentModalElement = document.getElementById('departmentModal');
        const selectEl = $('#select2DepartmentHeadIdModal');

        // Function to initialize Select2 for department head dropdown
        const initHodSelect2InModal = () => {
            if (selectEl.length) {
                // Destroy existing Select2 instance if present
                if (selectEl.hasClass("select2-hidden-accessible")) {
                    selectEl.select2('destroy');
                }

                // Initialize new Select2 instance
                selectEl.select2({
                    placeholder: "{{ __('Pilih Ketua Jabatan (Pilihan)') }}",
                    dropdownParent: $(departmentModalElement),
                    allowClear: true,
                    language: "ms"
                });

                // Set current value from Livewire component
                let currentHodId = @this.get('departmentHeadId');
                selectEl.val(currentHodId).trigger('change.select2');
            }
        };

        // Initialize Select2 when modal is shown
        if (departmentModalElement && selectEl.length) {
            $(departmentModalElement).on('shown.bs.modal', function () {
                initHodSelect2InModal();
            });

            // Update Livewire property when Select2 value changes
            selectEl.on('change', function (e) {
                @this.set('departmentHeadId', $(this).val());
            });
        }

        // Listen for Livewire event to refresh Select2 values
        Livewire.on('departmentModalOpened', () => {
            if (selectEl.data('select2')) {
                let currentHodId = @this.get('departmentHeadId');
                selectEl.val(currentHodId).trigger('change.select2');
            }
        });
    } else {
        console.error("jQuery or Select2 is not loaded for department modal.");
    }
  });
</script>
@endPushOnce
