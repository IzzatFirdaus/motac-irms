{{-- MOTAC ICT Loan Application Form (multilingual, suffixed language keys) --}}
<div>
    @php
        $pageTitle = $this->generatePageTitle();
        if ($isEditMode && $loanApplicationInstance && $loanApplicationInstance->reference_number) {
            $pageTitle .= ' (' . $loanApplicationInstance->reference_number . ')';
        }
    @endphp
    @section('title', $pageTitle)

    @push('page-style')
        <!-- MOTAC branding and focus styles -->
        <style>
            .motac-card-header { background-color: var(--bs-tertiary-bg); border-bottom: 1px solid var(--bs-border-color);}
            .motac-primary { color: #0047AB;}
            .motac-bg-primary { background-color: #0047AB;}
            .form-control:focus { border-color: #0047AB; box-shadow: 0 0 0 0.2rem rgba(0, 71, 171, 0.25);}
            .btn-primary { background-color: #0047AB; border-color: #0047AB;}
            .btn-primary:hover { background-color: #003a8c; border-color: #003a8c;}
        </style>
    @endpush

    {{-- Form Header with BPM Logo and Title (uses language keys) --}}
    <div class="d-flex justify-content-between align-items-center mb-24 mt-16">
        <div class="d-flex align-items-center">
            <div class="d-flex flex-column align-items-center me-3">
                <img src="{{ asset('assets/img/logo/logo_bpm.png') }}"
                     alt="@lang('app.bpm_short_name')"
                     style="height: 60px; width: 60px; object-fit: contain;">
                <small class="heading-xsmall text-muted text-center mt-1">@lang('app.bpm_short_name')</small>
            </div>
            <h1 class="heading-medium fw-semibold text-black-900 mb-0">@lang('forms.ict_loan_form_title')</h1>
        </div>
        <small class="heading-xsmall text-muted">@lang('forms.text_form_ref_no')</small>
    </div>

    {{-- Submission date info (edit mode) --}}
    @if ($isEditMode && $loanApplicationInstance && $loanApplicationInstance->submitted_at)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 border rounded bg-light-subtle">
                    <span class="fw-medium">@lang('forms.date_application_received_label'):</span>
                    <span>{{ Carbon\Carbon::parse($loanApplicationInstance->submitted_at)->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- General alert messages --}}
    @include('_partials._alerts.alert-general')

    {{-- Main form, autosave and multilingual labels --}}
    <form wire:submit.prevent="submitLoanApplication"
          x-data="{
              autosaveKey: 'loan_application_form_autosave_{{ $editing_application_id ?? 'new' }}',
              termsScrolled: @entangle('termsScrolled').live,
              applicantConfirmation: @entangle('applicant_confirmation').live,
              getDataForCache() {
                  return {
                      purpose: this.$wire.purpose,
                      location: this.$wire.location,
                      return_location: this.$wire.return_location,
                      loan_start_date: this.$wire.loan_start_date,
                      loan_end_date: this.$wire.loan_end_date,
                      applicant_phone: this.$wire.applicant_phone,
                      applicant_is_responsible_officer: this.$wire.applicant_is_responsible_officer,
                      responsible_officer_id: this.$wire.responsible_officer_id,
                      supporting_officer_id: this.$wire.supporting_officer_id,
                      loan_application_items: this.$wire.loan_application_items,
                  };
              },
              clearCache() { localStorage.removeItem(this.autosaveKey); }
          }"
          @init="if ({{ !$isEditMode ? 'true' : 'false' }}) { const cachedData = localStorage.getItem(autosaveKey); if (cachedData) { $wire.loadStateFromCache(JSON.parse(cachedData)); } }"
          @input.debounce.750ms="if ({{ !$isEditMode ? 'true' : 'false' }}) { localStorage.setItem(autosaveKey, JSON.stringify(getDataForCache())) }"
    >

        <div class="row g-4">
            {{-- Left Column: Applicant and Equipment Details --}}
            <div class="col-lg-7">
                {{-- Applicant Information Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                                <i class="bi bi-person-lines-fill me-2 fs-5 text-primary-500"></i>
                                @lang('forms.section_applicant_info_ict')
                            </h2>
                            <small class="heading-xsmall text-muted">@lang('messages.instruction_mandatory_fields')</small>
                        </div>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        <div class="row g-3">
                            {{-- Full Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium">@lang('forms.label_full_name')</label>
                                <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                    {{ $applicantName }}
                                </p>
                            </div>
                            {{-- Phone Number --}}
                            <div class="col-md-6">
                                <label for="applicant_phone" class="form-label fw-medium">
                                    @lang('forms.label_phone_number')
                                </label>
                                <input type="text"
                                       id="applicant_phone"
                                       wire:model.blur="applicant_phone"
                                       class="form-control @error('applicant_phone') is-invalid @enderror"
                                       placeholder="@lang('forms.placeholder_phone_number')">
                                @error('applicant_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Position & Grade --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium">@lang('forms.label_position_grade')</label>
                                <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                    {{ $applicantPositionAndGrade }}
                                </p>
                            </div>
                            {{-- Department/Unit --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium">@lang('forms.label_department_unit')</label>
                                <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                    {{ $applicantDepartment }}
                                </p>
                            </div>
                            {{-- Purpose --}}
                            <div class="col-md-12">
                                <label for="purpose" class="form-label fw-medium">
                                    @lang('forms.label_application_purpose')
                                </label>
                                <textarea id="purpose"
                                          wire:model.blur="purpose"
                                          rows="3"
                                          class="form-control @error('purpose') is-invalid @enderror"
                                          placeholder="@lang('forms.placeholder_purpose')"></textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Usage Location --}}
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-medium">
                                    @lang('forms.label_location_ict')
                                </label>
                                <input type="text"
                                       id="location"
                                       wire:model.blur="location"
                                       class="form-control @error('location') is-invalid @enderror"
                                       placeholder="@lang('forms.placeholder_usage_location')">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Return Location --}}
                            <div class="col-md-6">
                                <label for="return_location" class="form-label fw-medium">
                                    @lang('forms.label_expected_return_location')
                                </label>
                                <input type="text"
                                       id="return_location"
                                       wire:model.blur="return_location"
                                       class="form-control @error('return_location') is-invalid @enderror"
                                       placeholder="@lang('forms.placeholder_return_location')">
                                @error('return_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Loan Start Date --}}
                            <div class="col-md-6">
                                <label for="loan_start_date" class="form-label fw-medium">
                                    @lang('forms.label_loan_date')
                                </label>
                                <input type="datetime-local"
                                       id="loan_start_date"
                                       wire:model.blur="loan_start_date"
                                       class="form-control @error('loan_start_date') is-invalid @enderror"
                                       min="{{ now()->toDateTimeLocalString('minute') }}">
                                @error('loan_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Loan End Date --}}
                            <div class="col-md-6">
                                <label for="loan_end_date" class="form-label fw-medium">
                                    @lang('forms.label_expected_return_date')
                                </label>
                                <input type="datetime-local"
                                       id="loan_end_date"
                                       wire:model.blur="loan_end_date"
                                       class="form-control @error('loan_end_date') is-invalid @enderror">
                                @error('loan_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Equipment Details Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                                <i class="bi bi-tools me-2 fs-5 text-primary-500"></i>
                                @lang('forms.section_equipment_details_ict')
                            </h2>
                            <button type="button"
                                    wire:click="addLoanItem"
                                    class="button variant-primary size-small">
                                <i class="bi bi-plus-lg me-1"></i> @lang('app.button_add_equipment')
                            </button>
                        </div>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        @forelse ($loan_application_items as $index => $item)
                            <div wire:key="loan_item_{{ $index }}" class="list-group-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="heading-xsmall mb-0 fw-semibold text-primary-500">
                                        @lang('forms.title_equipment_item', ['index' => $index + 1])
                                    </h3>
                                    @if (count($loan_application_items) > 1)
                                        <button type="button"
                                                wire:click="removeLoanItem({{ $index }})"
                                                title="@lang('app.button_remove_equipment')"
                                                class="button variant-danger size-small p-0">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="row g-3">
                                    {{-- Equipment Type --}}
                                    <div class="col-md-6">
                                        <label for="item_{{ $index }}_equipment_type" class="form-label">
                                            @lang('forms.label_equipment_type')
                                        </label>
                                        <select id="item_{{ $index }}_equipment_type"
                                                wire:model="loan_application_items.{{ $index }}.equipment_type"
                                                class="form-select @error('loan_application_items.' . $index . '.equipment_type') is-invalid @enderror">
                                            <option value="">-- @lang('forms.placeholder_select_type') --</option>
                                            @foreach ($equipmentTypeOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('loan_application_items.' . $index . '.equipment_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- Quantity --}}
                                    <div class="col-md-6">
                                        <label for="item_{{ $index }}_quantity_requested" class="form-label">
                                            @lang('forms.label_quantity')
                                        </label>
                                        <input type="number"
                                               id="item_{{ $index }}_quantity_requested"
                                               wire:model="loan_application_items.{{ $index }}.quantity_requested"
                                               min="1"
                                               class="form-control @error('loan_application_items.' . $index . '.quantity_requested') is-invalid @enderror">
                                        @error('loan_application_items.' . $index . '.quantity_requested')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- Notes/Remarks --}}
                                    <div class="col-md-12">
                                        <label for="item_{{ $index }}_notes" class="form-label">
                                            @lang('forms.label_remarks')
                                        </label>
                                        <input type="text"
                                               id="item_{{ $index }}_notes"
                                               wire:model="loan_application_items.{{ $index }}.notes"
                                               class="form-control @error('loan_application_items.' . $index . '.notes') is-invalid @enderror"
                                               placeholder="@lang('forms.placeholder_equipment_remarks')">
                                        @error('loan_application_items.' . $index . '.notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16 border rounded bg-light-100">
                                <p class="heading-xsmall text-muted mb-0">@lang('forms.text_no_equipment_added')</p>
                            </div>
                        @endforelse

                        @error('loan_application_items')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Right Column: Officer Info, Terms, Confirmation --}}
            <div class="col-lg-5">
                {{-- Responsible Officer Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-person-check-fill me-2 fs-5 text-primary-500"></i>
                            @lang('forms.section_responsible_officer_info')
                        </h2>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        <div class="form-check mb-3">
                            <input id="applicant_is_responsible_officer"
                                   wire:model.live="applicant_is_responsible_officer"
                                   type="checkbox"
                                   class="form-check-input">
                            <label for="applicant_is_responsible_officer" class="form-check-label fw-medium">
                                @lang('forms.instruction_responsible_officer_is_applicant')
                            </label>
                        </div>
                        @if (!$applicant_is_responsible_officer)
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="responsible_officer_id" class="form-label fw-medium">
                                        @lang('forms.label_responsible_officer_name')<span class="text-danger">*</span>
                                    </label>
                                    <div wire:ignore>
                                        <select id="responsible_officer_id"
                                                wire:model="responsible_officer_id"
                                                class="form-select @error('responsible_officer_id') is-invalid @enderror">
                                            <option value="">-- @lang('forms.placeholder_select_responsible_officer') --</option>
                                            @foreach ($responsibleOfficerOptions as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('responsible_officer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Supporting Officer Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-person-badge-fill me-2 fs-5 text-primary-500"></i>
                            @lang('forms.section_supporting_officer_info')
                        </h2>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        <div class="mb-3">
                            <label for="supporting_officer_id" class="form-label fw-medium">
                                @lang('forms.label_supporting_officer_name')<span class="text-danger">*</span>
                            </label>
                            <div wire:ignore>
                                <select id="supporting_officer_id"
                                        wire:model="supporting_officer_id"
                                        class="form-select @error('supporting_officer_id') is-invalid @enderror">
                                    <option value="">-- @lang('forms.placeholder_select_supporting_officer') --</option>
                                    @foreach ($supportingOfficerOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-text text-muted">
                                @lang('forms.text_supporter_grade_requirement', ['grade' => config('motac.approval.min_loan_support_grade_level', 41)])
                            </small>
                            @error('supporting_officer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Terms and Conditions Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-card-checklist me-2 fs-5 text-primary-500"></i>
                            @lang('messages.terms_title')
                        </h2>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        <div id="termsBox"
                             style="height: 250px; overflow-y: scroll; border: 1px solid #dee2e6; padding: 15px; background-color: #f8f9fa; border-radius: 0.375rem;"
                             class="small text-muted motac-terms-box"
                             x-ref="termsBox"
                             @scroll.debounce.150ms="
                                 if ($refs.termsBox.scrollTop + $refs.termsBox.clientHeight >= $refs.termsBox.scrollHeight - 20) {
                                     termsScrolled = true;
                                 }
                             ">
                            <p class="fw-bold mb-2">@lang('messages.instruction_mandatory_fields')</p>
                            <ol class="ps-3 mb-0">
                                <li class="mb-2">@lang('messages.terms_item1')</li>
                                <li class="mb-2">@lang('messages.terms_item2')</li>
                                <li class="mb-2">@lang('messages.terms_item3')</li>
                                <li class="mb-2">@lang('messages.terms_item4')</li>
                                <li class="mb-2">@lang('messages.terms_item5')</li>
                                <li class="mb-2">@lang('messages.terms_item6')</li>
                                <li class="mb-2">@lang('messages.terms_item7')</li>
                                <li class="mb-2">@lang('messages.terms_item8')</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Applicant Confirmation Card --}}
                <div class="card motac-card mb-24">
                    <div class="card-header motac-card-header py-16 px-24">
                        <h2 class="heading-small mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-patch-check-fill me-2 fs-5 text-primary-500"></i>
                            @lang('forms.section_applicant_confirmation_ict')
                        </h2>
                    </div>
                    <div class="card-body py-24 px-24 motac-card-body">
                        <div class="form-check">
                            <input id="applicant_confirmation"
                                   wire:model="applicant_confirmation"
                                   type="checkbox"
                                   value="1"
                                   class="form-check-input @error('applicant_confirmation') is-invalid @enderror"
                                   x-model="applicantConfirmation">
                            <label for="applicant_confirmation" class="form-check-label fw-medium">
                                @lang('forms.text_applicant_declaration_ict')<span class="text-danger">*</span>
                            </label>
                            @error('applicant_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer: document reference and effective date (language keys where possible) --}}
        <div class="d-flex justify-content-between pt-16 mt-24 border-top">
            <small class="heading-xsmall text-muted">
                <strong>@lang('forms.text_document_no')</strong> PK.(S).KPK.08.(L3) Pin.1
            </small>
            <small class="heading-xsmall text-muted">
                <strong>@lang('forms.text_effective_date')</strong> 1/1/2024
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 pt-16">
            @if ($isEditMode && $loanApplicationInstance)
                <a href="{{ route('loan-applications.print', ['loan_application' => $loanApplicationInstance->id]) }}"
                   target="_blank"
                   class="button variant-secondary size-medium d-inline-flex align-items-center">
                    <i class="bi bi-printer-fill me-1"></i>@lang('common.print')
                </a>
            @endif

            <div class="d-inline-flex align-items-center me-2"
                 wire:loading.delay
                 wire:target="saveAsDraft, submitLoanApplication">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span wire:loading.remove wire:target="submitLoanApplication">@lang('app.text_processing')</span>
                <span wire:loading.remove wire:target="saveAsDraft">@lang('app.text_saving')</span>
            </div>

            <button type="button"
                    wire:click="saveAsDraft"
                    wire:loading.attr="disabled"
                    @click="clearCache()"
                    class="button variant-secondary size-medium">
                <i class="bi bi-save me-1"></i>@lang('app.button_save_draft')
            </button>

            <button type="submit"
                    wire:loading.attr="disabled"
                    @click="clearCache()"
                    class="button variant-primary size-medium"
                    x-bind:disabled="!applicantConfirmation || !termsScrolled">
                <i class="bi bi-send-check-fill me-1"></i>
                {{ $isEditMode && $this->loanApplicationInstance?->status !== \App\Models\LoanApplication::STATUS_DRAFT
                    ? __('forms.button_update_and_resubmit')
                    : __('app.button_submit_application') }}
            </button>
        </div>
    </form>
</div>
