@extends('layouts.app') {{-- Ensure layouts.app is Bootstrap-compatible --}}

@section('title', __('Borang Permohonan Pinjaman Peralatan ICT'))

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9"> {{-- Adjust column width as needed --}}

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h3 fw-bold text-dark mb-0">{{ __('Borang Permohonan Pinjaman Peralatan ICT') }}</h2>
                <a href="{{ route('loan-applications.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                </a>
            </div>

            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <h5 class="alert-heading fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Ralat Pengesahan Sila Semak Input Anda:') }}</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Display session messages --}}
            @include('partials.alert-messages') {{-- Create this partial if it doesn't exist --}}

            <form action="{{ route('loan-applications.store') }}" method="POST" id="loanApplicationCreateForm">
                @csrf

                {{-- BAHAGIAN 1 | MAKLUMAT PEMOHON --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium small text-muted">{{ __('Nama Penuh:') }}</label>
                                <p class="form-control-plaintext bg-light px-3 py-2 rounded-3">{{ Auth::user()->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="applicant_phone" class="form-label fw-semibold">{{ __('No. Telefon (Untuk Dihubungi)') }}<span class="text-danger">*</span></label>
                                <input type="text" name="applicant_phone" id="applicant_phone" class="form-control @error('applicant_phone') is-invalid @enderror" value="{{ old('applicant_phone', Auth::user()->mobile_number ?? (Auth::user()->phone_number ?? '')) }}" placeholder="Cth: 012-3456789" required>
                                @error('applicant_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium small text-muted">{{ __('Jawatan & Gred:') }}</label>
                                <p class="form-control-plaintext bg-light px-3 py-2 rounded-3">{{ optional(Auth::user()->position)->name ?? 'N/A' }} ({{ optional(Auth::user()->grade)->name ?? 'N/A' }})</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium small text-muted">{{ __('Bahagian/Unit:') }}</label>
                                <p class="form-control-plaintext bg-light px-3 py-2 rounded-3">{{ optional(Auth::user()->department)->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-semibold">{{ __('Tujuan Permohonan') }}<span class="text-danger">*</span></label>
                            <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3" required placeholder="Nyatakan tujuan permohonan peralatan ICT...">{{ old('purpose') }}</textarea>
                            @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label fw-semibold">{{ __('Lokasi Penggunaan Peralatan') }}<span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" required value="{{ old('location') }}" placeholder="Cth: Bilik Mesyuarat Utama, Aras 10">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="return_location" class="form-label fw-semibold">{{ __('Lokasi Dijangka Pulang / Pemulangan') }}</label>
                                <input type="text" name="return_location" id="return_location" class="form-control @error('return_location') is-invalid @enderror" value="{{ old('return_location') }}" placeholder="Cth: Kaunter BPM (Jika berbeza daripada lokasi guna)">
                                @error('return_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="loan_start_date" class="form-label fw-semibold">{{ __('Tarikh & Masa Pinjaman Bermula') }}<span class="text-danger">*</span></label>
                                <input type="datetime-local" name="loan_start_date" id="loan_start_date" class="form-control @error('loan_start_date') is-invalid @enderror" required value="{{ old('loan_start_date') }}">
                                @error('loan_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="loan_end_date" class="form-label fw-semibold">{{ __('Tarikh & Masa Dijangka Pulang') }}<span class="text-danger">*</span></label>
                                <input type="datetime-local" name="loan_end_date" id="loan_end_date" class="form-control @error('loan_end_date') is-invalid @enderror" required value="{{ old('loan_end_date') }}">
                                @error('loan_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="applicant_is_responsible_officer_checkbox" id="applicant_is_responsible_officer_checkbox" value="1" class="form-check-input" {{ old('applicant_is_responsible_officer_checkbox', true) ? 'checked' : '' }} onchange="toggleResponsibleOfficerSection()">
                            <label class="form-check-label" for="applicant_is_responsible_officer_checkbox">{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                        </div>
                        <div id="responsible-officer-fields" style="{{ old('applicant_is_responsible_officer_checkbox', true) ? 'display:none;' : '' }}">
                            <p class="form-text small mb-3">{{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.') }}</p>
                            <div class="mb-3">
                                <label for="responsible_officer_id" class="form-label fw-semibold">{{ __('Nama Penuh Pegawai Bertanggungjawab') }}<span id="responsible_officer_id_asterisk" class="text-danger" style="display:none;">*</span></label>
                                <select name="responsible_officer_id" id="responsible_officer_id" class="form-select @error('responsible_officer_id') is-invalid @enderror" {{ old('applicant_is_responsible_officer_checkbox', true) ? '' : 'required' }}>
                                    <option value="">- {{ __('Pilih Pegawai') }} -</option>
                                    @php
                                        // Controller should pass $responsibleOfficers (e.g., all users or users with certain roles/grades)
                                        // This is a placeholder. Ensure $responsibleOfficers is available from your controller.
                                        $responsibleOfficers = $responsibleOfficers ?? \App\Models\User::orderBy('name')->get();
                                    @endphp
                                    @foreach ($responsibleOfficers as $officer)
                                        <option value="{{ $officer->id }}" {{ old('responsible_officer_id') == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->name }} ({{ optional($officer->position)->name ?? 'Posisi T/D' }} - {{ optional($officer->grade)->name ?? 'Gred T/D' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsible_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAKLUMAT PEGAWAI PENYOKONG --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('MAKLUMAT PEGAWAI PENYOKONG') }}<span class="text-danger">*</span></h4>
                    </div>
                    <div class="card-body p-4">
                         <div class="mb-3">
                            <label for="supporting_officer_id" class="form-label fw-semibold">{{ __('Nama Penuh Pegawai Penyokong') }}</label>
                            <select name="supporting_officer_id" id="supporting_officer_id" class="form-select @error('supporting_officer_id') is-invalid @enderror" required>
                                <option value="">- {{ __('Pilih Pegawai Penyokong') }} -</option>
                                @php
                                    // Controller should pass $supportingOfficers (e.g., users with grade level >= configured minimum)
                                    // This is a placeholder. Ensure $supportingOfficers is available and filtered by grade.
                                    // Example: \App\Models\User::whereHas('grade', fn($q) => $q->where('level', '>=', config('motac.approval.min_loan_support_grade_level', 41)))->orderBy('name')->get();
                                    $minSupportGradeLevel = config('motac.approval.min_loan_support_grade_level', 41);
                                    $supportingOfficers = $supportingOfficers ?? []; // Assume passed from controller
                                @endphp
                                @foreach ($supportingOfficers as $officer)
                                    <option value="{{ $officer->id }}" {{ old('supporting_officer_id') == $officer->id ? 'selected' : '' }}>
                                        {{ $officer->name }} ({{ optional($officer->position)->name ?? 'Posisi T/D' }} - {{ optional($officer->grade)->name ?? 'Gred T/D' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">{{__('Pegawai Penyokong mestilah sekurang-kurangnya Gred :grade atau setara.', ['grade' => $minSupportGradeLevel])}} [cite: 129, 62]</div>
                            @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- BAHAGIAN 3 | MAKLUMAT PERALATAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN') }}</h4>
                        <button type="button" id="add-item-button-bootstrap" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('Tambah Item') }}
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="form-text small mb-3">{{ __('Sila senaraikan peralatan ICT yang diperlukan.') }} [cite: 126]</p>
                        <div id="equipment-items-container">
                            @php
                                $initialItems = old('items', [['equipment_type' => '', 'quantity_requested' => '1', 'notes' => '']]);
                                if (empty($initialItems)) {
                                    $initialItems = [['equipment_type' => '', 'quantity_requested' => '1', 'notes' => '']];
                                }
                            @endphp
                            @foreach ($initialItems as $index => $item)
                            <div class="row g-3 align-items-end mb-3 border-bottom pb-3 pt-2 item-row" id="item-row-{{ $index }}">
                                <div class="col-md-4">
                                    <label for="items_{{ $index }}_equipment_type" class="form-label fw-semibold small">{{ __('Jenis Peralatan') }}<span class="text-danger">*</span></label>
                                    <select name="items[{{ $index }}][equipment_type]" id="items_{{ $index }}_equipment_type" class="form-select form-select-sm @error('items.'.$index.'.equipment_type') is-invalid @enderror" required>
                                        <option value="">- {{ __('Pilih Jenis') }} -</option>
                                        @foreach(\App\Models\Equipment::getAssetTypeOptions() as $typeKey => $typeLabel) {{-- Assumes this static method exists --}}
                                            <option value="{{ $typeKey }}" {{ (isset($item['equipment_type']) && $item['equipment_type'] == $typeKey) ? 'selected' : '' }}>{{ __($typeLabel) }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.equipment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="items_{{ $index }}_quantity_requested" class="form-label fw-semibold small">{{ __('Kuantiti') }}<span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][quantity_requested]" id="items_{{ $index }}_quantity_requested" class="form-control form-control-sm @error('items.'.$index.'.quantity_requested') is-invalid @enderror" min="1" required value="{{ $item['quantity_requested'] ?? '1' }}">
                                    @error('items.'.$index.'.quantity_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="items_{{ $index }}_notes" class="form-label fw-semibold small">{{ __('Catatan Tambahan') }}</label>
                                    <input type="text" name="items[{{ $index }}][notes]" id="items_{{ $index }}_notes" class="form-control form-control-sm @error('items.'.$index.'.notes') is-invalid @enderror" value="{{ $item['notes'] ?? '' }}" placeholder="Cth: Model spesifik, perisian khas">
                                    @error('items.'.$index.'.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" onclick="removeItemRowBootstrap(this)" class="btn btn-sm btn-outline-danger w-100 remove-item-button" {{ ($loop->count <= 1 && count($initialItems) <=1 ) ? 'style="display:none;"' : '' }}>
                                       <i class="bi bi-trash3"></i> {{ __('Buang') }}
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('items') <div class="d-block text-danger small mt-2">{{ $message }}</div> @enderror
                         <div class="form-text small mt-1">{{__('Pastikan sekurang-kurangnya satu item peralatan disenaraikan.')}}</div>
                    </div>
                </div>

                {{-- BAHAGIAN 4 | PENGESAHAN PEMOHON --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON') }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-3 text-muted small">
                            {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua maklumat yang diberikan adalah benar dan peralatan yang dipinjam adalah untuk kegunaan rasmi dan akan berada di bawah tanggungjawab serta penyeliaan saya (atau Pegawai Bertanggungjawab yang dinamakan) sepanjang tempoh pinjaman. Saya juga bersetuju untuk mematuhi semua syarat dan peraturan peminjaman yang ditetapkan oleh pihak MOTAC.') }} [cite: 127]
                        </p>
                        <div class="form-check">
                            <input type="checkbox" name="applicant_confirmation" id="applicant_confirmation" value="1" class="form-check-input @error('applicant_confirmation') is-invalid @enderror" required {{ old('applicant_confirmation') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="applicant_confirmation">{{ __('Saya faham dan bersetuju dengan perakuan di atas.') }} <span class="text-danger">*</span></label>
                            @error('applicant_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                         <p class="form-text small mt-2 text-danger fst-italic">
                            {{__('Sila pastikan semua peralatan disemak semasa penerimaan dan pemulangan. Pemohon/Pegawai Bertanggungjawab akan dipertanggungjawabkan ke atas sebarang kerosakan atau kehilangan peralatan yang dipinjam.')}} [cite: 188]
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4 pt-3">
                    <a href="{{ route('loan-applications.index') }}" class="btn btn-secondary btn-lg px-4 me-lg-3 me-2 mb-2 mb-lg-0 d-inline-flex align-items-center">
                        <i class="bi bi-x-circle me-1"></i> {{ __('Batal') }}
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5 mb-2 mb-lg-0">
                        <i class="bi bi-send-check-fill me-2"></i> {{ __('Hantar Permohonan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndexBootstrap = document.querySelectorAll('#equipment-items-container .item-row').length;
        const containerBootstrap = document.getElementById('equipment-items-container');
        const addButton = document.getElementById('add-item-button-bootstrap');

        if (addButton) {
            addButton.onclick = function() {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'g-3', 'align-items-end', 'mb-3', 'border-bottom', 'pb-3', 'pt-2', 'item-row');
                newRow.id = 'item-row-' + itemIndexBootstrap;

                let equipmentOptionsHTML = '<option value="">- {{ __('Pilih Jenis') }} -</option>';
                @foreach(\App\Models\Equipment::getAssetTypeOptions() as $typeKey => $typeLabel)
                    equipmentOptionsHTML += `<option value="{{ $typeKey }}">{{ __($typeLabel) }}</option>`;
                @endforeach

                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label for="items_${itemIndexBootstrap}_equipment_type" class="form-label fw-semibold small">{{ __('Jenis Peralatan') }}<span class="text-danger">*</span></label>
                        <select name="items[${itemIndexBootstrap}][equipment_type]" id="items_${itemIndexBootstrap}_equipment_type" class="form-select form-select-sm" required>
                           ${equipmentOptionsHTML}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="items_${itemIndexBootstrap}_quantity_requested" class="form-label fw-semibold small">{{ __('Kuantiti') }}<span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndexBootstrap}][quantity_requested]" id="items_${itemIndexBootstrap}_quantity_requested" class="form-control form-control-sm" min="1" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="items_${itemIndexBootstrap}_notes" class="form-label fw-semibold small">{{ __('Catatan Tambahan') }}</label>
                        <input type="text" name="items[${itemIndexBootstrap}][notes]" id="items_${itemIndexBootstrap}_notes" class="form-control form-control-sm" placeholder="Cth: Model spesifik, perisian khas">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" onclick="removeItemRowBootstrap(this)" class="btn btn-sm btn-outline-danger w-100 remove-item-button">
                            <i class="bi bi-trash3"></i> {{ __('Buang') }}
                        </button>
                    </div>
                `;
                containerBootstrap.appendChild(newRow);
                itemIndexBootstrap++;
                updateRemoveButtonsBootstrap();
            };
        }

        window.removeItemRowBootstrap = function(button) {
            const currentRows = containerBootstrap.querySelectorAll('.item-row').length;
            if (currentRows > 1) {
                button.closest('.item-row').remove();
            } else {
                alert('{{ __("Sekurang-kurangnya satu item peralatan diperlukan.") }}');
            }
            updateRemoveButtonsBootstrap();
        }

        window.updateRemoveButtonsBootstrap = function() {
            const rows = containerBootstrap.querySelectorAll('.item-row');
            rows.forEach(row => {
                const removeButton = row.querySelector('.remove-item-button');
                if (removeButton) {
                    removeButton.style.display = (rows.length <= 1) ? 'none' : 'inline-block';
                }
            });
        }

        window.toggleResponsibleOfficerSection = function() {
            const checkbox = document.getElementById('applicant_is_responsible_officer_checkbox');
            const section = document.getElementById('responsible-officer-fields');
            const responsibleOfficerIdField = document.getElementById('responsible_officer_id');
            const asterisk = document.getElementById('responsible_officer_id_asterisk');

            if (checkbox.checked) {
                section.style.display = 'none';
                if(responsibleOfficerIdField) {
                    responsibleOfficerIdField.value = ''; // Clear selection
                    responsibleOfficerIdField.removeAttribute('required');
                }
                if(asterisk) asterisk.style.display = 'none';
            } else {
                section.style.display = 'block';
                if(responsibleOfficerIdField) responsibleOfficerIdField.setAttribute('required', 'required');
                if(asterisk) asterisk.style.display = 'inline';
            }
        }

        // Initial calls on page load
        if (containerBootstrap) {
            updateRemoveButtonsBootstrap();
        }
        toggleResponsibleOfficerSection(); // Set initial state for responsible officer section
    });
</script>
@endsection
