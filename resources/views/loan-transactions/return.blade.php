{{-- resources/views/loan-transactions/return.blade.php --}}
@extends('layouts.app')

@section('title', __('Rekod Pulangan Peralatan untuk Transaksi #') . $loanTransaction->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0">{{ __('Rekod Pulangan Peralatan') }}</h1>
                 <span class="text-muted small">{{__('Untuk Permohonan Pinjaman')}} #{{ $loanApplication->id }} / {{__('Transaksi Keluar')}} #{{ $loanTransaction->id }}</span>
            </div>

            {{-- CORRECTED: Use consistent general alert partial --}}
            @include('_partials._alerts.alert-general')
            {{-- If 'partials.validation-errors-alt' was specific, ensure its content is merged or use the general one --}}
            {{-- For simplicity, showing only general errors. If validation-errors-alt is distinct and needed, keep it or merge logic --}}
             @if ($errors->any()) {{-- Explicitly handling $errors if not covered by general alerts for form pages --}}
                <x-alert type="danger" :title="__('Amaran! Sila semak ralat input berikut:')" dismissible="true">
                    <ul class="list-unstyled mb-0 small ps-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif


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
                        <dt class="col-sm-4 text-muted">{{ __('Tarikh Pinjaman:') }}</dt>
                        <dd class="col-sm-8">{{ optional($loanApplication->loan_start_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                        <dt class="col-sm-4 text-muted">{{ __('Tarikh Dijangka Pulang:') }}</dt>
                        <dd class="col-sm-8">{{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                    </dl>

                    @if (!empty($issuedItemsForThisTransaction) && $issuedItemsForThisTransaction->count() > 0)
                        <h3 class="h6 fw-semibold mt-3 mb-2 pt-2 border-top">{{ __('Peralatan Dikeluarkan Dalam Transaksi Ini (#') }}{{ $loanTransaction->id }})</h3>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small text-uppercase text-muted fw-medium ps-2">{{ __('Peralatan (Tag ID)') }}</th>
                                        <th class="small text-uppercase text-muted fw-medium">{{ __('Tarikh Dikeluarkan') }}</th>
                                        <th class="small text-uppercase text-muted fw-medium">{{ __('Aksesori Semasa Dikeluarkan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($issuedItemsForThisTransaction as $item)
                                         @if(is_object($item) && $item->equipment)
                                        <tr>
                                            <td class="small ps-2">
                                                {{ e(optional($item->equipment)->brand_model_serial ?? optional($item->equipment)->tag_id) }}
                                            </td>
                                            <td class="small">{{ optional($loanTransaction->issue_timestamp)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</td>
                                            <td class="small">{{ implode(', ', $item->accessories_checklist_issue ?? ($loanTransaction->accessories_checklist_on_issue ?? [])) ?: '-' }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-warning fst-italic mt-2 small"><i class="bi bi-exclamation-circle me-1"></i>{{ __('Tiada butiran item ditemui untuk transaksi pengeluaran ini.') }}</p>
                    @endif
                </div>
            </div>

            {{-- CORRECTED ROUTE NAME (assuming it's defined in web.php with this full name) --}}
            <form action="{{ route('resource-management.bpm.loan-transactions.storeReturn', $loanTransaction) }}" method="POST">
                @csrf
                <input type="hidden" name="loan_application_id" value="{{ $loanApplication->id }}">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Rekod Pemulangan Peralatan') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="loan_transaction_item_ids" class="form-label fw-semibold">{{ __('Pilih Item Peralatan yang Dipulangkan dari Transaksi Pengeluaran Ini') }}<span class="text-danger">*</span></label>
                            <p class="form-text small mt-0 mb-2 text-muted">{{__('Pilih semua item dari transaksi pengeluaran #')}}{{ $loanTransaction->id }} {{__('yang sedang dipulangkan.')}}</p>
                            <select name="loan_transaction_item_ids[]" id="loan_transaction_item_ids" class="form-select @error('loan_transaction_item_ids') is-invalid @enderror @error('loan_transaction_item_ids.*') is-invalid @enderror" multiple required size="5">
                                @if(!empty($issuedItemsForThisTransaction) && $issuedItemsForThisTransaction->count() > 0)
                                    @foreach ($issuedItemsForThisTransaction as $item)
                                        @if(is_object($item) && $item->equipment)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, old('loan_transaction_item_ids', [])) ? 'selected' : '' }}>
                                            {{ e(optional($item->equipment)->brand_model_serial ?? optional($item->equipment)->tag_id) }}
                                            ({{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[optional($item->equipment)->asset_type] ?? '') }})
                                        </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option disabled>{{__('Tiada item dari transaksi ini untuk dipulangkan.')}}</option>
                                @endif
                            </select>
                            @error('loan_transaction_item_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('loan_transaction_item_ids.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                         <div class="alert alert-info small py-2">
                            <i class="bi bi-info-circle-fill me-1"></i> {{__('Nota: Keadaan setiap item akan dinilai oleh pegawai BPM. Sila nyatakan sebarang kerosakan atau kehilangan dalam catatan di bawah.')}}
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dipulangkan:') }}</label>
                            <p class="form-text small mt-0 mb-2 text-muted">{{ __('Sila tandakan aksesori yang dipulangkan bersama peralatan.') }}</p>
                            <div class="row">
                                @forelse ($allAccessoriesList ?? config('motac.loan_accessories_list', []) as $accessory)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="accessories_on_return[]" value="{{ $accessory }}" id="return-accessory-{{ Str::slug($accessory) }}" class="form-check-input @error('accessories_on_return') is-invalid @enderror" {{ in_array($accessory, old('accessories_on_return', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="return-accessory-{{ Str::slug($accessory) }}">{{ e($accessory) }}</label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12"><p class="small text-muted fst-italic">{{__('Tiada senarai aksesori standard dikonfigurasi.')}}</p></div>
                                @endforelse
                            </div>
                            @error('accessories_on_return') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                             @error('accessories_on_return.*') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="return_notes" class="form-label fw-semibold">{{ __('Catatan Pulangan (cth: kerosakan, item hilang):') }}</label>
                            <textarea name="return_notes" id="return_notes" class="form-control @error('return_notes') is-invalid @enderror" rows="3" placeholder="Nyatakan sebarang kerosakan, kehilangan, atau maklumat tambahan berkaitan pemulangan.">{{ old('return_notes') }}</textarea>
                            @error('return_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold d-block mb-1">{{ __('Pemulangan Diterima Oleh (Pegawai BPM):') }}</label>
                                <p class="form-control-plaintext px-1 py-0 text-dark">{{ Auth::user()->name ?? __('N/A') }}</p>
                                <input type="hidden" name="return_accepting_officer_id" value="{{ Auth::id() }}">
                                @error('return_accepting_officer_id') <div class="d-block text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="returning_officer_id" class="form-label fw-semibold">{{ __('Peralatan Dipulangkan Oleh (Pemohon/Wakil)') }}<span class="text-danger">*</span></label>
                                <select name="returning_officer_id" id="returning_officer_id" class="form-select @error('returning_officer_id') is-invalid @enderror" required>
                                    <option value="">-- {{__('Pilih Pemulang')}} --</option>
                                    @if(!empty($loanApplicantAndResponsibleOfficer) && $loanApplicantAndResponsibleOfficer->count())
                                        @foreach($loanApplicantAndResponsibleOfficer as $officer)
                                            @if(is_object($officer))
                                            <option value="{{ $officer->id }}" {{ old('returning_officer_id', $loanApplication->user_id) == $officer->id ? 'selected' : '' }}>
                                                {{ e($officer->name) }} {{ $officer->id == $loanApplication->user_id ? __('(Pemohon)') : (optional($loanApplication->responsibleOfficer)->id == $officer->id ? __('(Peg. Bertanggungjawab)') : '') }}
                                            </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('returning_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('Rekod Pulangan Peralatan') }}
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                {{-- This route 'loan-applications.show' is global and correct --}}
                <a href="{{ route('loan-applications.show', $loanApplication) }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                     <i class="bi bi-arrow-left-circle me-1"></i>
                    {{ __('Kembali ke Butiran Permohonan') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
