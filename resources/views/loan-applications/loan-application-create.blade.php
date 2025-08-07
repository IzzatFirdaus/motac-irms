{{-- resources/views/loan-applications/loan-application-create.blade.php --}}
{{-- Create new ICT loan application --}}
{{-- Code unchanged; only filename is updated, and this comment documents the file purpose. --}}

@extends('layouts.app')

@section('title', __('Borang Permohonan Pinjaman Peralatan ICT'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                {{-- FIX: Replaced 'text-dark' with 'text-body' to allow the theme to control text color. --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-body mb-0 d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>{{ __('Borang Permohonan Pinjaman ICT') }}
                    </h1>
                    <a href="{{ route('loan-applications.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle me-1"></i> {{ __('Kembali ke Senarai') }}
                    </a>
                </div>

                @include('_partials._alerts.alert-general')

                <form action="{{ route('loan-applications.store') }}" method="POST" id="loanApplicationCreateForm">
                    @csrf

                    {{-- BAHAGIAN 1 | MAKLUMAT PEMOHON --}}
                    {{-- FIX: Removed hardcoded 'bg-light' and non-standard classes. The .card and .card-header classes are now styled by theme-motac.css. --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">{{ __('Nama Penuh:') }}</label>
                                    {{-- FIX: Replaced 'bg-light' with Bootstrap's theme-aware 'bg-body-secondary'. --}}
                                    <p class="form-control-plaintext bg-body-secondary px-3 py-2 rounded-3 border">
                                        {{ Auth::user()->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">{{ __('Jawatan & Gred:') }}</label>
                                    <p class="form-control-plaintext bg-body-secondary px-3 py-2 rounded-3 border">
                                        {{ optional(Auth::user()->position)->name ?? 'N/A' }}
                                        ({{ optional(Auth::user()->grade)->name ?? 'N/A' }})</p>
                                </div>
                                <div class="col-md-6">
                                    <label for="applicant_phone"
                                        class="form-label fw-semibold">{{ __('No. Telefon') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="applicant_phone" id="applicant_phone"
                                        class="form-control @error('applicant_phone') is-invalid @enderror"
                                        value="{{ old('applicant_phone', Auth::user()->mobile_number) }}" required>
                                    @error('applicant_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">{{ __('Bahagian/Unit:') }}</label>
                                    <p class="form-control-plaintext bg-body-secondary px-3 py-2 rounded-3 border">
                                        {{ optional(Auth::user()->department)->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="mb-3">
                                <label for="purpose" class="form-label fw-semibold">{{ __('Tujuan Permohonan') }}<span
                                        class="text-danger">*</span></label>
                                <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3"
                                    required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location" class="form-label fw-semibold">{{ __('Lokasi Penggunaan') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="location" id="location"
                                        class="form-control @error('location') is-invalid @enderror"
                                        value="{{ old('location') }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="return_location"
                                        class="form-label fw-semibold">{{ __('Lokasi Pemulangan') }}</label>
                                    <input type="text" name="return_location" id="return_location"
                                        class="form-control @error('return_location') is-invalid @enderror"
                                        value="{{ old('return_location') }}">
                                    @error('return_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="loan_start_date"
                                        class="form-label fw-semibold">{{ __('Tarikh Mula') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="loan_start_date" id="loan_start_date"
                                        class="form-control @error('loan_start_date') is-invalid @enderror"
                                        value="{{ old('loan_start_date') }}"
                                        min="{{ now()->toDateTimeLocalString('minute') }}" required>
                                    @error('loan_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="loan_end_date"
                                        class="form-label fw-semibold">{{ __('Tarikh Pulang') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="loan_end_date" id="loan_end_date"
                                        class="form-control @error('loan_end_date') is-invalid @enderror"
                                        value="{{ old('loan_end_date') }}" required>
                                    @error('loan_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">
                                {{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="applicant_is_responsible_officer_checkbox"
                                    id="applicant_is_responsible_officer_checkbox" value="1" class="form-check-input"
                                    {{ old('applicant_is_responsible_officer_checkbox', true) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="applicant_is_responsible_officer_checkbox">{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                            </div>
                            <div id="responsible-officer-fields"
                                class="{{ old('applicant_is_responsible_officer_checkbox', true) ? 'd-none' : '' }}">
                                <p class="form-text small mb-3 text-muted">
                                    {{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.') }}
                                </p>
                                <div class="mb-3">
                                    <label for="responsible_officer_id"
                                        class="form-label fw-semibold">{{ __('Nama Penuh Pegawai Bertanggungjawab') }}<span
                                            id="responsible_officer_id_asterisk" class="text-danger"
                                            style="display:none;">*</span></label>
                                    <select name="responsible_officer_id" id="responsible_officer_id"
                                        class="form-select @error('responsible_officer_id') is-invalid @enderror">
                                        <option value="">- {{ __('Pilih Pegawai') }} -</option>
                                        @if (!empty($responsibleOfficers) && $responsibleOfficers->count())
                                            @foreach ($responsibleOfficers as $officer)
                                                @if (is_object($officer))
                                                    <option value="{{ $officer->id }}"
                                                        {{ old('responsible_officer_id') == $officer->id ? 'selected' : '' }}>
                                                        {{ e($officer->name) }}
                                                        ({{ e(optional($officer->position)->name) ?? __('Posisi T/D') }} -
                                                        {{ e(optional($officer->grade)->name) ?? __('Gred T/D') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('responsible_officer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MAKLUMAT PEGAWAI PENYOKONG --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h2 class="card-title h5 mb-0 fw-semibold">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}<span
                                    class="text-danger">*</span></h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="supporting_officer_id"
                                    class="form-label fw-semibold">{{ __('Nama Penuh Pegawai Penyokong') }}</label>
                                <select name="supporting_officer_id" id="supporting_officer_id"
                                    class="form-select @error('supporting_officer_id') is-invalid @enderror" required>
                                    <option value="">- {{ __('Pilih Pegawai Penyokong') }} -</option>
                                    @php $minSupportGradeLevel = config('motac.approval.min_loan_support_grade_level', 41); @endphp
                                    @if (!empty($supportingOfficers) && $supportingOfficers->count())
                                        @foreach ($supportingOfficers as $officer)
                                            @if (is_object($officer))
                                                <option value="{{ $officer->id }}"
                                                    {{ old('supporting_officer_id') == $officer->id ? 'selected' : '' }}>
                                                    {{ e($officer->name) }}
                                                    ({{ e(optional($officer->position)->name) ?? __('Posisi T/D') }} -
                                                    {{ e(optional($officer->grade)->name) ?? __('Gred T/D') }})
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text small text-muted">
                                    {{ __('Pegawai Penyokong mestilah sekurang-kurangnya Gred :grade atau setara.', ['grade' => $minSupportGradeLevel]) }}
                                </div>
                                @error('supporting_officer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- BAHAGIAN 3 | MAKLUMAT PERALATAN --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN DIMOHON') }}
                            </h2>
                            <button type="button" id="add-item-button"
                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                <i class="bi bi-plus-circle me-1"></i> {{ __('Tambah Item') }}
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <p class="form-text small mb-3 text-muted">
                                {{ __('Sila senaraikan peralatan ICT yang diperlukan.') }}</p>
                            <div id="equipment-items-container" class="vstack gap-3">
                                @php
                                    $oldItems = old('items');
                                    $initialItems = [
                                        ['equipment_type' => '', 'quantity_requested' => 1, 'notes' => ''],
                                    ];
                                    if (!empty($oldItems) && is_array($oldItems)) {
                                        $initialItems = $oldItems;
                                    }
                                @endphp
                                @foreach ($initialItems as $index => $item)
                                    <div class="row g-3 align-items-end border-bottom pb-3 item-row"
                                        id="item-row-{{ $index }}">
                                        <div class="col-md-4">
                                            <label for="items_{{ $index }}_equipment_type"
                                                class="form-label fw-semibold small">{{ __('Jenis Peralatan') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="items[{{ $index }}][equipment_type]"
                                                id="items_{{ $index }}_equipment_type"
                                                class="form-select form-select-sm @error('items.' . $index . '.equipment_type') is-invalid @enderror"
                                                required>
                                                <option value="">- {{ __('Pilih Jenis') }} -</option>
                                                @foreach ($equipmentAssetTypeOptions ?? [] as $typeKey => $typeLabel)
                                                    <option value="{{ $typeKey }}"
                                                        {{ isset($item['equipment_type']) && $item['equipment_type'] == $typeKey ? 'selected' : '' }}>
                                                        {{ e(__($typeLabel)) }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.equipment_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="items_{{ $index }}_quantity_requested"
                                                class="form-label fw-semibold small">{{ __('Kuantiti') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="items[{{ $index }}][quantity_requested]"
                                                id="items_{{ $index }}_quantity_requested"
                                                class="form-control form-control-sm @error('items.' . $index . '.quantity_requested') is-invalid @enderror"
                                                min="1" required value="{{ $item['quantity_requested'] ?? '1' }}">
                                            @error('items.' . $index . '.quantity_requested')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="items_{{ $index }}_notes"
                                                class="form-label fw-semibold small">{{ __('Catatan Tambahan') }}</label>
                                            <input type="text" name="items[{{ $index }}][notes]"
                                                id="items_{{ $index }}_notes"
                                                class="form-control form-control-sm @error('items.' . $index . '.notes') is-invalid @enderror"
                                                value="{{ $item['notes'] ?? '' }}"
                                                placeholder="Cth: Model spesifik, perisian khas">
                                            @error('items.' . $index . '.notes')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger w-100 remove-item-button">
                                                <i class="bi bi-trash3-fill"></i> <span
                                                    class="d-none d-sm-inline">{{ __('Buang') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('items')
                                <div class="d-block text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            <div class="form-text small mt-1 text-muted">
                                {{ __('Pastikan sekurang-kurangnya satu item peralatan disenaraikan.') }}</div>
                        </div>
                    </div>

                    {{-- BAHAGIAN 4 | PENGESAHAN PEMOHON --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h2 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON') }}</h2>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-3 text-muted small">
                                {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua maklumat yang diberikan adalah benar dan peralatan yang dipinjam adalah untuk kegunaan rasmi dan akan berada di bawah tanggungjawab serta penyeliaan saya (atau Pegawai Bertanggungjawab yang dinamakan) sepanjang tempoh pinjaman. Saya juga bersetuju untuk mematuhi semua syarat dan peraturan peminjaman yang ditetapkan oleh pihak MOTAC.') }}
                            </p>
                            <div class="form-check">
                                <input type="checkbox" name="applicant_confirmation" id="applicant_confirmation"
                                    value="1"
                                    class="form-check-input @error('applicant_confirmation') is-invalid @enderror"
                                    required {{ old('applicant_confirmation') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold"
                                    for="applicant_confirmation">{{ __('Saya faham dan bersetuju dengan perakuan di atas.') }}
                                    <span class="text-danger">*</span></label>
                                @error('applicant_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="form-text small mt-2 text-danger fst-italic">
                                {{ __('Sila pastikan semua peralatan disemak semasa penerimaan dan pemulangan. Pemohon/Pegawai Bertanggungjawab akan dipertanggungjawabkan ke atas sebarang kerosakan atau kehilangan peralatan yang dipinjam.') }}
                            </p>
                        </div>
                    </div>

                    <div class="text-center mt-4 pt-3">
                        <a href="{{ route('loan-applications.index') }}"
                            class="btn btn-outline-secondary btn-lg px-4 me-lg-3 me-2">
                            <i class="bi bi-x-circle me-1"></i> {{ __('Batal') }}
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                            <i class="bi bi-send-check-fill me-2"></i> {{ __('Hantar Permohonan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('equipment-items-container');
            const addButton = document.getElementById('add-item-button');
            const templateNode = document.getElementById('equipment-item-template');
            let itemIndex = container.querySelectorAll('.item-row').length > 0 ? container.querySelectorAll(
                '.item-row').length : {{ count(old('items', [['dummy']])) }};


            function updateRemoveButtonsVisibility() {
                const rows = container.querySelectorAll('.item-row');
                rows.forEach(row => {
                    const removeButton = row.querySelector('.remove-item-button');
                    if (removeButton) {
                        removeButton.style.display = (rows.length <= 1) ? 'none' :
                            'flex'; // 'flex' to match d-inline-flex
                    }
                });
            }

            updateRemoveButtonsVisibility(); // Initial check

            if (addButton && templateNode && container) {
                addButton.addEventListener('click', function() {
                    const templateContent = templateNode.innerHTML;
                    const newRowHTML = templateContent.replace(/INDEX/g, itemIndex);

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newRowHTML.trim();
                    const newRowElement = tempDiv.firstChild;

                    // Set unique IDs for inputs/selects for labels
                    newRowElement.querySelectorAll('[data-name-template]').forEach(el => {
                        const nameTemplate = el.getAttribute('data-name-template');
                        const newId = nameTemplate.replace(/\[INDEX\]/g, `_${itemIndex}_`).replace(
                            /[\[\]]/g, ''); // e.g., items_0_equipment_type
                        el.id = newId;
                        const label = newRowElement.querySelector(
                            `label[for="${nameTemplate.replace('INDEX', 'INDEX')}"]`
                        ); // Placeholder for matching, might need refinement
                        if (label && label.getAttribute('for') ===
                            `items_INDEX_${el.name.split('[')[2].replace(']', '')}`) {
                            label.setAttribute('for', newId);
                        }
                    });

                    container.appendChild(newRowElement);
                    itemIndex++;
                    updateRemoveButtonsVisibility();
                });
            }

            if (container) {
                container.addEventListener('click', function(event) {
                    const removeButton = event.target.closest('.remove-item-button');
                    if (removeButton) {
                        const rows = container.querySelectorAll('.item-row');
                        if (rows.length > 1) {
                            removeButton.closest('.item-row').remove();
                        } else {
                            // Consider using a non-blocking notification if available
                            alert('{{ __('Sekurang-kurangnya satu item peralatan diperlukan.') }}');
                        }
                        updateRemoveButtonsVisibility();
                    }
                });
            }

            const responsibleOfficerCheckbox = document.getElementById('applicant_is_responsible_officer_checkbox');
            const responsibleOfficerSection = document.getElementById('responsible-officer-fields');
            const responsibleOfficerIdField = document.getElementById('responsible_officer_id');
            const responsibleOfficerAsterisk = document.getElementById('responsible_officer_id_asterisk');

            function toggleResponsibleOfficerDisplay() {
                if (!responsibleOfficerCheckbox || !responsibleOfficerSection || !responsibleOfficerIdField || !
                    responsibleOfficerAsterisk) return;

                if (responsibleOfficerCheckbox.checked) {
                    responsibleOfficerSection.classList.add('d-none');
                    responsibleOfficerIdField.value = '';
                    responsibleOfficerIdField.removeAttribute('required');
                    responsibleOfficerAsterisk.style.display = 'none';
                } else {
                    responsibleOfficerSection.classList.remove('d-none');
                    responsibleOfficerIdField.setAttribute('required', 'required');
                    responsibleOfficerAsterisk.style.display = 'inline';
                }
            }

            if (responsibleOfficerCheckbox) {
                responsibleOfficerCheckbox.addEventListener('change', toggleResponsibleOfficerDisplay);
                toggleResponsibleOfficerDisplay();
            }

            const startDateInput = document.getElementById('loan_start_date');
            const endDateInput = document.getElementById('loan_end_date');

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    if (this.value) {
                        endDateInput.min = this.value;
                        // If end date was before new start date, clear it or set to new start date
                        if (endDateInput.value && endDateInput.value < this.value) {
                            endDateInput.value = '';
                        }
                    } else {
                        endDateInput.min = ''; // Remove min constraint if start date is cleared
                    }
                });
                // Set initial min for end date if start date has a value (e.g. from old input)
                if (startDateInput.value) {
                    endDateInput.min = startDateInput.value;
                }
            }
        });
    </script>
@endpush
