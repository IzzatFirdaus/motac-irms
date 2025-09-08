<div>
    @section('title', __('Proses Pengeluaran Peralatan #') . $loanApplication->id)

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-box-arrow-up-right me-2"></i>
            {{ __('Rekod Pengeluaran Peralatan') }}
            <span class="badge bg-label-primary ms-2">@lang('Untuk Permohonan') #{{ $loanApplication->id }}</span>
        </h4>
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Loan Application Details Card --}}
    <div class="card motac-card mb-4">
        <div class="card-header motac-card-header">
            <h5 class="card-title mb-0">@lang('Butiran Permohonan Pinjaman Berkaitan')</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <span class="fw-semibold d-block">@lang('Pemohon'):</span>
                    <span>{{ $loanApplication->user->name }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="fw-semibold d-block">@lang('Tujuan Permohonan'):</span>
                    <span>{{ $loanApplication->purpose }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="fw-semibold d-block">@lang('Tarikh Pinjaman'):</span>
                    <span>{{ $loanApplication->loan_start_date->translatedFormat('d M Y, g:i A') }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="fw-semibold d-block">@lang('Tarikh Dijangka Pulang'):</span>
                    <span>{{ $loanApplication->loan_end_date->translatedFormat('d M Y, g:i A') }}</span>
                </div>
            </div>
            <h6 class="mt-3 mb-2 fw-semibold">@lang('Item Peralatan Diluluskan'):</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small px-3 py-2">#</th>
                            <th class="small px-3 py-2">@lang('Jenis Peralatan')</th>
                            <th class="small px-3 py-2 text-center">@lang('Qty. Lulus')</th>
                            <th class="small px-3 py-2 text-center">@lang('Baki Untuk Dikeluarkan')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanApplication->loanApplicationItems as $item)
                        <tr>
                            <td class="small px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="small px-3 py-2">{{ \App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? $item->equipment_type }}</td>
                            <td class="small px-3 py-2 text-center">{{ $item->quantity_approved }}</td>
                            <td class="small px-3 py-2 text-center fw-bold">{{ ($item->quantity_approved ?? 0) - ($item->quantity_issued ?? 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Issuance Form --}}
    <form wire:submit.prevent="recordIssuance">
        <div class="card motac-card">
            <div class="card-header motac-card-header">
                <h5 class="card-title mb-0">@lang('Rekod Pengeluaran Peralatan Sebenar')</h5>
            </div>
            <div class="card-body">
                @if(empty($issueItems))
                    <div class="alert alert-warning">@lang('Tiada baki peralatan untuk dikeluarkan bagi permohonan ini.')</div>
                @else
                    @foreach ($issueItems as $index => $issueItem)
                        <div wire:key="issue-item-{{ $index }}" class="border rounded p-3 mb-3 {{ $loop->odd ? 'bg-light-subtle' : '' }}">
                            <h6 class="mb-3 fw-semibold border-bottom pb-2">@lang('Item Pengeluaran #'){{ $index + 1 }} : <span class="text-primary">{{ \App\Models\Equipment::getAssetTypeOptions()[$issueItem['equipment_type']] ?? 'N/A' }}</span></h6>

                            <div class="mb-3">
                                <label for="issueItems_{{ $index }}_equipment_id" class="form-label">@lang('Pilih Peralatan Spesifik (Tag ID)') <span class="text-danger">*</span></label>
                                <select wire:model="issueItems.{{ $index }}.equipment_id" id="issueItems_{{ $index }}_equipment_id" class="form-select @error('issueItems.'.$index.'.equipment_id') is-invalid @enderror">
                                    <option value="">-- @lang('Pilih Peralatan') --</option>
                                    {{-- This filters the available equipment to only show matching types --}}
                                    @foreach ($availableEquipment->where('asset_type', $issueItem['equipment_type']) as $equipment)
                                        <option value="{{ $equipment->id }}">
                                            {{ $equipment->name }} (Tag: {{ $equipment->tag_id ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                    @if($availableEquipment->where('asset_type', $issueItem['equipment_type'])->isEmpty())
                                        <option value="" disabled>@lang('Tiada peralatan jenis ini tersedia.')</option>
                                    @endif
                                </select>
                                @error('issueItems.'.$index.'.equipment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="form-label">{{ __('Senarai Semak Aksesori') }}:</label>
                                <div class="row">
                                    @forelse ($allAccessoriesList as $accessory)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="issueItems.{{ $index }}.accessories_checklist" value="{{ $accessory }}" id="accessory_{{ $index }}_{{ Str::slug($accessory) }}" class="form-check-input">
                                                <label class="form-check-label" for="accessory_{{ $index }}_{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12"><p class="small text-muted">@lang('Tiada senarai aksesori dikonfigurasi.')</p></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="receiving_officer_id" class="form-label fw-semibold">@lang('Peralatan Diterima Oleh (Pemohon/Wakil)') <span class="text-danger">*</span></label>
                        <select wire:model="receiving_officer_id" id="receiving_officer_id" class="form-select @error('receiving_officer_id') is-invalid @enderror">
                            <option value="{{ $loanApplication->user_id }}">{{ $loanApplication->user->name }} (@lang('Pemohon'))</option>
                        </select>
                        @error('receiving_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="transaction_date" class="form-label fw-semibold">@lang('Tarikh Pengeluaran') <span class="text-danger">*</span></label>
                        <input type="date" wire:model="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror">
                        @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issue_notes" class="form-label fw-semibold">@lang('Catatan Pengeluaran (Jika Ada)')</label>
                    <textarea wire:model="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror" rows="3" placeholder="@lang('cth: Peralatan dalam keadaan baik semasa dikeluarkan.')"></textarea>
                    @error('issue_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-x-circle me-1"></i>
                    @lang('Batal')
                </a>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="recordIssuance">
                    <span wire:loading wire:target="recordIssuance" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <span wire:loading.remove wire:target="recordIssuance"><i class="bi bi-check-lg me-1"></i></span>
                    @lang('Rekod Pengeluaran')
                </button>
            </div>
        </div>
    </form>
</div>
