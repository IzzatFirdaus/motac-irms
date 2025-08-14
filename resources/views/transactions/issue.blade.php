{{-- resources/views/transactions/issue.blade.php --}}
@extends('layouts.app') {{-- Or your main admin layout --}}

@section('title', __('Keluarkan Peralatan untuk Pinjaman #:app_id', ['app_id' => $loanApplication->id]))

@section('content')
    <div class="container py-4">
        {{-- Initialize Alpine.js component for this form --}}
        <div x-data="issueForm()" x-init="init()">
            <h1 class="fw-bold mb-4">
                {{ __('Keluarkan Peralatan untuk Permohonan Pinjaman #:app_id', ['app_id' => $loanApplication->id]) }}
            </h1>

            {{-- Loan Application Summary --}}
            <div class="card shadow-sm rounded p-4 mb-4">
                <h3 class="card-title fw-semibold mb-3">
                    {{ __('Maklumat Permohonan Pinjaman') }}</h3>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <span class="fw-bold">{{ __('Pemohon:') }}</span>
                        <span>{{ $loanApplication->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="fw-bold">{{ __('Jabatan:') }}</span>
                        <span>{{ optional($loanApplication->user->department)->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-12">
                        <span class="fw-bold">{{ __('Tujuan:') }}</span>
                        <p class="text-break mb-0">{{ $loanApplication->purpose }}</p>
                    </div>
                    <div class="col-md-12">
                        <span class="fw-bold">{{ __('Tempoh Pinjaman:') }}</span>
                        <span>{{ optional($loanApplication->loan_start_date)->format(config('app.date_format_my', 'd/m/Y')) }}
                            -
                            {{ optional($loanApplication->loan_end_date)->format(config('app.date_format_my', 'd/m/Y')) }}</span>
                    </div>
                </div>
            </div>

            {{-- Issue Form --}}
            <div class="card shadow-sm rounded p-4">
                <h3 class="card-title fw-semibold mb-4">
                    {{ __('Peralatan untuk Dikeluarkan') }}</h3>
                <form action="{{ route('resource-management.admin.loan-transactions.issue', $loanApplication) }}"
                    method="POST">
                    @csrf

                    {{-- Dynamic list of items to be issued --}}
                    <div>
                        <template x-for="(item, index) in itemsToIssue" :key="index">
                            <div class="border rounded p-3 mb-3 bg-light position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h5 fw-semibold mb-0" x-text="'{{ __('Item Baris') }} #' + (index + 1)"></h4>
                                    <template x-if="itemsToIssue.length > 1"> {{-- Show remove button if more than one item --}}
                                        <button type="button" @click="removeItem(index)"
                                            class="btn btn-sm btn-outline-danger" title="{{ __('Buang Item Baris Ini') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label :for="'loan_app_item_id_' + index"
                                            class="form-label">{{ __('Rujuk Item Permohonan Asal') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" :name="'items[' + index + '][loan_application_item_id]'"
                                            :id="'loan_app_item_id_' + index" x-model="item.loan_application_item_id"
                                            @change="updateAvailableEquipmentAndMaxQty(index)" required>
                                            <option value="">-- {{ __('Pilih Item Asal') }} --</option>
                                            @foreach ($loanApplication->loanApplicationItems as $appItem)
                                                @if (($appItem->quantity_approved ?? 0) > ($appItem->quantity_issued ?? 0))
                                                    <option value="{{ $appItem->id }}"
                                                        data-equipment-type="{{ $appItem->equipment_type }}"
                                                        data-qty-approved="{{ $appItem->quantity_approved }}"
                                                        data-qty-issued="{{ $appItem->quantity_issued ?? 0 }}">
                                                        {{ $appItem->equipment_type }} ({{ __('Diluluskan') }}:
                                                        {{ $appItem->quantity_approved }}, {{ __('Telah Dikeluarkan') }}:
                                                        {{ $appItem->quantity_issued ?? 0 }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('items.*.loan_application_item_id')
                                            <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label :for="'equipment_id_' + index"
                                            class="form-label">{{ __('Peralatan Spesifik') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" :name="'items[' + index + '][equipment_id]'"
                                            :id="'equipment_id_' + index" x-model="item.equipment_id" required
                                            :disabled="!item.loan_application_item_id || getAvailableEquipmentForSelectedType(index).length === 0">
                                            <option value="">-- {{ __('Pilih Peralatan') }} --</option>
                                            <template x-for="equip in getAvailableEquipmentForSelectedType(index)"
                                                :key="equip.id">
                                                <option :value="equip.id"
                                                    x-text="equip.tag_id + ' - ' + (equip.brand || '') + ' ' + (equip.model || '') + ' (' + (equip.serial_number || 'N/A SN') + ')'">
                                                </option>
                                            </template>
                                            <template
                                                x-if="item.loan_application_item_id && getAvailableEquipmentForSelectedType(index).length === 0 && item.selected_equipment_type">
                                                <option value="" disabled class="text-warning"
                                                    x-text="'{{ __('Tiada peralatan jenis') }} ' + item.selected_equipment_type + ' {{ __('tersedia untuk dipilih.') }}'">
                                                </option>
                                            </template>
                                            <template x-if="!item.loan_application_item_id">
                                                <option value="" disabled>
                                                    {{ __('Sila pilih item permohonan asal dahulu.') }}</option>
                                            </template>
                                        </select>
                                        @error('items.*.equipment_id')
                                            <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label :for="'quantity_issued_' + index"
                                        class="form-label">{{ __('Kuantiti Dikeluarkan Kali Ini') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" :name="'items[' + index + '][quantity_issued]'"
                                        :id="'quantity_issued_' + index" x-model.number="item.quantity_issued"
                                        min="1" :max="item.max_qty_to_issue" required
                                        :disabled="!item.loan_application_item_id || item.max_qty_to_issue === 0">
                                    <template x-if="item.loan_application_item_id">
                                        <div class="form-text small mt-1">
                                            {{ __('Baki boleh dikeluarkan untuk item ini') }}: <span
                                                x-text="item.max_qty_to_issue">0</span>.
                                            {{ __('Diluluskan') }}: <span
                                                x-text="item.quantity_approved_for_item">0</span>,
                                            {{ __('Telah Dikeluarkan') }}: <span
                                                x-text="item.quantity_already_issued_for_item">0</span>.
                                        </div>
                                    </template>
                                    @error('items.*.quantity_issued')
                                        <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <label
                                        class="form-label">{{ __('Aksesori Dikeluarkan (Tandakan yang berkaitan)') }}</label>
                                    @php
                                        $standardAccessories = config('motac.loan_accessories_list', [
                                            'Power Adapter',
                                            'Beg',
                                            'Mouse',
                                            'Kabel USB',
                                            'Kabel HDMI/VGA',
                                            'Alat Kawalan Jauh',
                                        ]); // [cite: 64]
                                    @endphp
                                    <div class="row row-cols-2 row-cols-sm-3 g-2 mt-1">
                                        @foreach ($standardAccessories as $accessory)
                                            @php $accessoryValue = Str::slug($accessory); @endphp
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $accessoryValue }}"
                                                        :id="'accessory_' + index + '_' + '{{ $accessoryValue }}'"
                                                        :name="'items[' + index + '][accessories_checklist_item][]'"
                                                        x-model="item.accessories_checklist_item">
                                                    <label class="form-check-label small"
                                                        :for="'accessory_' + index + '_' + '{{ $accessoryValue }}'">
                                                        {{ __($accessory) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('items.*.accessories_checklist_item')
                                        <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('items.*.accessories_checklist_item.*')
                                        <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <label :for="'item_notes_' + index"
                                        class="form-label">{{ __('Nota untuk Item Baris Ini (Pilihan)') }}</label>
                                    <textarea class="form-control" :name="'items[' + index + '][issue_item_notes]'" :id="'item_notes_' + index"
                                        x-model="item.issue_item_notes" rows="2"></textarea>
                                    @error('items.*.issue_item_notes')
                                        <div class="form-text text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addItem()"
                        class="btn btn-outline-primary btn-sm text-uppercase mt-3 d-inline-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Tambah Baris Peralatan') }}
                    </button>

                    <div class="mt-4 pt-4 border-top">
                        <div class="mb-3">
                            <label for="receiving_officer_id"
                                class="form-label">{{ __('Pegawai Penerima (Pengguna/Wakil)') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="receiving_officer_id" name="receiving_officer_id" required
                                x-model="transactionDetails.receiving_officer_id">
                                <option value="">-- {{ __('Pilih Pegawai Penerima') }} --</option>
                                <option value="{{ $loanApplication->user_id }}">{{ $loanApplication->user->name }}
                                    ({{ __('Pemohon') }})
                                </option>
                                {{-- Controller MUST pass $allUsers collection --}}
                                @foreach ($allUsers ?? [] as $user)
                                    @if ($user->id !== $loanApplication->user_id)
                                        {{-- Avoid duplicate listing of applicant --}}
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('receiving_officer_id')
                                <div class="form-text text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="issue_notes"
                                class="form-label">{{ __('Nota Keseluruhan Pengeluaran (Pilihan)') }}</label>
                            <textarea class="form-control" id="issue_notes" name="issue_notes" x-model="transactionDetails.issue_notes"
                                rows="3"></textarea>
                            @error('issue_notes')
                                <div class="form-text text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <p class="form-text">
                            {{ __('Pegawai Pengeluar') }}: {{ Auth::user()->name }}.
                            {{ __('Tarikh/Masa pengeluaran akan direkodkan semasa penghantaran borang.') }}
                        </p>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('resource-management.loan-applications.show', $loanApplication) }}"
                            class="btn btn-secondary me-2">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="btn btn-primary" :disabled="isSubmitDisabled()">
                            {{ __('Sahkan & Keluarkan Peralatan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @php
        $jsonAvailableEquipmentGroupedByType = json_encode(
            $availableEquipmentGroupedByType ?? [],
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT,
        );
    @endphp
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('issueForm', () => ({
            allAvailableEquipmentGroupedByType: JSON.parse('{!! $jsonAvailableEquipmentGroupedByType !!}'),
            itemsToIssue: [],
            transactionDetails: {
                receiving_officer_id: @json(old('receiving_officer_id', $loanApplication->user_id ?? '')),
                issue_notes: @json(old('issue_notes', ''))
            },

            init() {
                const oldItems = @json(old('items', []));
                if (oldItems && oldItems.length > 0) {
                    this.itemsToIssue = oldItems.map(item => ({
                        loan_application_item_id: item.loan_application_item_id || '',
                        equipment_id: item.equipment_id || '',
                        quantity_issued: parseInt(item.quantity_issued, 10) || 1,
                        max_qty_to_issue: 0,
                        selected_equipment_type: '',
                        accessories_checklist_item: Array.isArray(item.accessories_checklist_item) ? item.accessories_checklist_item : [],
                        issue_item_notes: item.issue_item_notes || '',
                        quantity_approved_for_item: 0,
                        quantity_already_issued_for_item: 0
                    }));
                    this.itemsToIssue.forEach((_, index) => {
                        // Use $nextTick to ensure DOM elements are available for dataset reading
                        this.$nextTick(() => {
                            this.updateAvailableEquipmentAndMaxQty(index);
                        });
                    });
                } else {
                    this.addItem();
                }
            },

            createEmptyItem() {
                return {
                    loan_application_item_id: '',
                    equipment_id: '',
                    quantity_issued: 1,
                    max_qty_to_issue: 0,
                    selected_equipment_type: '',
                    accessories_checklist_item: [],
                    issue_item_notes: '',
                    quantity_approved_for_item: 0,
                    quantity_already_issued_for_item: 0
                };
            },

            addItem() {
                this.itemsToIssue.push(this.createEmptyItem());
                this.$nextTick(() => {
                    if (this.itemsToIssue.length > 0) {
                        this.updateAvailableEquipmentAndMaxQty(this.itemsToIssue.length - 1);
                    }
                });
            },

            removeItem(index) {
                if (this.itemsToIssue.length > 1) {
                    this.itemsToIssue.splice(index, 1);
                } else {
                    console.warn("Cannot remove the last item. Clear fields if needed.");
                    // Optionally clear the first item's fields
                    // this.itemsToIssue[0] = this.createEmptyItem();
                    // this.$nextTick(() => { this.updateAvailableEquipmentAndMaxQty(0); });
                }
            },

            updateAvailableEquipmentAndMaxQty(itemIndex) {
                const item = this.itemsToIssue[itemIndex];
                if (!item) return;

                const selectElement = document.getElementById('loan_app_item_id_' + itemIndex);
                if (!selectElement) {
                    // console.warn(`Element with ID 'loan_app_item_id_${itemIndex}' not found.`);
                    return;
                }

                const selectedOption = selectElement.selectedIndex >= 0
                    ? selectElement.options[selectElement.selectedIndex]
                    : null;

                if (selectedOption && selectedOption.value) {
                    item.selected_equipment_type = selectedOption.dataset?.equipmentType || '';
                    const qtyApproved = parseInt(selectedOption.dataset?.qtyApproved || '0', 10);
                    const qtyAlreadyIssued = parseInt(selectedOption.dataset?.qtyIssued || '0', 10);

                    item.quantity_approved_for_item = qtyApproved;
                    item.quantity_already_issued_for_item = qtyAlreadyIssued;
                    item.max_qty_to_issue = Math.max(0, qtyApproved - qtyAlreadyIssued);

                    // Reset equipment_id if the main item changes or if max_qty_to_issue is 0
                    // to ensure user re-selects from potentially new list or if nothing can be issued
                    if (item.equipment_id && (this.getAvailableEquipmentForSelectedType(itemIndex).findIndex(e => String(e.id) === String(item.equipment_id)) === -1 || item.max_qty_to_issue <= 0) ) {
                        item.equipment_id = '';
                    }

                    if (item.quantity_issued > item.max_qty_to_issue) {
                        item.quantity_issued = item.max_qty_to_issue;
                    }
                    if (item.quantity_issued < 1 && item.max_qty_to_issue > 0) {
                        item.quantity_issued = 1;
                    }
                    if (item.max_qty_to_issue <= 0) {
                        item.quantity_issued = 0;
                        item.equipment_id = ''; // Clear equipment if none can be issued
                    }

                } else {
                    item.selected_equipment_type = '';
                    item.max_qty_to_issue = 0;
                    item.equipment_id = '';
                    item.quantity_approved_for_item = 0;
                    item.quantity_already_issued_for_item = 0;
                    item.quantity_issued = 0;
                }
            },

            getAvailableEquipmentForSelectedType(itemIndex) {
                const currentItem = this.itemsToIssue[itemIndex];
                if (!currentItem || !currentItem.selected_equipment_type) {
                    return [];
                }

                const equipmentType = currentItem.selected_equipment_type;
                const availableForType = this.allAvailableEquipmentGroupedByType[equipmentType] || [];

                const selectedEquipmentIdsInOtherLines = this.itemsToIssue
                    .filter((it, idx) => idx !== itemIndex)
                    .map(itm => itm.equipment_id ? String(itm.equipment_id) : null)
                    .filter(id => id !== null);

                return availableForType.filter(equip => {
                    if (!equip || typeof equip.id === 'undefined' || equip.id === null) {
                        return false;
                    }
                    const equipIdStr = String(equip.id);
                    const currentItemEquipmentIdStr = currentItem.equipment_id ? String(currentItem.equipment_id) : null;

                    if (selectedEquipmentIdsInOtherLines.includes(equipIdStr)) {
                        return equipIdStr === currentItemEquipmentIdStr;
                    } else {
                        return true;
                    }
                });
            },

            isSubmitDisabled() {
                if (!this.transactionDetails.receiving_officer_id) return true;
                if (this.itemsToIssue.length === 0) return true;

                return this.itemsToIssue.some(item =>
                    !item.loan_application_item_id ||
                    !item.equipment_id ||
                    item.max_qty_to_issue <= 0 || // Cannot issue if approved qty already met/exceeded for the line item
                    item.quantity_issued === null ||
                    item.quantity_issued < 1 ||
                    item.quantity_issued > item.max_qty_to_issue
                );
            }
        }));
    });
    </script>
@endpush
