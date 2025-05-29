@extends('layouts.app') {{-- Ensure layouts.app is Bootstrap-compatible --}}

@section('title', __('Borang Permohonan Pinjaman Peralatan ICT'))

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8"> {{-- Adjust column width as needed --}}

            <h2 class="h2 fw-bold mb-4 text-dark">{{ __('Borang Permohonan Pinjaman Peralatan ICT') }}</h2>

            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">{{ __('Ralat Pengesahan:') }}</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Display session messages --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('loan-applications.store') }}" method="POST">
                @csrf

                {{-- BAHAGIAN 1 | MAKLUMAT PEMOHON --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title h5 mb-0">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">{{ __('Nama Penuh:') }}</label>
                            <p class="form-control-plaintext bg-light px-2 rounded">{{ Auth::user()->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">{{ __('Jawatan & Gred:') }}</label>
                            <p class="form-control-plaintext bg-light px-2 rounded">{{ Auth::user()->position->name ?? 'N/A' }} & {{ Auth::user()->grade->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">{{ __('Bahagian/Unit:') }}</label>
                            <p class="form-control-plaintext bg-light px-2 rounded">{{ Auth::user()->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">{{ __('No. Telefon:') }}</label>
                            <p class="form-control-plaintext bg-light px-2 rounded">{{ Auth::user()->mobile_number ?? (Auth::user()->phone_number ?? 'N/A') }}</p>
                        </div>
                        <hr class="my-3">
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-bold">{{ __('Tujuan Permohonan') }}<span class="text-danger">*</span></label>
                            <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3" required>{{ old('purpose') }}</textarea>
                            @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label fw-bold">{{ __('Lokasi Penggunaan Peralatan') }}<span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" required value="{{ old('location') }}">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="loan_start_date" class="form-label fw-bold">{{ __('Tarikh Pinjaman') }}<span class="text-danger">*</span></label>
                                <input type="date" name="loan_start_date" id="loan_start_date" class="form-control @error('loan_start_date') is-invalid @enderror" required value="{{ old('loan_start_date') }}">
                                @error('loan_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="loan_end_date" class="form-label fw-bold">{{ __('Tarikh Dijangka Pulang') }}<span class="text-danger">*</span></label>
                                <input type="date" name="loan_end_date" id="loan_end_date" class="form-control @error('loan_end_date') is-invalid @enderror" required value="{{ old('loan_end_date') }}">
                                @error('loan_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title h5 mb-0">{{ __('BAHAGIAN 2 | MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_applicant_responsible" id="is_applicant_responsible" value="1" class="form-check-input" {{ old('is_applicant_responsible', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_applicant_responsible">{{ __('Sila tandakan jika Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                            @error('is_applicant_responsible') <div class="d-block text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <p class="form-text small mb-3">{{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.') }}</p>
                        <div class="mb-3">
                            <label for="responsible_officer_id" class="form-label fw-bold">{{ __('Nama Penuh Pegawai Bertanggungjawab') }}</label>
                            <select name="responsible_officer_id" id="responsible_officer_id" class="form-select @error('responsible_officer_id') is-invalid @enderror">
                                <option value="">- {{ __('Pilih Pegawai') }} -</option>
                                @php $responsibleOfficers = $responsibleOfficers ?? []; @endphp
                                @foreach ($responsibleOfficers as $officer)
                                    <option value="{{ $officer->id }}" {{ old('responsible_officer_id') == $officer->id ? 'selected' : '' }}>
                                        {{ $officer->name }} ({{ $officer->position->name ?? 'N/A' }} - {{ $officer->grade->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('responsible_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- BAHAGIAN 3 | MAKLUMAT PERALATAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title h5 mb-0">{{ __('BAHAGIAN 3 | MAKLUMAT PERALATAN') }}</h4>
                    </div>
                    <div class="card-body">
                        <p class="form-text small mb-3">{{ __('Sila senaraikan peralatan ICT yang diperlukan.') }}</p>
                        <div id="equipment-items-container">
                            @php
                                $initialItems = old('items', [['equipment_type' => '', 'quantity_requested' => '1', 'notes' => '']]);
                                if (empty($initialItems)) {
                                    $initialItems = [['equipment_type' => '', 'quantity_requested' => '1', 'notes' => '']];
                                }
                            @endphp
                            @foreach ($initialItems as $index => $item)
                            <div class="row g-3 align-items-end mb-3 border-bottom pb-3" id="item-row-{{ $index }}">
                                <div class="col-md-4">
                                    <label for="equipment_type_{{ $index }}" class="form-label fw-bold">{{ __('Jenis Peralatan') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="items[{{ $index }}][equipment_type]" id="equipment_type_{{ $index }}" class="form-control @error('items.'.$index.'.equipment_type') is-invalid @enderror" required value="{{ $item['equipment_type'] ?? '' }}">
                                    @error('items.'.$index.'.equipment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="quantity_requested_{{ $index }}" class="form-label fw-bold">{{ __('Kuantiti') }}<span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][quantity_requested]" id="quantity_requested_{{ $index }}" class="form-control @error('items.'.$index.'.quantity_requested') is-invalid @enderror" min="1" required value="{{ $item['quantity_requested'] ?? '1' }}">
                                    @error('items.'.$index.'.quantity_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="item_notes_{{ $index }}" class="form-label fw-bold">{{ __('Catatan') }}</label>
                                    <input type="text" name="items[{{ $index }}][notes]" id="item_notes_{{ $index }}" class="form-control @error('items.'.$index.'.notes') is-invalid @enderror" value="{{ $item['notes'] ?? '' }}">
                                    @error('items.'.$index.'.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    @if ($loop->index > 0 || count($initialItems) > 1)
                                    <button type="button" onclick="removeItemRowBootstrap({{ $index }})" class="btn btn-sm btn-outline-danger w-100">
                                        {{ __('Buang') }}
                                    </button>
                                    @else
                                    <button type="button" onclick="removeItemRowBootstrap({{ $index }})" class="btn btn-sm btn-outline-danger w-100" style="display:none;">
                                        {{ __('Buang') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-item-button-bootstrap" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('Tambah Item Peralatan') }}
                        </button>
                        @error('items') <div class="d-block text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- BAHAGIAN 4 | PENGESAHAN PEMOHON --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title h5 mb-0">{{ __('BAHAGIAN 4 | PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)') }}</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-3 text-muted">{{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut;') }}</p>
                        <div class="form-check">
                            <input type="checkbox" name="applicant_confirmation" id="applicant_confirmation" value="1" class="form-check-input @error('applicant_confirmation') is-invalid @enderror" required {{ old('applicant_confirmation') ? 'checked' : '' }}>
                            <label class="form-check-label" for="applicant_confirmation">{{ __('Saya faham dan bersetuju dengan syarat-syarat peminjaman peralatan ICT.') }} <span class="text-danger">*</span></label>
                            @error('applicant_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-4">
                        <i class="bi bi-check-lg me-2"></i> {{ __('Hantar Permohonan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JavaScript for adding/removing item rows (Bootstrap version)
    let itemIndexBootstrap = {{ count(old('items', [['']])) }};
    const containerBootstrap = document.getElementById('equipment-items-container');

    document.getElementById('add-item-button-bootstrap').onclick = function() {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-3', 'align-items-end', 'mb-3', 'border-bottom', 'pb-3');
        newRow.id = 'item-row-' + itemIndexBootstrap;
        newRow.innerHTML = `
            <div class="col-md-4">
                <label for="equipment_type_${itemIndexBootstrap}" class="form-label fw-bold">{{ __('Jenis Peralatan') }}<span class="text-danger">*</span></label>
                <input type="text" name="items[${itemIndexBootstrap}][equipment_type]" id="equipment_type_${itemIndexBootstrap}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label for="quantity_requested_${itemIndexBootstrap}" class="form-label fw-bold">{{ __('Kuantiti') }}<span class="text-danger">*</span></label>
                <input type="number" name="items[${itemIndexBootstrap}][quantity_requested]" id="quantity_requested_${itemIndexBootstrap}" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-4">
                <label for="item_notes_${itemIndexBootstrap}" class="form-label fw-bold">{{ __('Catatan') }}</label>
                <input type="text" name="items[${itemIndexBootstrap}][notes]" id="item_notes_${itemIndexBootstrap}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="button" onclick="removeItemRowBootstrap(${itemIndexBootstrap})" class="btn btn-sm btn-outline-danger w-100">
                    {{ __('Buang') }}
                </button>
            </div>
        `;
        containerBootstrap.appendChild(newRow);
        itemIndexBootstrap++;
        updateRemoveButtonsBootstrap();
    };

    function removeItemRowBootstrap(index) {
        const row = document.getElementById('item-row-' + index);
        if (row) {
            const currentRows = containerBootstrap.querySelectorAll('.row.g-3.align-items-end.mb-3').length;
            if (currentRows > 1) {
                row.remove();
            } else {
                alert('{{ __("Sekurang-kurangnya satu item peralatan diperlukan.") }}');
            }
            updateRemoveButtonsBootstrap();
        }
    }

    function updateRemoveButtonsBootstrap() {
        const rows = containerBootstrap.querySelectorAll('.row.g-3.align-items-end.mb-3');
        rows.forEach(row => {
            const removeButton = row.querySelector('button[onclick^="removeItemRowBootstrap"]');
            if (removeButton) {
                removeButton.style.display = (rows.length <= 1) ? 'none' : 'inline-block';
            }
        });
    }
    // Initial call on page load
    if(containerBootstrap) { // ensure container exists before trying to query it
        updateRemoveButtonsBootstrap();
    }
</script>
@endsection
