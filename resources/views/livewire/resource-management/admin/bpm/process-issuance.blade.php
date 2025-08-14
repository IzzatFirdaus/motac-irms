<div>
    @section('title', __('transaction.issuance_form.page_title', ['id' => $loanApplication->id]))

    {{-- UPDATED: Added container-fluid for a full-width layout --}}
    <div class="container-fluid">
        {{-- UPDATED: New two-column row structure --}}
        <div class="row g-4">

            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h4 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="bi bi-box-arrow-up-right me-2"></i>
                        {{ __('transaction.issuance_form.header') }}
                        <span class="badge bg-label-primary ms-2">{{ __('transaction.issuance_form.for_application') }} #{{ $loanApplication->id }}</span>
                    </h4>
                </div>

                @include('_partials._alerts.alert-general')

                {{-- Issuance Form --}}
                <form wire:submit.prevent="submitIssue">
                    <div class="card motac-card">
                        <div class="card-header motac-card-header">
                            <h5 class="card-title mb-0">{{ __('transaction.issuance_form.actual_issuance_record') }}</h5>
                        </div>
                        <div class="card-body">
                            @if(empty($issueItems))
                                <div class="alert alert-warning">{{ __('transaction.issuance_form.no_items_to_issue') }}</div>
                            @else
                                @foreach ($issueItems as $index => $issueItem)
                                    <div wire:key="issue-item-{{ $index }}" class="border rounded p-3 mb-3 {{ $loop->odd ? 'bg-light-subtle' : '' }}">
                                        <h6 class="mb-3 fw-semibold border-bottom pb-2">{{ __('transaction.issuance_form.issue_item_header', ['index' => $index + 1]) }} : <span class="text-primary">{{ \App\Models\Equipment::getAssetTypeOptions()[$issueItem['equipment_type']] ?? 'N/A' }}</span></h6>

                                        <div class="mb-3">
                                            <label for="issueItems_{{ $index }}_equipment_id" class="form-label">{{ __('transaction.issuance_form.select_specific_equipment') }} <span class="text-danger">*</span></label>
                                            <select wire:model.live="issueItems.{{ $index }}.equipment_id" id="issueItems_{{ $index }}_equipment_id" class="form-select @error('issueItems.'.$index.'.equipment_id') is-invalid @enderror">
                                                <option value="">{{ __('transaction.issuance_form.placeholder_select_equipment') }}</option>
                                                @foreach ($availableEquipment->where('asset_type', $issueItem['equipment_type']) as $equipment)
                                                    <option value="{{ $equipment->id }}">
                                                        {{ $equipment->brand }} {{ $equipment->model }} (Tag: {{ $equipment->tag_id ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                                @if($availableEquipment->where('asset_type', $issueItem['equipment_type'])->isEmpty())
                                                    <option value="" disabled>{{ __('transaction.issuance_form.no_equipment_available') }}</option>
                                                @endif
                                            </select>
                                            @error('issueItems.'.$index.'.equipment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="form-label">{{ __('transaction.issuance_form.accessories_checklist') }}:</label>
                                            <div class="row">
                                                @forelse ($allAccessoriesList as $accessory)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" wire:model="issueItems.{{ $index }}.accessories_checklist" value="{{ $accessory }}" id="accessory_{{ $index }}_{{ Str::slug($accessory) }}" class="form-check-input">
                                                            <label class="form-check-label" for="accessory_{{ $index }}_{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12"><p class="small text-muted">{{ __('transaction.issuance_form.no_accessories_configured') }}</p></div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="receiving_officer_id" class="form-label fw-semibold">{{ __('transaction.issuance_form.received_by') }} <span class="text-danger">*</span></label>
                                    <select wire:model="receiving_officer_id" id="receiving_officer_id" class="form-select @error('receiving_officer_id') is-invalid @enderror">
                                        <option value="">{{ __('transaction.issuance_form.placeholder_select_receiver') }}</option>
                                        @foreach($potentialRecipients as $recipient)
                                            <option value="{{ $recipient->id }}">
                                                {{ $recipient->name }}
                                                @if($recipient->id === $loanApplication->user_id)
                                                    ({{ __('transaction.issuance_form.option_applicant') }})
                                                @elseif($recipient->id === $loanApplication->responsible_officer_id)
                                                    ({{ __('transaction.issuance_form.option_responsible_officer') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('receiving_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="transaction_date" class="form-label fw-semibold">{{ __('transaction.issuance_form.issuance_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror">
                                    @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="issue_notes" class="form-label fw-semibold">{{ __('transaction.issuance_form.issuance_notes') }}</label>
                                <textarea wire:model="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror" rows="3" placeholder="{{ __('transaction.issuance_form.placeholder_issuance_notes') }}"></textarea>
                                @error('issue_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end align-items-center">
                            <div class="me-3" wire:loading wire:target="submitIssue">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="ms-1">{{ __('forms.text_processing') }}</span>
                            </div>

                            <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary me-2">
                                <i class="bi bi-x-circle me-1"></i>
                                {{ __('transaction.issuance_form.button_cancel') }}
                            </a>

                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitIssue">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ __('transaction.issuance_form.button_record_issuance') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="position-sticky top-0">
                    {{-- Loan Application Details Card --}}
                    <div class="card motac-card">
                        <div class="card-header motac-card-header">
                            <h5 class="card-title mb-0">{{ __('transaction.issuance_form.related_application_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <span class="fw-semibold d-block">{{ __('transaction.issuance_form.applicant') }}:</span>
                                    <span>{{ $loanApplication->user->name }}</span>
                                </div>
                                <div class="col-12 mb-3">
                                    <span class="fw-semibold d-block">{{ __('transaction.issuance_form.purpose') }}:</span>
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $loanApplication->purpose }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <span class="fw-semibold d-block">{{ __('transaction.issuance_form.loan_date') }}:</span>
                                    <span>{{ $loanApplication->loan_start_date->translatedFormat('d M Y, g:i A') }}</span>
                                </div>
                                <div class="col-12 mb-2">
                                    <span class="fw-semibold d-block">{{ __('transaction.issuance_form.expected_return_date') }}:</span>
                                    <span>{{ $loanApplication->loan_end_date->translatedFormat('d M Y, g:i A') }}</span>
                                </div>
                            </div>
                            <h6 class="mt-4 mb-2 fw-semibold">{{ __('transaction.issuance_form.approved_items') }}:</h6>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small px-3 py-2">#</th>
                                            <th class="small px-3 py-2">{{ __('transaction.issuance_form.equipment_type') }}</th>
                                            <th class="small px-3 py-2 text-center">{{ __('transaction.issuance_form.approved_qty') }}</th>
                                            <th class="small px-3 py-2 text-center">{{ __('transaction.issuance_form.balance_to_issue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->loanApplicationItems as $item)
                                        <tr>
                                            <td class="small px-3 py-2">{{ $loop->iteration }}</td>
                                            <td class="small px-3 py-2">{{ \App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? $item->equipment_type }}</td>
                                            <td class="small px-3 py-2 text-center">{{ $item->quantity_approved }}</td>
                                            <td class="small px-3 py-2 text-center fw-bold">{{ ($item->quantity_approved ?? 0) - ($item->quantity_issued ?? 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
