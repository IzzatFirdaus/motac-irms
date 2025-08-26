{{-- resources/views/loan-transactions/loan-transaction-return.blade.php --}}
{{-- Record equipment return for a loan transaction --}}

@extends('layouts.app')
@section('title', __('Rekod Pulangan Peralatan untuk Transaksi Pengeluaran #') . $loanTransaction->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0">{{ __('Rekod Pulangan Peralatan') }}</h1>
                <span class="text-muted small">{{__('Untuk Permohonan Pinjaman')}} #{{ $loanApplication->id }} / {{__('Transaksi Pengeluaran Asal')}} #{{ $loanTransaction->id }}</span>
            </div>

            @include('_partials._alerts.alert-general')
            @if ($errors->any())
                <x-alert type="danger" :title="__('Amaran! Sila semak ralat input berikut:')" dismissible="true">
                    <ul class="list-unstyled mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Butiran Permohonan & Transaksi Pengeluaran Asal') }}</h2>
                </div>
                <div class="card-body p-4 small">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">{{ __('Pemohon:') }}</dt>
                        <dd class="col-sm-8">{{ e(optional($loanApplication->user)->name ?? __('N/A')) }}</dd>
                        <dt class="col-sm-4 text-muted">{{ __('Tarikh Dijangka Pulang (Permohonan):') }}</dt>
                        <dd class="col-sm-8">{{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                        <dt class="col-sm-4 text-muted">{{ __('Tarikh Transaksi Pengeluaran Asal:') }}</dt>
                        <dd class="col-sm-8">{{ optional($loanTransaction->issue_timestamp ?? $loanTransaction->transaction_date)->translatedFormat('d M Y, H:i A') ?? __('N/A') }}</dd>
                         <dt class="col-sm-4 text-muted">{{ __('Tujuan Permohonan:') }}</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{{ e($loanApplication->purpose ?? __('N/A')) }}</dd>
                    </dl>
                </div>
            </div>

            <form action="{{ route('loan-transactions.return.store', $loanTransaction) }}" method="POST">
                @csrf

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Item Peralatan untuk Dipulangkan') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        @if($issuedItemsForThisTransaction->isEmpty())
                            <p class="text-warning fst-italic"><i class="bi bi-exclamation-circle me-1"></i>{{ __('Tiada item yang boleh dipulangkan dari transaksi pengeluaran ini. Semua item mungkin telah dipulangkan atau tiada item berstatus "issued".') }}</p>
                        @else
                            <p class="form-text small text-muted mb-3">{{__('Sila isikan butiran untuk setiap item yang dipulangkan dari transaksi pengeluaran #')}}{{ $loanTransaction->id }}.</p>
                            @foreach ($issuedItemsForThisTransaction as $index => $issuedItem)
                                <div class="border p-3 mb-3 rounded bg-light shadow-sm">
                                    <h6 class="fw-semibold border-bottom pb-2 mb-3">
                                        {{ __('Item ke-') }}{{ $loop->iteration }}: {{ e(optional($issuedItem->equipment)->brand_model_serial ?? optional($issuedItem->equipment)->tag_id) }}
                                        ({{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[optional($issuedItem->equipment)->asset_type] ?? optional($issuedItem->equipment)->asset_type) }})
                                    </h6>
                                    <input type="hidden" name="items[{{ $issuedItem->id }}][loan_transaction_item_id]" value="{{ $issuedItem->id }}">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="items_{{ $issuedItem->id }}_condition_on_return" class="form-label">{{ __('Keadaan Semasa Pulangan') }}*</label>
                                            <select name="items[{{ $issuedItem->id }}][condition_on_return]" id="items_{{ $issuedItem->id }}_condition_on_return" class="form-select @error('items.'.$issuedItem->id.'.condition_on_return') is-invalid @enderror" required>
                                                <option value="">-- {{ __('Sila Pilih Keadaan') }} --</option>
                                                @foreach (\App\Models\Equipment::getConditionStatusOptions() as $value => $label)
                                                    <option value="{{ $value }}" {{ old('items.'.$issuedItem->id.'.condition_on_return', \App\Models\Equipment::CONDITION_GOOD) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$issuedItem->id.'.condition_on_return') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="items_{{ $issuedItem->id }}_quantity_returned" class="form-label">{{ __('Kuantiti Dipulangkan') }}*</label>
                                            <input type="number" name="items[{{ $issuedItem->id }}][quantity_returned]" id="items_{{ $issuedItem->id }}_quantity_returned" class="form-control @error('items.'.$issuedItem->id.'.quantity_returned') is-invalid @enderror" value="{{ old('items.'.$issuedItem->id.'.quantity_returned', $issuedItem->quantity_transacted) }}" min="1" max="{{ $issuedItem->quantity_transacted }}" required>
                                            <small class="form-text text-muted">{{ __('Asal dikeluarkan: ') }} {{ $issuedItem->quantity_transacted }}</small>
                                            @error('items.'.$issuedItem->id.'.quantity_returned') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="items_{{ $issuedItem->id }}_return_item_notes" class="form-label">{{ __('Catatan Khas untuk Item Ini') }}</label>
                                        <textarea name="items[{{ $issuedItem->id }}][return_item_notes]" id="items_{{ $issuedItem->id }}_return_item_notes" class="form-control @error('items.'.$issuedItem->id.'.return_item_notes') is-invalid @enderror" rows="2" placeholder="Cth: Sedikit calar pada bucu.">{{ old('items.'.$issuedItem->id.'.return_item_notes') }}</textarea>
                                        @error('items.'.$issuedItem->id.'.return_item_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">{{ __('Aksesori Dipulangkan Bersama Item Ini') }}:</label>
                                        @if (!empty($allAccessoriesList))
                                        <div class="row">
                                            @foreach ($allAccessoriesList as $accessory)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input type="checkbox" name="items[{{ $issuedItem->id }}][accessories_checklist_item][]" value="{{ $accessory }}" id="item-{{ $issuedItem->id }}-accessory-{{ Str::slug($accessory) }}"
                                                        class="form-check-input @error('items.'.$issuedItem->id.'.accessories_checklist_item.*') is-invalid @enderror @error('items.'.$issuedItem->id.'.accessories_checklist_item') is-invalid @enderror"
                                                        {{ in_array($accessory, old('items.'.$issuedItem->id.'.accessories_checklist_item', $issuedItem->accessories_checklist_issue ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="item-{{ $issuedItem->id }}-accessory-{{ Str::slug($accessory) }}">{{ e($accessory) }}</label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @error('items.'.$issuedItem->id.'.accessories_checklist_item') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                                        @error('items.'.$issuedItem->id.'.accessories_checklist_item.*') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                                        @else
                                        <p class="small text-muted fst-italic">{{__('Tiada senarai aksesori standard dikonfigurasi.')}}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <hr class="my-4">
                        <div class="row g-3">
                             <div class="col-md-6 mb-3">
                                <label for="returning_officer_id" class="form-label fw-semibold">{{ __('Peralatan Dipulangkan Oleh (Pemohon/Wakil)') }}<span class="text-danger">*</span></label>
                                <select name="returning_officer_id" id="returning_officer_id" class="form-select @error('returning_officer_id') is-invalid @enderror" required>
                                    <option value="">-- {{__('Pilih Pemulang')}} --</option>
                                    @php
                                        $usersForDropdown = $loanApplicantAndResponsibleOfficer ?? collect();
                                        if ($loanApplication->user && !$usersForDropdown->contains('id', $loanApplication->user->id)) {
                                            $usersForDropdown->push($loanApplication->user);
                                        }
                                        if ($loanApplication->responsibleOfficer && !$usersForDropdown->contains('id', $loanApplication->responsibleOfficer->id)) {
                                            $usersForDropdown->push($loanApplication->responsibleOfficer);
                                        }
                                        $usersForDropdown = $usersForDropdown->unique('id')->sortBy('name');
                                    @endphp
                                    @foreach($usersForDropdown as $officer)
                                        @if(is_object($officer))
                                        <option value="{{ $officer->id }}" {{ old('returning_officer_id', $loanApplication->user_id) == $officer->id ? 'selected' : '' }}>
                                            {{ e($officer->name) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('returning_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold d-block mb-1">{{ __('Pemulangan Diterima Oleh (Pegawai BPM):') }}</label>
                                <p class="form-control-plaintext px-1 py-0 text-dark">{{ Auth::user()->name ?? __('N/A') }}</p>
                                {{-- The officer accepting the return will be set to the authenticated user --}}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="return_notes" class="form-label fw-semibold">{{ __('Catatan Keseluruhan Transaksi Pemulangan') }}</label>
                            <textarea name="return_notes" id="return_notes" class="form-control @error('return_notes') is-invalid @enderror" rows="3" placeholder="Catatan umum jika ada.">{{ old('return_notes') }}</textarea>
                            @error('return_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 mb-3">
                    @if($issuedItemsForThisTransaction->isNotEmpty())
                    <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('Sahkan & Rekod Pulangan Peralatan') }}
                    </button>
                    @else
                     <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center px-5" disabled>
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('Rekod Pulangan Peralatan') }}
                    </button>
                    <p class="small text-danger mt-2">{{__('Tidak dapat merekod pemulangan kerana tiada item yang layak dari transaksi ini.')}}</p>
                    @endif
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
