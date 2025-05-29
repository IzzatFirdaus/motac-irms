@push('custom-css')
{{-- If using Select2 for Head of Department --}}
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endpush

<div wire:ignore.self class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple"> {{-- Increased size for more fields --}}
    <div class="modal-content p-0 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{ $isEdit ? __('Update Department') : __('New Department') }}</h3>
          <p class="text-muted">{{ __('Please fill out the following information') }}</p>
        </div>
        <form wire:submit.prevent="submitDepartment" class="row g-3">
          <div class="col-md-6 mb-3">
            <label for="departmentName" class="form-label w-100">{{ __('Name') }} <span class="text-danger">*</span></label>
            <input wire:model.defer='departmentName' id="departmentName" class="form-control @error('departmentName') is-invalid @enderror" type="text" />
            @error('departmentName') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="departmentCode" class="form-label w-100">{{ __('Code') }}</label>
            <input wire:model.defer='departmentCode' id="departmentCode" class="form-control @error('departmentCode') is-invalid @enderror" type="text" />
            @error('departmentCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="departmentBranchType" class="form-label w-100">{{ __('Branch Type') }} <span class="text-danger">*</span></label>
            <select wire:model.defer='departmentBranchType' id="departmentBranchType" class="form-select @error('departmentBranchType') is-invalid @enderror">
              <option value="">{{ __('Select Branch Type') }}</option>
              <option value="{{ \App\Models\Department::BRANCH_TYPE_HQ }}">{{ __('Headquarters') }}</option>
              <option value="{{ \App\Models\Department::BRANCH_TYPE_STATE }}">{{ __('State') }}</option>
            </select>
            @error('departmentBranchType') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Assuming $users is passed from Livewire component for HOD selection --}}
          {{-- If not using Select2, a simple select can be used. --}}
          <div wire:ignore class="col-md-6 mb-3">
            <label for="select2DepartmentHeadId" class="form-label w-100">{{ __('Head of Department') }}</label>
            <select wire:model.defer='departmentHeadId' id="select2DepartmentHeadId" class="select2 form-select @error('departmentHeadId') is-invalid @enderror">
              <option value="">{{ __('Select Head of Department (Optional)') }}</option>
              @foreach($usersForHodSelection ?? [] as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
              @endforeach
            </select>
            @error('departmentHeadId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 mb-3">
            <label for="departmentDescription" class="form-label w-100">{{ __('Description') }}</label>
            <textarea wire:model.defer='departmentDescription' id="departmentDescription" class="form-control @error('departmentDescription') is-invalid @enderror" rows="3"></textarea>
            @error('departmentDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 mb-4">
            <div class="form-check">
              <input wire:model.defer='departmentIsActive' class="form-check-input" type="checkbox" id="departmentIsActive" />
              <label class="form-check-label" for="departmentIsActive">
                {{ __('Is Active') }}
              </label>
            </div>
             @error('departmentIsActive') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">
              <span wire:loading wire:target="submitDepartment" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              {{ __('Submit') }}
            </button>
            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">{{ __('Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
{{-- If using Select2 for Head of Department --}}
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
  document.addEventListener('livewire:load', function () {
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
        const initHodSelect2 = () => {
            const selectEl = $('#select2DepartmentHeadId');
            if (selectEl.length) {
                if (selectEl.data('select2')) { // Destroy if already initialized
                    selectEl.select2('destroy');
                }
                selectEl.wrap('<div class="position-relative"></div>').select2({
                    placeholder: "{{ __('Select Head of Department (Optional)') }}",
                    dropdownParent: $('#departmentModal'), // Attach to modal
                    allowClear: true
                }).on('change', function (e) {
                    @this.set('departmentHeadId', $(this).val());
                });
            }
        };

        initHodSelect2(); // Initial call

        Livewire.on('departmentModalOpened', () => { // Or a more specific event after data is loaded
            initHodSelect2();
            // If departmentHeadId is set in Livewire, trigger change for Select2
            let currentHodId = @this.get('departmentHeadId');
            if(currentHodId) {
                 $('#select2DepartmentHeadId').val(currentHodId).trigger('change.select2');
            } else {
                 $('#select2DepartmentHeadId').val(null).trigger('change.select2');
            }
        });
         // Re-initialize Select2 when the modal is shown
        $('#departmentModal').on('shown.bs.modal', function () {
            initHodSelect2();
            let currentHodId = @this.get('departmentHeadId'); // Ensure this reflects the current state
            if(currentHodId) {
                 $('#select2DepartmentHeadId').val(currentHodId).trigger('change.select2');
            } else {
                 $('#select2DepartmentHeadId').val(null).trigger('change.select2');
            }
        });


    } else {
        console.error("jQuery or Select2 is not loaded for department modal.");
    }
  });
</script>
@endpush
