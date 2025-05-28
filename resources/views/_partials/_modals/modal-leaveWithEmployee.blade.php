{{-- resources/views/_partials/_modals/modal-leaveWithEmployee.blade.php --}}
{{-- Modal for adding/editing employee leave records, as per dashboard description. --}}
{{-- Design Language: Clean & Modern Official Aesthetic, Bootstrap 5 forms --}}
<div>
  @push('custom-css')
    {{-- Page-specific CSS for Flatpickr and Select2 --}}
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
  @endpush

  <div wire:ignore.self class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple modal-dialog-centered">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body p-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
          <div class="text-center mb-4">
            <h3 id="leaveModalLabel" class="mb-2">{{ $isEdit ? __('Kemaskini Rekod Cuti') : __('Rekod Cuti Baharu') }}</h3>
            <p class="text-muted">{{ __('Sila lengkapkan maklumat di bawah.') }}</p>
          </div>

          @if(isset($employeePhoto) && $employeePhoto) {{-- Only show if photo is relevant/available --}}
          <div class="d-flex justify-content-center mb-4">
            <div class="avatar avatar-xl"> {{-- Use Bootstrap avatar classes --}}
              <img src="{{ Storage::disk("public")->exists($employeePhoto) ? Storage::disk("public")->url($employeePhoto) : asset('assets/img/avatars/default-avatar.png') }}" alt="{{ __('Foto Profil') }}" class="rounded-circle">
            </div>
          </div>
          @endif

          <form wire:submit.prevent="submitLeave" class="row g-3">
            @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger">
                    <h6 class="alert-heading mb-1">{{ __('Ralat Pengesahan') }}</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div wire:ignore class="col-md-6">
                <label for="select2selectedEmployeeId" class="form-label">{{ __('Pekerja') }} <span class="text-danger">*</span></label>
                <select wire:model.defer='selectedEmployeeId' class="select2 form-select" id="select2selectedEmployeeId"> {{-- Use form-select --}}
                  <option value="">{{ __('Sila Pilih Pekerja') }}</option>
                  @forelse ($activeEmployees as $employeeEntry) {{-- Changed variable name for clarity --}}
                    <option value="{{ $employeeEntry->employee->id }}">{{ $employeeEntry->employee->id . ' - ' . $employeeEntry->employee->full_name }}</option>
                  @empty
                    <option value="" disabled>{{__('Tiada Pekerja Aktif Ditemui!') }}</option>
                  @endforelse
                </select>
                @error('selectedEmployeeId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div wire:ignore class="col-md-6">
                <label for="select2LeaveId" class="form-label w-100">{{ __('Jenis Cuti') }} <span class="text-danger">*</span></label>
                <select wire:model.defer='newLeaveInfo.LeaveId' class="select2 form-select" id="select2LeaveId"> {{-- Use form-select --}}
                  <option value="">{{ __('Sila Pilih Jenis Cuti') }}</option>
                  @forelse ($leaveTypes as $leaveType)
                    <option value="{{ $leaveType->id }}">{{ __($leaveType->name) }}</option> {{-- Assuming leave type name is translatable --}}
                  @empty
                    <option value="" disabled>{{ __('Tiada Jenis Cuti Ditemui!') }}</option>
                  @endforelse
                </select>
                @error('newLeaveInfo.LeaveId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label for="flatpickr-date-from" class="form-label">{{ __('Tarikh Mula') }} <span class="text-danger">*</span></label>
                <input wire:model.defer='newLeaveInfo.fromDate' type="text" class="form-control flatpickr-input @error('newLeaveInfo.fromDate') is-invalid @enderror" id="flatpickr-date-from" placeholder="YYYY-MM-DD" readonly="readonly">
                @error('newLeaveInfo.fromDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label for="flatpickr-date-to" class="form-label w-100">{{ __('Tarikh Akhir') }} <span class="text-danger">*</span></label>
                <input wire:model.defer='newLeaveInfo.toDate' class="form-control flatpickr-input @error('newLeaveInfo.toDate') is-invalid @enderror" type="text" id="flatpickr-date-to" placeholder="YYYY-MM-DD" readonly="readonly" />
                @error('newLeaveInfo.toDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label for="startAt" class="form-label w-100">{{ __('Masa Mula') }}</label>
                <input wire:model.defer='newLeaveInfo.startAt' class="form-control @error('newLeaveInfo.startAt') is-invalid @enderror" type="text" id="startAt" placeholder="HH:MM" autocomplete="off" />
                @error('newLeaveInfo.startAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label for="endAt" class="form-label w-100">{{ __('Masa Akhir') }}</label>
                <input wire:model.defer='newLeaveInfo.endAt' class="form-control @error('newLeaveInfo.endAt') is-invalid @enderror" type="text" id="endAt" placeholder="HH:MM" autocomplete="off" />
                @error('newLeaveInfo.endAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="leaveNote" class="form-label">{{ __('Catatan') }}</label>
                <textarea wire:model.defer='newLeaveInfo.note' class="form-control" id="leaveNote" rows="3" placeholder="{{ __('Sila masukkan catatan jika ada...') }}"></textarea>
                @error('newLeaveInfo.note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">
                <span wire:loading wire:target="submitLeave" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ __('Hantar') }}
              </button>
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">{{__('Batal') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('custom-scripts')
    {{-- Ensure vendor scripts are loaded, ideally via scripts.blade.php if used globally or pushed from page if specific --}}
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    {{-- If using Bahasa Melayu for Flatpickr, load the locale --}}
    {{-- <script src="https://npmcdn.com/flatpickr/dist/l10n/ms.js"></script> --}}


    <script>
      document.addEventListener('livewire:load', function () {
        const flatpickrDateFrom = document.querySelector('#flatpickr-date-from');
        if (flatpickrDateFrom) {
          flatpickrDateFrom.flatpickr({
            dateFormat: "Y-m-d",
            // locale: 'ms', // Uncomment if Malay locale for flatpickr is loaded
            allowInput: true, // Allows manual input, works with readonly if you prefer picker only
            disable: [
                // {
                //     from: "1970-01-01",
                //     to: "{{ $fromDateLimit ?? '1970-01-01' }}" // Ensure $fromDateLimit is passed or has a default
                // },
            ],
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('newLeaveInfo.fromDate', dateStr);
            }
          });
        }

        const flatpickrDateTo = document.querySelector('#flatpickr-date-to');
        if (flatpickrDateTo) {
          flatpickrDateTo.flatpickr({
            dateFormat: "Y-m-d",
            // locale: 'ms',
            allowInput: true,
            disable: [
                // {
                //     from: "1970-01-01",
                //     to: "{{ $fromDateLimit ?? '1970-01-01' }}"
                // },
            ],
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('newLeaveInfo.toDate', dateStr);
            }
          });
        }

        const timePickerConfig = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i", // 24hr format
            time_24hr: true,
            minuteIncrement: 15, // Or 1, 5, 10, 30 based on preference
            // locale: 'ms',
            allowInput: true
        };

        const startAtPicker = document.querySelector('#startAt');
        if (startAtPicker) {
          startAtPicker.flatpickr({
            ...timePickerConfig,
            defaultHour: 9,
            defaultMinute: 0,
            // minTime: "09:00", // Define business hours if needed
            // maxTime: "17:00",
             onChange: function(selectedDates, dateStr, instance) {
                @this.set('newLeaveInfo.startAt', dateStr);
            }
          });
        }

        const endAtPicker = document.querySelector('#endAt');
        if (endAtPicker) {
          endAtPicker.flatpickr({
            ...timePickerConfig,
            defaultHour: 17,
            defaultMinute: 0,
            // minTime: "09:00",
            // maxTime: "17:00",
             onChange: function(selectedDates, dateStr, instance) {
                @this.set('newLeaveInfo.endAt', dateStr);
            }
          });
        }

        // Initialize Select2
        // Ensure jQuery is loaded before this script runs for Select2
        if (typeof jQuery !== 'undefined') {
            const initSelect2 = (elementId, modelName, placeholderText) => {
                const selectEl = $(`#${elementId}`);
                if (selectEl.length) {
                    selectEl.wrap('<div class="position-relative"></div>').select2({
                        placeholder: placeholderText,
                        dropdownParent: selectEl.parent(),
                        allowClear: true
                    }).on('change', function (e) {
                        @this.set(modelName, $(this).val());
                    });
                }
            };

            initSelect2('select2selectedEmployeeId', 'selectedEmployeeId', "{{ __('Sila Pilih Pekerja (ID, Nama...)') }}");
            initSelect2('select2LeaveId', 'newLeaveInfo.LeaveId', "{{ __('Sila Pilih Jenis Cuti...') }}");

            // Livewire event listeners to re-initialize or update Select2 if needed
            Livewire.on('setSelect2Values', detail => {
                $('#select2selectedEmployeeId').val(detail.employeeId).trigger('change.select2');
                $('#select2LeaveId').val(detail.leaveId).trigger('change.select2');
            });

            Livewire.on('clearSelect2Values', () => {
                // $('#select2selectedEmployeeId').val(null).trigger('change.select2'); // Clear employee
                $('#select2LeaveId').val(null).trigger('change.select2'); // Clear leave type
            });
        } else {
            console.error("jQuery is not loaded. Select2 cannot be initialized.");
        }

      }); // End Livewire load event
    </script>
  @endpush
</div>
