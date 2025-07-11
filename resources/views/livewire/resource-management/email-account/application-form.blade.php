{{-- Email/User ID Application Form: resources/views/livewire/resource-management/email-account/application-form.blade.php --}}
<div>
    @section('title', $this->applicationToEdit ? __('forms.email_app_edit_title') : __('forms.email_app_create_title'))

    <div class="container-fluid py-4">
        <div class="row g-4">

            {{-- ================================================================= --}}
            {{--                        MAIN FORM CONTENT (LEFT)                     --}}
            {{-- ================================================================= --}}
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                    <h2 class="h4 fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi {{ $this->applicationToEdit ? 'bi-pencil-square' : 'bi-file-earmark-text-fill' }} me-2"></i>
                        {{ $this->applicationToEdit ? __('forms.email_app_edit_title') : __('forms.email_app_create_title') }}
                    </h2>
                    <span class="text-xs text-danger">{{ __('forms.label_required_field') }}</span>
                </div>

                @include('_partials._alerts.alert-general')

                <form wire:submit.prevent="saveApplication">
                    {{-- Card for Application Details --}}
                    <div class="card mb-4 motac-card">
                        <div class="card-header motac-card-header d-flex align-items-center">
                            <i class="bi bi-card-list me-2 fs-5"></i>
                            <h5 class="mb-0">{{ __('forms.section_application_details') }}</h5>
                        </div>
                        <div class="card-body motac-card-body">
                            {{-- Taraf Perkhidmatan --}}
                            <div class="mb-3">
                                <label for="service_status_selection" class="form-label fw-medium">{{ __('forms.label_service_status') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="service_status_selection" id="service_status_selection" class="form-select @error('service_status_selection') is-invalid @enderror">
                                    @foreach ($this->serviceStatusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('service_status_selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Pelantikan (Appointment Type) --}}
                            <div class="mb-3">
                                <label for="appointment_type_selection" class="form-label fw-medium">{{ __('forms.label_appointment_type') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="appointment_type_selection" id="appointment_type_selection" class="form-select @error('appointment_type_selection') is-invalid @enderror">
                                     @foreach ($this->appointmentTypeOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('appointment_type_selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Conditional Previous Department Fields --}}
                            @if ($this->shouldShowPreviousDepartmentFields())
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_name" class="form-label fw-medium">{{__('forms.label_previous_department')}} <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="previous_department_name" id="previous_department_name" class="form-control @error('previous_department_name') is-invalid @enderror">
                                        @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_department_email" class="form-label fw-medium">{{__('forms.label_previous_email')}} <span class="text-danger">*</span></label>
                                        <input type="email" wire:model="previous_department_email" id="previous_department_email" class="form-control @error('previous_department_email') is-invalid @enderror">
                                        @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="application_reason_notes" class="form-label fw-medium">{{ __('forms.label_application_purpose_notes') }} <span class="text-danger">*</span></label>
                                <textarea wire:model="application_reason_notes" id="application_reason_notes" rows="3"
                                    class="form-control @error('application_reason_notes') is-invalid @enderror"
                                    placeholder="{{ __('forms.placeholder_application_purpose') }}"></textarea>
                                @error('application_reason_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="proposed_email" class="form-label fw-medium">{{ __('forms.label_proposed_email') }}</label>
                                <input type="email" wire:model="proposed_email" id="proposed_email"
                                    class="form-control @error('proposed_email') is-invalid @enderror"
                                    placeholder="{{ __('forms.placeholder_proposed_email') }}">
                                <div class="form-text">{{ __('forms.text_proposed_email_help') }}</div>
                                @error('proposed_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Conditional Group Email Fields --}}
                            @if ($this->showGroupEmailFields())
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="mb-3 fw-semibold">{{ __('forms.section_group_email_info') }}</h6>
                                    <div class="mb-3">
                                        <label for="group_email_request_name" class="form-label fw-medium">{{ __('forms.label_group_email_name') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="text" wire:model="group_email_request_name" id="group_email_request_name" class="form-control @error('group_email_request_name') is-invalid @enderror">
                                        @error('group_email_request_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_name" class="form-label fw-medium">{{ __('forms.label_contact_person_name') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="text" wire:model="contact_person_name" id="contact_person_name" class="form-control @error('contact_person_name') is-invalid @enderror">
                                        @error('contact_person_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person_email" class="form-label fw-medium">{{ __('forms.label_contact_person_email') }} <span class="text-danger @if(empty($this->group_email_request_name)) d-none @endif">*</span></label>
                                        <input type="email" wire:model="contact_person_email" id="contact_person_email" class="form-control @error('contact_person_email') is-invalid @enderror">
                                        <div class="form-text">{{ __('forms.text_contact_person_email_help') }}</div>
                                        @error('contact_person_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Conditional Service Dates --}}
                            @if ($this->shouldShowServiceDates())
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_start_date" class="form-label fw-medium">{{ __('forms.label_service_start_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="service_start_date" id="service_start_date" class="form-control @error('service_start_date') is-invalid @enderror" max="{{ date('Y-m-d') }}">
                                        @error('service_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="service_end_date" class="form-label fw-medium">{{ __('forms.label_service_end_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="service_end_date" id="service_end_date" class="form-control @error('service_end_date') is-invalid @enderror">
                                        @error('service_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card for Certification --}}
                    <div class="card mb-4 motac-card">
                        <div class="card-header motac-card-header d-flex align-items-center">
                            <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                            <h5 class="mb-0">{{ __('forms.section_applicant_declaration_email') }}</h5>
                        </div>
                        <div class="card-body motac-card-body">
                            <div class="certification-block bg-light-subtle p-3 rounded border">
                                <p class="mb-2 small text-muted">{{ __('forms.text_applicant_declaration_lead_in_email') }}</p>
                                <div class="form-check mb-2">
                                    <input type="checkbox" wire:model="cert_info_is_true" id="cert_info_is_true" value="1" class="form-check-input @error('cert_info_is_true') is-invalid @enderror">
                                    <label for="cert_info_is_true" class="form-check-label">{{ __('forms.checkbox_info_is_true') }} <span class="text-danger">*</span></label>
                                    @error('cert_info_is_true') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" wire:model="cert_data_usage_agreed" id="cert_data_usage_agreed" value="1" class="form-check-input @error('cert_data_usage_agreed') is-invalid @enderror">
                                    <label for="cert_data_usage_agreed" class="form-check-label">{{ __('forms.checkbox_data_usage_agreed') }} <span class="text-danger">*</span></label>
                                    @error('cert_data_usage_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check mb-0">
                                    <input type="checkbox" wire:model="cert_email_responsibility_agreed" id="cert_email_responsibility_agreed" value="1" class="form-check-input @error('cert_email_responsibility_agreed') is-invalid @enderror">
                                    <label for="cert_email_responsibility_agreed" class="form-check-label">{{ __('forms.checkbox_email_responsibility_agreed') }} <span class="text-danger">*</span></label>
                                    @error('cert_email_responsibility_agreed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-danger fst-italic">{{ __('forms.text_all_certifications_required') }}</p>
                        </div>
                    </div>

                    {{-- ================================================================= --}}
                    {{--               ACTION BUTTONS (UPDATED BLOCK)                    --}}
                    {{-- ================================================================= --}}
                    <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top">
                        <div class="d-inline-flex align-items-center me-3" wire:loading.delay wire:target="saveApplication">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="ms-2">{{ __('forms.text_processing') }}</span>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="saveApplication" class="btn btn-success px-4 py-2">
                            <i class="bi bi-save-fill me-1"></i> {{ $this->applicationToEdit ? __('forms.button_update_draft') : __('forms.button_save_draft') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- ================================================================= --}}
            {{--                        SIDEBAR (RIGHT)                              --}}
            {{-- ================================================================= --}}
            <div class="col-lg-4">
                <div class="position-sticky top-0">
                    {{-- Applicant Details Card --}}
                    <div class="card mb-4 motac-card">
                        <div class="card-header motac-card-header d-flex align-items-center">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <h5 class="mb-0">{{ __('forms.section_applicant_info_email') }}
                                <small class="d-block text-muted fw-normal">{{__('forms.text_profile_info_note')}}</small>
                            </h5>
                        </div>
                        <div class="card-body motac-card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_full_name')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantName }}" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_nric_no')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->user->identification_number ?? '' }}" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_personal_email_login')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantEmail }}" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_position_grade')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantPositionAndGrade }}" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_department_unit')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantDepartment }}" readonly>
                                </div>
                                 <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">{{__('forms.label_phone_number')}}</label>
                                    <input type="text" class="form-control" value="{{ $this->applicantPhone }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Supporting Officer Card --}}
                    <div class="card mb-4 motac-card">
                        <div class="card-header motac-card-header d-flex align-items-center">
                            <i class="bi bi-person-check-fill me-2 fs-5"></i>
                            <h5 class="mb-0">{{ __('forms.section_supporting_officer_info') }}</h5>
                        </div>
                        <div class="card-body motac-card-body">
                            <p class="form-text">{{ __('forms.text_supporter_instruction')}}</p>
                            <div class="mb-3">
                                <label for="supporting_officer_name" class="form-label fw-medium">{{__('forms.label_supporter_full_name')}} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="supporting_officer_name" id="supporting_officer_name" class="form-control @error('supporting_officer_name') is-invalid @enderror" placeholder="{{ __('forms.placeholder_supporter_name') }}">
                                @error('supporting_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="supporting_officer_grade" class="form-label fw-medium">{{__('forms.label_supporter_grade')}} <span class="text-danger">*</span></label>
                                <select wire:model="supporting_officer_grade" id="supporting_officer_grade" class="form-select @error('supporting_officer_grade') is-invalid @enderror">
                                    @foreach($this->supportingOfficerGradeOptions as $gradeKey => $gradeName)
                                        <option value="{{ $gradeKey }}">{{ $gradeName }}</option>
                                    @endforeach
                                </select>
                                @error('supporting_officer_grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
                                <label for="supporting_officer_email" class="form-label fw-medium">{{__('forms.label_supporter_email')}} <span class="text-danger">*</span></label>
                                <input type="email" wire:model="supporting_officer_email" id="supporting_officer_email" class="form-control @error('supporting_officer_email') is-invalid @enderror" placeholder="{{ __('forms.placeholder_supporter_email') }}">
                                 @error('supporting_officer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
