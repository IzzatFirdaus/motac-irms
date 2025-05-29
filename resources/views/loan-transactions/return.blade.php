{{-- resources/views/loan-transactions/return.blade.php --}}
@extends('layouts.app')

@section('title', __('Rekod Pulangan Peralatan'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <h2 class="h2 fw-bold text-dark mb-4">{{ __('Rekod Pulangan Peralatan untuk Permohonan Pinjaman') }} #{{ $loanApplication->id }}</h2>

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

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="h5 card-title mb-0">{{ __('Butiran Permohonan Pinjaman') }}</h3>
                </div>
                <div class="card-body small">
                    <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon:') }}</span> {{ $loanApplication->user->name ?? 'N/A' }}</p>
                    <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan:') }}</span> {{ $loanApplication->purpose ?? 'N/A' }}</p>
                    <p class="mb-1"><span class="fw-semibold">{{ __('Tarikh Pinjaman:') }}</span> {{ $loanApplication->loan_start_date?->format('d/m/Y') ?? 'N/A' }}</p>
                    <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang:') }}</span> {{ $loanApplication->loan_end_date?->format('d/m/Y') ?? 'N/A' }}</p>

                    @if ($issuedTransactions->isNotEmpty())
                        <h4 class="h6 fw-semibold mt-3 mb-2">{{ __('Peralatan Sedang Dipinjam Untuk Permohonan Ini:') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small">{{ __('Peralatan (Tag ID)') }}</th>
                                        <th class="small">{{ __('Tarikh Dikeluarkan') }}</th>
                                        <th class="small">{{ __('Aksesori Dikeluarkan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($issuedTransactions as $transaction)
                                        <tr>
                                            <td class="small">
                                                {{ $transaction->equipment->brand ?? '' }} {{ $transaction->equipment->model ?? '' }}
                                                (Tag: {{ $transaction->equipment->tag_id ?? 'N/A' }})
                                            </td>
                                            <td class="small">{{ $transaction->issue_timestamp?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                            <td class="small">{{ implode(', ', json_decode($transaction->accessories_checklist_on_issue, true) ?? []) ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted fst-italic mt-2 small">{{ __('Tiada peralatan sedang dipinjam untuk permohonan ini.') }}</p>
                    @endif
                </div>
            </div>

            <form action="{{ route('loan-transactions.storeReturn', $loanApplication) }}" method="POST">
                @csrf
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="h5 card-title mb-0">{{ __('Rekod Pulangan Peralatan') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="transaction_ids" class="form-label fw-semibold">{{ __('Pilih Peralatan yang Dipulangkan') }}<span class="text-danger">*</span></label>
                            <select name="transaction_ids[]" id="transaction_ids" class="form-select @error('transaction_ids') is-invalid @enderror @error('transaction_ids.*') is-invalid @enderror" multiple required size="5">
                                @foreach ($issuedTransactions as $transaction)
                                    <option value="{{ $transaction->id }}" {{ in_array($transaction->id, old('transaction_ids', [])) ? 'selected' : '' }}>
                                        {{ $transaction->equipment->brand ?? '' }} {{ $transaction->equipment->model ?? '' }}
                                        (Tag: {{ $transaction->equipment->tag_id ?? 'N/A' }})
                                        - Dikeluarkan: {{ $transaction->issue_timestamp?->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('transaction_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('transaction_ids.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dipulangkan:') }}</label>
                            <p class="form-text small mt-0 mb-2">{{ __('Sila tandakan aksesori yang dipulangkan bersama peralatan yang dipilih.') }}</p>
                            <div class="row">
                                @foreach ($allAccessoriesList as $accessory)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="accessories_on_return[]" value="{{ $accessory }}" id="return-accessory-{{ Str::slug($accessory) }}" class="form-check-input @error('accessories_on_return') is-invalid @enderror" {{ in_array($accessory, old('accessories_on_return', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="return-accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('accessories_on_return') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                             @error('accessories_on_return.*') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="return_notes" class="form-label fw-semibold">{{ __('Catatan Pulangan (cth: kerosakan, item hilang):') }}</label>
                            <textarea name="return_notes" id="return_notes" class="form-control @error('return_notes') is-invalid @enderror" rows="3">{{ old('return_notes') }}</textarea>
                            @error('return_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block mb-1">{{ __('Diterima Oleh:') }}</label>
                            <p class="form-control-plaintext px-1 py-0">{{ Auth::user()->name ?? 'N/A' }}</p>
                            <input type="hidden" name="return_accepting_officer_id" value="{{ Auth::id() }}">
                             @error('return_accepting_officer_id') <div class="d-block text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('Rekod Pulangan Peralatan') }}
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('loan-applications.show', $loanApplication) }}" class="btn btn-secondary d-inline-flex align-items-center">
                     <i class="bi bi-arrow-left me-2"></i>
                    {{ __('Kembali ke Butiran Permohonan') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
