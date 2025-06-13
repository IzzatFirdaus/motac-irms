<div>
    @php
        $pageTitle = $this->generatePageTitle();
        if (
            $this->editing_application_id &&
            $this->loanApplicationInstance &&
            $this->loanApplicationInstance->reference_number
        ) {
            $pageTitle .= ' (' . $this->loanApplicationInstance->reference_number . ')';
        }
    @endphp
    @section('title', $pageTitle)

    <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
        <div class="d-flex align-items-center">
            <div class="d-flex flex-column align-items-center me-3">
                <img src="{{ asset('assets/img/logo/logo_bpm.png') }}" alt="BPM Logo"
                    style="height: 60px; width: 60px; object-fit: contain;">
                <small class="text-muted text-center mt-1">@lang('forms.bpm_office_display')</small>
            </div>
            <h1 class="h4 fw-bold text-dark mb-0">{{ $this->generatePageTitle() }}</h1>
        </div>
        <small class="text-muted">@lang('forms.text_form_ref_no')</small>
    </div>

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

    @include('_partials._alerts.alert-general')

    <form wire:submit.prevent="submitLoanApplication" x-data="{
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
        clearCache() {
            localStorage.removeItem(this.autosaveKey);
        }
    }"
        @init="
            if ({{ !$isEditMode ? 'true' : 'false' }}) {
                const savedData = localStorage.getItem(autosaveKey);
                if (savedData) {
                    console.log('Found cached form data. Restoring...');
                    $wire.loadStateFromCache(JSON.parse(savedData));
                }
            }
        "
        @input.debounce.750ms="
            if ({{ !$isEditMode ? 'true' : 'false' }}) {
                localStorage.setItem(autosaveKey, JSON.stringify(getDataForCache()))
            }
        ">

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                        @lang('forms.section_applicant_info_ict')
                    </h2>
                    <small class="text-muted">@lang('messages.instruction_mandatory_fields')</small>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">@lang('forms.label_full_name')</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $applicantName }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label for="applicant_phone" class="form-label fw-medium">@lang('forms.label_phone_number')</label>
                        <input type="text" id="applicant_phone" wire:model.blur="applicant_phone"
                            class="form-control @error('applicant_phone') is-invalid @enderror"
                            placeholder="@lang('forms.placeholder_phone_number')">
                        @error('applicant_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">@lang('forms.label_position_grade')</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                            {{ $applicantPositionAndGrade }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">@lang('forms.label_department_unit')</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                            {{ $applicantDepartment }}</p>
                    </div>
                    <div class="col-md-12">
                        <label for="purpose" class="form-label fw-medium">@lang('forms.label_application_purpose')</label>
                        <textarea id="purpose" wire:model.blur="purpose" rows="3"
                            class="form-control @error('purpose') is-invalid @enderror" placeholder="@lang('forms.placeholder_purpose')"></textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label fw-medium">@lang('forms.label_location_ict')</label>
                        <input type="text" id="location" wire:model.blur="location"
                            class="form-control @error('location') is-invalid @enderror"
                            placeholder="@lang('forms.placeholder_usage_location')">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="return_location" class="form-label fw-medium">@lang('forms.label_expected_return_location')</label>
                        <input type="text" id="return_location" wire:model.blur="return_location"
                            class="form-control @error('return_location') is-invalid @enderror"
                            placeholder="@lang('forms.placeholder_return_location')">
                        @error('return_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_start_date" class="form-label fw-medium">@lang('forms.label_loan_date')</label>
                        <input type="datetime-local" id="loan_start_date" wire:model.blur="loan_start_date"
                            class="form-control @error('loan_start_date') is-invalid @enderror"
                            min="{{ now()->toDateTimeLocalString('minute') }}">
                        @error('loan_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_end_date" class="form-label fw-medium">@lang('forms.label_expected_return_date')</label>
                        <input type="datetime-local" id="loan_end_date" wire:model.blur="loan_end_date"
                            class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-check-fill me-2 fs-5"></i>
                    @lang('forms.section_responsible_officer_info')
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check mb-3">
                    <input id="applicant_is_responsible_officer" wire:model.live="applicant_is_responsible_officer"
                        type="checkbox" class="form-check-input">
                    <label for="applicant_is_responsible_officer"
                        class="form-check-label fw-medium">@lang('forms.instruction_responsible_officer_is_applicant')</label>
                </div>

                @if (!$applicant_is_responsible_officer)
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="responsible_officer_id" class="form-label fw-medium">@lang('forms.label_responsible_officer_name')<span
                                    class="text-danger">*</span></label>
                            <div wire:ignore>
                                <select id="responsible_officer_id" wire:model="responsible_officer_id"
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

        {{-- MAKLUMAT PEGAWAI PENYOKONG --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-2 fs-5"></i>
                    @lang('forms.section_supporting_officer_info') <span class="text-danger">*</span>
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="mb-3">
                    <label for="supporting_officer_id" class="form-label fw-medium">@lang('forms.label_supporting_officer_name')</label>
                    <div wire:ignore>
                        <select id="supporting_officer_id" wire:model="supporting_officer_id"
                            class="form-select @error('supporting_officer_id') is-invalid @enderror">
                            <option value="">-- @lang('forms.placeholder_select_supporting_officer') --</option>
                            @foreach ($supportingOfficerOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="form-text text-muted">@lang('forms.text_supporter_grade_requirement', ['grade' => config('motac.approval.min_loan_support_grade_level', 41)])</small>
                    @error('supporting_officer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-tools me-2 fs-5"></i>
                        @lang('forms.section_equipment_details_ict') <span class="text-danger">*</span>
                    </h2>
                    <button type="button" wire:click="addLoanItem" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> @lang('app.button_add_equipment')
                    </button>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                @forelse ($loan_application_items as $index => $item)
                    <div wire:key="loan_item_{{ $index }}" class="list-group-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0 fw-medium text-primary">@lang('forms.title_equipment_item', ['index' => $index + 1])</h3>
                            @if (count($loan_application_items) > 1)
                                <button type="button" wire:click="removeLoanItem({{ $index }})"
                                    title="@lang('app.button_remove_equipment')" class="btn btn-sm btn-icon btn-text-danger p-0">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            @endif
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="item_{{ $index }}_equipment_type"
                                    class="form-label">@lang('forms.label_equipment_type')</label>
                                <select id="item_{{ $index }}_equipment_type"
                                    wire:model="loan_application_items.{{ $index }}.equipment_type"
                                    class="form-select @error('loan_application_items.' . $index . '.equipment_type') is-invalid @enderror">
                                    <option value="">-- @lang('forms.placeholder_select_type') --</option>
                                    @foreach ($equipmentTypeOptions as $key => $label)
                                        <option value="{{ $key }}">@lang('forms.option_equipment_' . Illuminate\Support\Str::snake($key))</option>
                                    @endforeach
                                </select>
                                @error('loan_application_items.' . $index . '.equipment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="item_{{ $index }}_quantity_requested"
                                    class="form-label">@lang('forms.label_quantity')</label>
                                <input type="number" id="item_{{ $index }}_quantity_requested"
                                    wire:model="loan_application_items.{{ $index }}.quantity_requested"
                                    min="1"
                                    class="form-control @error('loan_application_items.' . $index . '.quantity_requested') is-invalid @enderror">
                                @error('loan_application_items.' . $index . '.quantity_requested')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="item_{{ $index }}_notes"
                                    class="form-label">@lang('forms.label_remarks')</label>
                                <input type="text" id="item_{{ $index }}_notes"
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
                    <div class="text-center py-3 border rounded bg-light-subtle">
                        <p class="text-muted mb-0">@lang('forms.text_no_equipment_added')</p>
                    </div>
                @endforelse
                @error('loan_application_items')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Syarat-Syarat Permohonan --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-card-checklist me-2 fs-5"></i>
                    @lang('messages.terms_ict_loan_title')
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div id="termsBox"
                    style="height: 250px; overflow-y: scroll; border: 1px solid #dee2e6; padding: 15px; background-color: #f8f9fa; border-radius: 0.375rem;"
                    class="small text-muted motac-terms-box" x-ref="termsBox"
                    @scroll.debounce.150ms="if ($refs.termsBox.scrollTop + $refs.termsBox.clientHeight >= $refs.termsBox.scrollHeight - 20) { termsScrolled = true; }">
                    <p class="fw-bold mb-2">@lang('messages.terms_reminder')</p>
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">@lang('messages.terms_item1')</li>
                        <li class="mb-2">@lang('messages.terms_item2')</li>
                        <li class="mb-2">@lang('messages.terms_item3')</li>
                        <li class="mb-2">@lang('messages.terms_item4')</li>
                        <li class="mb-2">@lang('messages.terms_item5')</li>
                        <li class="mb-2">@lang('messages.terms_item6')</li>
                        <li class="mb-2">@lang('messages.terms_item7')</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                    @lang('forms.section_applicant_confirmation_ict')
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check">
                    <input id="applicant_confirmation" wire:model="applicant_confirmation" type="checkbox"
                        value="1" class="form-check-input @error('applicant_confirmation') is-invalid @enderror"
                        x-model="applicantConfirmation">
                    <label for="applicant_confirmation" class="form-check-label fw-medium">
                        @lang('forms.text_applicant_declaration_ict')
                        <span class="text-danger">*</span>
                    </label>
                    @error('applicant_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Document Version Info --}}
        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
            <small class="text-muted">
                <strong>@lang('forms.text_document_no')</strong> PK.(S).KPK.08.(L3) Pin.1
            </small>
            <small class="text-muted">
                <strong>@lang('forms.text_effective_date')</strong> 1/1/2024
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 pt-3">

            {{-- Print Button (from previous request) --}}
            @if ($isEditMode && $loanApplicationInstance)
                <a href="{{ route('loan-applications.print', ['loan_application' => $loanApplicationInstance->id]) }}"
                    target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-printer-fill me-1"></i>
                    @lang('app.button_print_form')
                </a>
            @endif

            {{-- Save as Draft Button --}}
            {{-- This button is type="button" and specifically calls the "saveAsDraft" action. --}}
            <button type="button" wire:click="saveAsDraft" wire:loading.attr="disabled" @click="clearCache()"
                class="btn btn-secondary">

                {{-- The wire:target="saveAsDraft" ensures this content only swaps during the "saveAsDraft" action. --}}
                <span wire:loading.remove wire:target="saveAsDraft">
                    <i class="bi bi-save me-1"></i>
                    @lang('app.button_save_draft')
                </span>
                <span wire:loading wire:target="saveAsDraft" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    @lang('app.text_saving')
                </span>
            </button>

            {{-- Submit Application Button --}}
            {{-- This is a type="submit" button that triggers the form's wire:submit.prevent="submitLoanApplication" event. --}}
            <button type="submit" wire:loading.attr="disabled" @click="clearCache()" class="btn btn-primary"
                x-bind:disabled="!applicantConfirmation || !termsScrolled">

                {{-- The wire:target="submitLoanApplication" ensures this content only swaps during the "submitLoanApplication" action. --}}
                <span wire:loading.remove wire:target="submitLoanApplication">
                    <i class="bi bi-send-check-fill me-1"></i>
                    {{ $isEditMode && $this->loanApplicationInstance?->status !== \App\Models\LoanApplication::STATUS_DRAFT ? __('forms.button_update_and_resubmit') : __('app.button_submit_application') }}
                </span>
                <span wire:loading wire:target="submitLoanApplication" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    @lang('app.text_processing')
                </span>
            </button>
        </div>
    </form>
</div>
