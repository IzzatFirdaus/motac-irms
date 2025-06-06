<div>
    @section('title', __('Proses Pemulangan Peralatan untuk Permohonan #') . $loanApplication->id)

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-box-arrow-in-down-right me-2"></i>
            @lang('Rekod Pemulangan Peralatan')
            <span class="badge bg-label-primary ms-2">@lang('Untuk Permohonan') #{{ $loanApplication->id }}</span>
        </h4>
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Details of the original issuance --}}
    <div class="card motac-card mb-4">
        <div class="card-header motac-card-header">
            <h5 class="card-title mb-0">@lang('Butiran Pengeluaran Asal (Transaksi #:id)', ['id' => $issueTransaction->id])</h5>
        </div>
        <div class="card-body">
            <p><span class="fw-semibold">@lang('Dikeluarkan kepada'):</span> {{ $issueTransaction->receivingOfficer->name ?? 'N/A' }}</p>
            <p><span class="fw-semibold">@lang('Pada'):</span> {{ $issueTransaction->issue_timestamp?->translatedFormat('d M Y, g:i A') ?? 'N/A' }}</p>
        </div>
    </div>

    <form wire:submit.prevent="submitReturn">
        <div class="card motac-card">
            <div class="card-header motac-card-header">
                <h5 class="card-title mb-0">@lang('Pemeriksaan Item Semasa Pemulangan')</h5>
            </div>
            <div class="card-body">
                @error('returnItems') <div class="alert alert-danger">{{ $message }}</div> @enderror

                @forelse($returnItems as $index => $item)
                    <div wire:key="return-item-{{ $item['loan_transaction_item_id'] }}" class="border rounded p-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model.live="returnItems.{{ $index }}.is_returning" id="return_item_{{ $index }}">
                            <label class="form-check-label fw-semibold" for="return_item_{{ $index }}">
                                {{ $item['equipment_name'] }}
                            </label>
                        </div>

                        @if($returnItems[$index]['is_returning'])
                            <div class="ps-4 mt-3 border-start">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="condition_item_{{ $index }}" class="form-label">@lang('Keadaan') <span class="text-danger">*</span></label>
                                        <select wire:model="returnItems.{{ $index }}.condition_on_return" id="condition_item_{{ $index }}" class="form-select @error('returnItems.'.$index.'.condition_on_return') is-invalid @enderror">
                                            @foreach($conditionOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('returnItems.'.$index.'.condition_on_return') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="notes_item_{{ $index }}" class="form-label">@lang('Catatan')</label>
                                        <input type="text" wire:model="returnItems.{{ $index }}.return_item_notes" id="notes_item_{{ $index }}" class="form-control" placeholder="@lang('cth: Terdapat calar kecil')">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info">@lang('Semua peralatan dari transaksi ini telah dipulangkan.')</div>
                @endforelse

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="returning_officer_id" class="form-label fw-semibold">@lang('Peralatan Dipulangkan Oleh') <span class="text-danger">*</span></label>
                        <select wire:model="returning_officer_id" id="returning_officer_id" class="form-select @error('returning_officer_id') is-invalid @enderror">
                             @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                         @error('returning_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="transaction_date" class="form-label fw-semibold">@lang('Tarikh Pemulangan') <span class="text-danger">*</span></label>
                        <input type="date" wire:model="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror">
                        @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="mb-3">
                    <label for="return_notes" class="form-label fw-semibold">@lang('Catatan Keseluruhan Pemulangan')</label>
                    <textarea wire:model="return_notes" id="return_notes" class="form-control" rows="3"></textarea>
                </div>
                 <p class="form-text">
                    @lang('Diterima Oleh (Pegawai BPM)'): {{ Auth::user()->name }}
                </p>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary me-2">@lang('Batal')</a>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-check-lg me-1"></i> @lang('Sahkan Pemulangan')</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> @lang('Memproses...')</span>
                </button>
            </div>
        </div>
    </form>
</div>
