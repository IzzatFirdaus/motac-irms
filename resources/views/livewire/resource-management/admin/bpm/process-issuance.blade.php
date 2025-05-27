<<<<<<< HEAD
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
=======
{{-- resources/views/livewire/resource-management/admin/bpm/process-issuance.blade.php --}}
<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Rekod Pengeluaran Peralatan untuk Permohonan Pinjaman #{{ $loanApplication->id }}</h2>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
     @if ($errors->any())
         <div class="alert alert-danger mb-4">
             <p class="font-semibold">Sila perbetulkan ralat berikut:</p>
             <ul class="list-disc list-inside">
                 @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                 @endforeach
             </ul>
         </div>
     @endif


    {{-- Loan Application Details Card (similar to issue.blade.php) --}}
    <div class="card mb-6">
        <h3 class="card-title">Butiran Permohonan Pinjaman</h3>
        <p class="mb-2"><span class="font-semibold">Pemohon:</span> {{ $loanApplication->user->name ?? 'N/A' }}</p>
        <p class="mb-2"><span class="font-semibold">Tujuan Permohonan:</span> {{ $loanApplication->purpose ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Lokasi Penggunaan:</span> {{ $loanApplication->location ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Tarikh Pinjaman:</span> {{ $loanApplication->loan_start_date?->format('Y-m-d') ?? 'N/A' }}</p>
         <p class="mb-2"><span class="font-semibold">Tarikh Dijangka Pulang:</span> {{ $loanApplication->loan_end_date?->format('Y-m-d') ?? 'N/A' }}</p>

        @if ($loanApplication->items->isNotEmpty())
            <h4 class="text-lg font-semibold mt-4 mb-2 text-gray-700">Item Peralatan Dimohon:</h4>
            <div class="overflow-x-auto shadow-sm rounded-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Bil.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Jenis Peralatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kuantiti Dimohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kuantiti Diluluskan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($loanApplication->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->equipment_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->quantity_requested ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">{{ $item->quantity_approved ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-b">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <form wire:submit.prevent="submitIssue">
        <div class="card">
            <h3 class="card-title">Rekod Pengeluaran Peralatan</h3>

            <div class="form-group">
                <label for="selectedEquipmentIds" class="block text-gray-700 text-sm font-bold mb-2">Pilih Peralatan untuk Dikeluarkan*:</label>
                <select wire:model.defer="selectedEquipmentIds" id="selectedEquipmentIds" class="form-control @error('selectedEquipmentIds') border-red-500 @enderror" multiple required>
                    @forelse ($availableEquipment as $equipment)
                        <option value="{{ $equipment->id }}">
                            {{ $equipment->brand }} {{ $equipment->model }} (Tag: {{ $equipment->tag_id ?? 'N/A' }}) - {{ $equipment->asset_type_label }}  {{-- --}}
                        </option>
                    @empty
                        <option value="" disabled>Tiada peralatan tersedia yang sepadan.</option>
                    @endforelse
                </select>
                @error('selectedEquipmentIds') <span class="text-danger">{{ $message }}</span> @enderror
                @error('selectedEquipmentIds.*') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-gray-700 text-sm font-bold mb-2">Senarai Semak Aksesori Dikeluarkan:</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($allAccessoriesList as $accessory)
                        <div class="flex items-center">
                            <input type="checkbox" wire:model.defer="accessories" value="{{ $accessory }}" id="accessory-{{ Str::slug($accessory) }}" class="form-check-input h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700" for="accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                        </div>
                    @endforeach
                </div>
                @error('accessories') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="issue_notes" class="block text-gray-700 text-sm font-bold mb-2">Catatan Pengeluaran:</label>
                <textarea wire:model.defer="issue_notes" id="issue_notes" class="form-control @error('issue_notes') border-red-500 @enderror" rows="3"></textarea>
                @error('issue_notes') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="block text-gray-700 text-sm font-bold mb-1">Diproses Oleh:</label>
                <p class="text-gray-800">{{ Auth::user()->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="flex justify-center mt-6">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <svg wire:loading wire:target="submitIssue" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="submitIssue">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                Rekod Pengeluaran Peralatan
            </button>
        </div>
    </form>

     <div class="mt-6 text-center">
         <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApplication) }}" class="btn btn-secondary"> {{-- --}}
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
             </svg>
             Kembali ke Butiran Permohonan
         </a>
     </div>
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
</div>
