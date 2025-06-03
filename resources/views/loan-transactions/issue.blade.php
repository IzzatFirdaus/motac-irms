{{-- resources/views/loan-transactions/issue.blade.php --}}
@extends('layouts.app')

@section('title', __('Rekod Pengeluaran Peralatan untuk Permohonan #') . $loanApplication->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Rekod Pengeluaran Peralatan') }}
                    </h1>
                    <span class="text-muted small">{{__('Untuk Permohonan Pinjaman')}} #{{ $loanApplication->id }}</span>
                </div>

                @include('partials.validation-errors-alt')
                @include('partials.alert-messages')

                {{-- Loan Application Details for Context --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Permohonan Pinjaman Berkaitan') }}</h2>
                    </div>
                    <div class="card-body p-4 small">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Pemohon:') }}</dt>
                            <dd class="col-sm-8">{{ e(optional($loanApplication->user)->name ?? __('N/A')) }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tujuan Permohonan:') }}</dt>
                            <dd class="col-sm-8" style="white-space: pre-wrap;">{{ e($loanApplication->purpose ?? __('N/A')) }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Lokasi Penggunaan:') }}</dt>
                            <dd class="col-sm-8">{{ e($loanApplication->location ?? __('N/A')) }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tarikh Pinjaman:') }}</dt>
                            <dd class="col-sm-8">{{ optional($loanApplication->loan_start_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tarikh Dijangka Pulang:') }}</dt>
                            <dd class="col-sm-8">{{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                        </dl>

                        @if ($loanApplication->applicationItems->isNotEmpty())
                            <h3 class="h6 fw-semibold mt-3 mb-2 pt-2 border-top">{{ __('Item Peralatan Dimohon & Diluluskan:') }}</h3>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-medium ps-2">Bil.</th>
                                            <th class="small text-uppercase text-muted fw-medium">{{ __('Jenis Peralatan') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium text-center">{{ __('Kuantiti Dimohon') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium text-center">{{ __('Kuantiti Diluluskan') }}</th>
                                            <th class="small text-uppercase text-muted fw-medium">{{ __('Catatan Pemohon') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->applicationItems as $item)
                                            <tr>
                                                <td class="small ps-2">{{ $loop->iteration }}.</td>
                                                <td class="small">{{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[$item->equipment_type] ?? Str::title(str_replace('_',' ',$item->equipment_type))) ?? __('N/A') }}</td>
                                                <td class="small text-center">{{ $item->quantity_requested ?? __('N/A') }}</td>
                                                <td class="small text-center fw-bold">{{ $item->quantity_approved ?? __('N/A') }}</td>
                                                <td class="small">{{ e($item->notes ?? '-') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted fst-italic mt-2 small">
                                {{ __('Tiada item peralatan diluluskan untuk permohonan ini.') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Form to Record Issuance --}}
                <form action="{{ route('loan-transactions.storeIssue', $loanApplication) }}" method="POST">
                    @csrf
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Rekod Pengeluaran Peralatan Sebenar') }}</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="equipment_ids" class="form-label fw-semibold">{{ __('Pilih Peralatan untuk Dikeluarkan (dari Inventori)') }}<span class="text-danger">*</span></label>
                                {{-- $availableEquipment (Collection of App\Models\Equipment) should be passed from the controller --}}
                                <select name="equipment_ids[]" id="equipment_ids" class="form-select @error('equipment_ids') is-invalid @enderror @error('equipment_ids.*') is-invalid @enderror" multiple required size="8">
                                    @if(!empty($availableEquipment) && $availableEquipment->count())
                                        @foreach ($availableEquipment as $equipment)
                                            @if(is_object($equipment))
                                            <option value="{{ $equipment->id }}" {{ in_array($equipment->id, old('equipment_ids', [])) ? 'selected' : '' }} data-asset-type="{{ $equipment->asset_type }}">
                                                {{ e($equipment->brand_model_serial) }} (Tag: {{ e($equipment->tag_id ?? __('N/A')) }}) - Jenis: {{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[$equipment->asset_type] ?? $equipment->asset_type) }}
                                            </option>
                                            @endif
                                        @endforeach
                                    @else
                                         <option value="" disabled>{{__('Tiada peralatan tersedia yang sepadan atau semuanya telah dipinjam.')}}</option>
                                    @endif
                                </select>
                                <div class="form-text small text-muted">{{__('Pilih satu atau lebih peralatan. Hanya peralatan yang berstatus "Tersedia" akan disenaraikan. Sila pastikan jenis peralatan sepadan dengan yang diluluskan.')}}</div>
                                @error('equipment_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('equipment_ids.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dikeluarkan:') }}</label>
                                <p class="form-text small mt-0 mb-2 text-muted">
                                    {{ __('Sila tandakan aksesori yang disertakan bersama peralatan yang dipilih.') }}
                                </p>
                                <div class="row">
                                    {{-- $allAccessoriesList (array) should be passed from the controller, e.g., from config('motac.loan_accessories_list') [cite: 348] --}}
                                    @forelse ($allAccessoriesList ?? [] as $accessory)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="accessories[]" value="{{ $accessory }}" id="accessory-{{ Str::slug($accessory) }}" class="form-check-input @error('accessories') is-invalid @enderror" {{ in_array($accessory, old('accessories', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="accessory-{{ Str::slug($accessory) }}">{{ e($accessory) }}</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="small text-muted fst-italic">{{__('Tiada senarai aksesori standard dikonfigurasi.')}}</p>
                                        </div>
                                    @endforelse
                                </div>
                                @error('accessories') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                                @error('accessories.*') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="issue_notes" class="form-label fw-semibold">{{ __('Catatan Pengeluaran (Jika Ada):') }}</label>
                                <textarea name="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror" rows="3" placeholder="Cth: Keadaan fizikal semasa serahan, sebarang perjanjian tambahan.">{{ old('issue_notes') }}</textarea>
                                @error('issue_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <hr class="my-4">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold d-block mb-1">{{ __('Pengeluaran Diproses Oleh (Pegawai BPM):') }}</label>
                                    <p class="form-control-plaintext px-1 py-0 text-dark">{{ Auth::user()->name ?? __('N/A') }}</p>
                                    <input type="hidden" name="issuing_officer_id" value="{{ Auth::id() }}">
                                    @error('issuing_officer_id') <div class="d-block text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label for="receiving_officer_id" class="form-label fw-semibold">{{ __('Peralatan Diterima Oleh (Pemohon/Wakil)') }}<span class="text-danger">*</span></label>
                                    {{-- $loanApplicantAndResponsibleOfficer (Collection of User models) should be passed from controller --}}
                                    <select name="receiving_officer_id" id="receiving_officer_id" class="form-select @error('receiving_officer_id') is-invalid @enderror" required>
                                        <option value="">-- {{__('Pilih Penerima')}} --</option>
                                        @if(!empty($loanApplicantAndResponsibleOfficer) && $loanApplicantAndResponsibleOfficer->count())
                                            @foreach($loanApplicantAndResponsibleOfficer as $officer)
                                                @if(is_object($officer))
                                                <option value="{{ $officer->id }}" {{ old('receiving_officer_id', $loanApplication->user_id) == $officer->id ? 'selected' : '' }}>
                                                    {{ e($officer->name) }} {{ $officer->id == $loanApplication->user_id ? __('(Pemohon)') : (optional($loanApplication->responsibleOfficer)->id == $officer->id ? __('(Peg. Bertanggungjawab)') : '') }}
                                                </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('receiving_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ __('Rekod Pengeluaran') }}
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('loan-applications.show', $loanApplication) }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle me-1"></i>
                        {{ __('Kembali ke Butiran Permohonan') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
<script>
    // JavaScript for filtering equipment_ids based on approved applicationItems can be added here.
    // For now, it assumes the controller pre-filters $availableEquipment or user selects manually.
</script>
@endpush
