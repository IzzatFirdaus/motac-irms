<<<<<<< HEAD
@extends('layouts.app')

@section('title', __('Keluarkan Peralatan untuk Pinjaman #:app_id', ['app_id' => $loanApplication->id]))

@section('content')
    {{-- UPDATED: Removed the 'container' class to allow for a full-width layout controlled by the component --}}
    <div class="py-4">
        {{--
            This single line now loads the entire issuance form and its logic from the ProcessIssuance component.
            We pass the loan application ID, and the component will handle the rest.
        --}}
        @livewire('resource-management.admin.bpm.process-issuance', ['loanApplicationId' => $loanApplication->id])
=======
{{-- resources/views/loan-transactions/issue.blade.php --}}
@extends('layouts.app')

@section('title', __('Rekod Pengeluaran Peralatan'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">

                <h2 class="h2 fw-bold text-dark mb-4">{{ __('Rekod Pengeluaran Peralatan untuk Permohonan Pinjaman') }}
                    #{{ $loanApplication->id }}</h2>

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

                {{-- Loan Application Details for Context --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="h5 card-title mb-0">{{ __('Butiran Permohonan Pinjaman') }}</h3>
                    </div>
                    <div class="card-body small">
                        <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon:') }}</span>
                            {{ $loanApplication->user->name ?? 'N/A' }}</p>
                        <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan:') }}</span>
                            {{ $loanApplication->purpose ?? 'N/A' }}</p>
                        <p class="mb-1"><span class="fw-semibold">{{ __('Lokasi Penggunaan:') }}</span>
                            {{ $loanApplication->location ?? 'N/A' }}</p>
                        <p class="mb-1"><span class="fw-semibold">{{ __('Tarikh Pinjaman:') }}</span>
                            {{ $loanApplication->loan_start_date?->format('d/m/Y') ?? 'N/A' }}</p>
                        <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang:') }}</span>
                            {{ $loanApplication->loan_end_date?->format('d/m/Y') ?? 'N/A' }}</p>

                        @if ($loanApplication->items->isNotEmpty())
                            <h4 class="h6 fw-semibold mt-3 mb-2">{{ __('Item Peralatan Dimohon:') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small">{{ __('Bil.') }}</th>
                                            <th class="small">{{ __('Jenis Peralatan') }}</th>
                                            <th class="small text-center">{{ __('Kuantiti Dimohon') }}</th>
                                            <th class="small text-center">{{ __('Kuantiti Diluluskan') }}</th>
                                            <th class="small">{{ __('Catatan') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->items as $item)
                                            <tr>
                                                <td class="small">{{ $loop->iteration }}</td>
                                                <td class="small">{{ $item->equipment_type ?? 'N/A' }}</td>
                                                <td class="small text-center">{{ $item->quantity_requested ?? 'N/A' }}</td>
                                                <td class="small text-center">{{ $item->quantity_approved ?? 'N/A' }}</td>
                                                <td class="small">{{ $item->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted fst-italic mt-2 small">
                                {{ __('Tiada item peralatan dimohon untuk permohonan ini.') }}</p>
                        @endif
                    </div>
                </div>

                <form action="{{ route('loan-transactions.storeIssue', $loanApplication) }}" method="POST">
                    @csrf
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="h5 card-title mb-0">{{ __('Rekod Pengeluaran Peralatan') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="equipment_ids"
                                    class="form-label fw-semibold">{{ __('Pilih Peralatan untuk Dikeluarkan') }}<span
                                        class="text-danger">*</span></label>
                                <select name="equipment_ids[]" id="equipment_ids"
                                    class="form-select @error('equipment_ids') is-invalid @enderror @error('equipment_ids.*') is-invalid @enderror"
                                    multiple required size="5">
                                    {{-- <option value="">- {{ __('Pilih Peralatan') }} -</option> --}}
                                    @foreach ($availableEquipment as $equipment)
                                        <option value="{{ $equipment->id }}"
                                            {{ in_array($equipment->id, old('equipment_ids', [])) ? 'selected' : '' }}>
                                            {{ $equipment->brand }} {{ $equipment->model }} (Tag:
                                            {{ $equipment->tag_id ?? 'N/A' }}) - {{ $equipment->asset_type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('equipment_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('equipment_ids.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dikeluarkan:') }}</label>
                                <p class="form-text small mt-0 mb-2">
                                    {{ __('Sila tandakan aksesori yang disertakan bersama peralatan yang dipilih.') }}</p>
                                <div class="row">
                                    @foreach ($allAccessoriesList as $accessory)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" name="accessories[]" value="{{ $accessory }}"
                                                    id="accessory-{{ Str::slug($accessory) }}"
                                                    class="form-check-input @error('accessories') is-invalid @enderror"
                                                    {{ in_array($accessory, old('accessories', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small"
                                                    for="accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('accessories')
                                    <div class="d-block invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('accessories.*')
                                    <div class="d-block invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="issue_notes"
                                    class="form-label fw-semibold">{{ __('Catatan Pengeluaran:') }}</label>
                                <textarea name="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror"
                                    rows="3">{{ old('issue_notes') }}</textarea>
                                @error('issue_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block mb-1">{{ __('Diproses Oleh:') }}</label>
                                <p class="form-control-plaintext px-1 py-0">{{ Auth::user()->name ?? 'N/A' }}</p>
                                <input type="hidden" name="issuing_officer_id" value="{{ Auth::id() }}">
                                @error('issuing_officer_id')
                                    <div class="d-block text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ __('Rekod Pengeluaran Peralatan') }}
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('loan-applications.show', $loanApplication) }}"
                        class="btn btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        {{ __('Kembali ke Butiran Permohonan') }}
                    </a>
                </div>
            </div>
        </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
    </div>
@endsection
