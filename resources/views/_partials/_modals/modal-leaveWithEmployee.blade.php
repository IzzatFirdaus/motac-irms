{{-- resources/views/_partials/_modals/modal-leaveWithEmployee.blade.php --}}
{{-- Modal for adding/editing employee leave records, as per dashboard description. --}}
{{-- Design Language: Clean & Modern Official Aesthetic, Bootstrap 5 forms --}}
<div>
    @push('custom-css')
        {{-- Page-specific CSS for Flatpickr and Select2 --}}
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    @endpush

    <div wire:ignore.self class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel"
        aria-hidden="true">
        {{-- modal-xl for wider content, modal-dialog-centered for vertical centering --}}
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content p-3 p-md-4"> {{-- Adjusted padding to p-md-4 for consistency --}}
                <div class="modal-header"> {{-- Added modal-header for conventional structure --}}
                    <h5 class="modal-title d-flex align-items-center" id="leaveModalLabel">
                        <i class="bi bi-calendar-plus me-2 fs-5"></i> {{-- Bootstrap Icon for "Leave Record" --}}
                        {{ $isEdit ? __('Kemaskini Rekod Cuti') : __('Rekod Cuti Baharu') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body pt-4"> {{-- Added pt-4 since header now exists --}}
                    {{-- Removed redundant close button and centered title as they are now in modal-header --}}

                    @if (isset($employeePhoto) && $employeePhoto)
                        <div class="d-flex justify-content-center mb-4">
                            <div class="avatar avatar-xl">
                                <img src="{{ Storage::disk('public')->exists($employeePhoto) ? Storage::disk('public')->url($employeePhoto) : asset('assets/img/avatars/default-avatar.png') }}"
                                    alt="{{ __('Foto Profil') }}" class="rounded-circle img-fluid"
                                    style="width: 100px; height: 100px; object-fit: cover;"> {{-- Added img-fluid and basic styling --}}
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="submitLeave" class="row g-3">
                        @if ($errors->any())
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{-- Added dismissible and show for Bootstrap alert functionality --}}
                                    <h6 class="alert-heading mb-1 d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Ralat Pengesahan') }}
                                    </h6>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="{{ __('Tutup') }}"></button>
                                </div>
                            </div>
                        @endif

                        <div wire:ignore class="col-md-6">
                            <label for="select2selectedEmployeeId" class="form-label">{{ __('Pekerja') }} <span
                                    class="text-danger">*</span></label>
                            <select wire:model='selectedEmployeeId' class="select2 form-select"
                                id="select2selectedEmployeeId" data-placeholder="{{ __('Sila Pilih Pekerja') }}">
                                {{-- Moved placeholder to data-placeholder for Select2 --}}
                                <option value="">{{ __('Sila Pilih Pekerja') }}</option>
                                @forelse ($activeEmployees as $employeeEntry)
                                    <option value="{{ $employeeEntry->employee->id }}">
                                        {{ $employeeEntry->employee->id . ' - ' . $employeeEntry->employee->full_name }}
                                    </option>
                                @empty
                                    <option value="" disabled>{{ __('Tiada Pekerja Aktif Ditemui!') }}</option>
                                @endforelse
                            </select>
                            @error('selectedEmployeeId')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div wire:ignore class="col-md-6">
                            <label for="select2LeaveId" class="form-label w-100">{{ __('Jenis Cuti') }} <span
                                    class="text-danger">*</span></label>
                            <select wire:model='newLeaveInfo.LeaveId' class="select2 form-select" id="select2LeaveId"
                                data-placeholder="{{ __('Sila Pilih Jenis Cuti') }}">
                                <option value="">{{ __('Sila Pilih Jenis Cuti') }}</option>
                                @forelse ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ __($leaveType->name) }}</option>
                                @empty
                                    <option value="" disabled>{{ __('Tiada Jenis Cuti Ditemui!') }}</option>
                                @endforelse
                            </select>
                            @error('newLeaveInfo.LeaveId')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="flatpickr-date-from" class="form-label">{{ __('Tarikh Mula') }} <span
                                    class="text-danger">*</span></label>
                            {{-- Added wire:key for Livewire DOM diffing on complex inputs --}}
                            <input wire:key="date-from-{{ rand() }}" wire:model.defer='newLeaveInfo.fromDate'
                                type="text"
                                class="form-control flatpickr-input @error('newLeaveInfo.fromDate') is-invalid @enderror"
                                id="flatpickr-date-from" placeholder="YYYY-MM-DD">
                            @error('newLeaveInfo.fromDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="flatpickr-date-to" class="form-label w-100">{{ __('Tarikh Akhir') }} <span
                                    class="text-danger">*</span></label>
                            <input wire:key="date-to-{{ rand() }}" wire:model.defer='newLeaveInfo.toDate'
                                class="form-control flatpickr-input @error('newLeaveInfo.toDate') is-invalid @enderror"
                                type="text" id="flatpickr-date-to" placeholder="YYYY-MM-DD" />
                            @error('newLeaveInfo.toDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="startAt" class="form-label w-100">{{ __('Masa Mula') }}</label>
                            <input wire:key="time-start-{{ rand() }}" wire:model.defer='newLeaveInfo.startAt'
                                class="form-control @error('newLeaveInfo.startAt') is-invalid @enderror" type="text"
                                id="startAt" placeholder="HH:MM" autocomplete="off" />
                            @error('newLeaveInfo.startAt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="endAt" class="form-label w-100">{{ __('Masa Akhir') }}</label>
                            <input wire:key="time-end-{{ rand() }}" wire:model.defer='newLeaveInfo.endAt'
                                class="form-control @error('newLeaveInfo.endAt') is-invalid @enderror" type="text"
                                id="endAt" placeholder="HH:MM" autocomplete="off" />
                            @error('newLeaveInfo.endAt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="leaveNote" class="form-label">{{ __('Catatan') }}</label>
                            <textarea wire:model.defer='newLeaveInfo.note' class="form-control" id="leaveNote" rows="3"
                                placeholder="{{ __('Sila masukkan catatan jika ada...') }}"></textarea>
                            @error('newLeaveInfo.note')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Moved buttons to modal-footer for conventional Bootstrap modal layout --}}
                    </form>
                </div>
                <div class="modal-footer"> {{-- Added modal-footer --}}
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('Batal') }}</button> {{-- Consistent secondary button --}}
                    <button type="submit" form="leaveSubmitForm" class="btn btn-primary" wire:click="submitLeave"
                        wire:loading.attr="disabled" wire:target="submitLeave"> {{-- `form` attribute links to form id if needed, or use wire:click --}}
                        <span wire:loading wire:target="submitLeave" class="spinner-border spinner-border-sm me-1"
                            role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="submitLeave"><i class="bi bi-check-lg me-1"></i></span>
                        {{-- Bootstrap Icon --}}
                        {{ __('Hantar') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('custom-scripts')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        {{-- Load Malay locale for Flatpickr if available in your assets --}}
        {{-- <script src="{{asset('assets/vendor/libs/flatpickr/l10n/ms.js')}}"></script> --}}

        <script>
            document.addEventListener('livewire:init', () => { // Changed to livewire:init
                let flatpickrDateFromInstance, flatpickrDateToInstance, startAtPickerInstance, endAtPickerInstance;

                const initFlatpickr = () => {
                    const flatpickrCommonConfig = {
                        dateFormat: "Y-m-d",
                        // locale: 'ms', // Uncomment if Malay locale for flatpickr is available
                        allowInput: false, // Recommended to set to false if using readonly, to force picker usage
                    };

                    if (document.querySelector('#flatpickr-date-from')) {
                        flatpickrDateFromInstance = flatpickr('#flatpickr-date-from', {
                            ...flatpickrCommonConfig,
                            // disable: [{ from: "1970-01-01", to: "{{ $fromDateLimit ?? '1970-01-01' }}" }], // Ensure dynamic values are safe
                            onChange: function(selectedDates, dateStr, instance) {
                                @this.set('newLeaveInfo.fromDate', dateStr);
                            }
                        });
                    }

                    if (document.querySelector('#flatpickr-date-to')) {
                        flatpickrDateToInstance = flatpickr('#flatpickr-date-to', {
                            ...flatpickrCommonConfig,
                            // disable: [{ from: "1970-01-01", to: "{{ $fromDateLimit ?? '1970-01-01' }}" }],
                            onChange: function(selectedDates, dateStr, instance) {
                                @this.set('newLeaveInfo.toDate', dateStr);
                            }
                        });
                    }

                    const timePickerConfig = {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        time_24hr: true,
                        minuteIncrement: 15,
                        // locale: 'ms',
                        allowInput: false
                    };

                    if (document.querySelector('#startAt')) {
                        startAtPickerInstance = flatpickr('#startAt', {
                            ...timePickerConfig,
                            defaultHour: 9,
                            defaultMinute: 0,
                            onChange: function(selectedDates, dateStr, instance) {
                                @this.set('newLeaveInfo.startAt', dateStr);
                            }
                        });
                    }

                    if (document.querySelector('#endAt')) {
                        endAtPickerInstance = flatpickr('#endAt', {
                            ...timePickerConfig,
                            defaultHour: 17,
                            defaultMinute: 0,
                            onChange: function(selectedDates, dateStr, instance) {
                                @this.set('newLeaveInfo.endAt', dateStr);
                            }
                        });
                    }
                };

                const initSelect2 = () => {
                    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 === 'function') {
                        $('#select2selectedEmployeeId').select2({
                            placeholder: "{{ __('Sila Pilih Pekerja') }}",
                            dropdownParent: $('#leaveModal'),
                            allowClear: true
                        }).on('change', function(e) {
                            @this.set('selectedEmployeeId', $(this).val());
                        });

                        $('#select2LeaveId').select2({
                            placeholder: "{{ __('Sila Pilih Jenis Cuti') }}",
                            dropdownParent: $('#leaveModal'),
                            allowClear: true
                        }).on('change', function(e) {
                            // Corrected model name to match Blade
                            @this.set('newLeaveInfo.LeaveId', $(this).val());
                        });
                    } else {
                        console.error("jQuery or Select2 is not loaded for leave modal.");
                    }
                };

                initFlatpickr();
                initSelect2();

                // Handling Livewire events to re-initialize or update
                Livewire.on('setSelect2Values', detail => {
                    if (typeof jQuery !== 'undefined') {
                        $('#select2selectedEmployeeId').val(detail.employeeId).trigger('change.select2');
                        $('#select2LeaveId').val(detail.leaveId).trigger('change.select2');
                    }
                });

                Livewire.on('clearSelect2Values', () => {
                    if (typeof jQuery !== 'undefined') {
                        // $('#select2selectedEmployeeId').val(null).trigger('change.select2'); // Keep employee if intended
                        $('#select2LeaveId').val(null).trigger('change.select2');
                    }
                });

                // Optional: Re-initialize on modal show if content is dynamic or state is lost
                $('#leaveModal').on('shown.bs.modal', function() {
                    // initFlatpickr(); // May not be needed if instances are preserved
                    // initSelect2();   // May not be needed if instances are preserved

                    // If you need to specifically reset values or re-apply them from Livewire state:
                    if (@this.get('selectedEmployeeId')) {
                        $('#select2selectedEmployeeId').val(@this.get('selectedEmployeeId')).trigger(
                            'change.select2');
                    }
                    if (@this.get('newLeaveInfo.LeaveId')) {
                        $('#select2LeaveId').val(@this.get('newLeaveInfo.LeaveId')).trigger('change.select2');
                    }
                });

            });
        </script>
    @endpush
</div>
