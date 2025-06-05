{{-- resources/views/livewire/resource-management/admin/bpm/process-issuance.blade.php --}}
<div>
    <h3 class="mb-4">{{ __('Rekod Pengeluaran Peralatan untuk Permohonan Pinjaman') }} #{{ $loanApplication->id }}</h3>

    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4"/>
    @endif
    {{-- More specific error display for multiple items --}}
    @if ($errors->any())
        <x-alert type="danger" class="mb-4">
            <p class="fw-semibold">{{ __('Sila perbetulkan ralat berikut:') }}</p>
            <ul class="mt-1 list-unstyled ps-4 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Loan Application Details Card --}}
    <x-card card-title="{{ __('Butiran Permohonan Pinjaman') }}" class="mb-4">
        <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon') }}:</span> {{ $loanApplication->user->name ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan') }}:</span> {{ $loanApplication->purpose ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Lokasi Penggunaan') }}:</span> {{ $loanApplication->location ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Tarikh Pinjaman') }}:</span> {{ $loanApplication->loan_start_date ? $loanApplication->loan_start_date->translatedFormat(config('app.date_format_my', 'd/m/Y')) : __('N/A') }}</p>
        <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang') }}:</span> {{ $loanApplication->loan_end_date ? $loanApplication->loan_end_date->translatedFormat(config('app.date_format_my', 'd/m/Y')) : __('N/A') }}</p>

        @if ($loanApplication->loanApplicationItems->isNotEmpty())
            <h6 class="mt-3 mb-2 fw-semibold">{{ __('Item Peralatan Dimohon & Diluluskan:') }}</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small px-3 py-2">#</th>
                            <th class="small px-3 py-2">{{ __('Jenis Peralatan') }}</th>
                            <th class="small px-3 py-2 text-center">{{ __('Qty. Mohon') }}</th>
                            <th class="small px-3 py-2 text-center">{{ __('Qty. Lulus') }}</th>
                            <th class="small px-3 py-2 text-center">{{ __('Qty. Telah Dikeluarkan') }}</th>
                            <th class="small px-3 py-2 text-center">{{ __('Baki Untuk Dikeluarkan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanApplication->loanApplicationItems as $item)
                        <tr>
                            <td class="small px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="small px-3 py-2">{{ $item->equipment_type ? (\App\Models\Equipment::$ASSET_TYPES_LABELS[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) : __('N/A') }}</td>
                            <td class="small px-3 py-2 text-center">{{ $item->quantity_requested ?? __('N/A') }}</td>
                            <td class="small px-3 py-2 text-center">{{ $item->quantity_approved ?? __('N/A') }}</td>
                            <td class="small px-3 py-2 text-center">{{ $item->quantity_issued ?? 0 }}</td>
                            <td class="small px-3 py-2 text-center fw-bold">{{ ($item->quantity_approved ?? 0) - ($item->quantity_issued ?? 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <form wire:submit.prevent="submitIssue">
        <x-card card-title="{{ __('Rekod Pengeluaran Peralatan Sebenar') }}">

            @foreach ($issueItems as $index => $issueItem)
                <div wire:key="issue-item-{{ $index }}" class="border rounded p-3 mb-3 {{ $loop->odd ? 'bg-light-subtle' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-semibold">{{ __('Item Pengeluaran #') }}{{ $index + 1 }}</h6>
                        @if (count($issueItems) > 1)
                            <button type="button" wire:click="removeIssueItem({{ $index }})" class="btn btn-sm btn-outline-danger" title="{{__('Buang Item Ini')}}">
                                <i class="bi bi-trash"></i> {{__('Buang')}}
                            </button>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="issueItems_{{ $index }}_loan_application_item_id" class="form-label">{{ __('Rujuk Item Permohonan Asal') }} <span class="text-danger">*</span></label>
                            <select wire:model.live="issueItems.{{ $index }}.loan_application_item_id" id="issueItems_{{ $index }}_loan_application_item_id" class="form-select @error('issueItems.'.$index.'.loan_application_item_id') is-invalid @enderror">
                                <option value="">-- {{ __('Pilih Item Asal') }} --</option>
                                @foreach ($loanApplication->loanApplicationItems as $appItem)
                                    @if (($appItem->quantity_approved ?? 0) > ($appItem->quantity_issued ?? 0) || (isset($issueItems[$index]['loan_application_item_id']) && $issueItems[$index]['loan_application_item_id'] == $appItem->id) ) {{-- Allow selecting if already selected or still has balance --}}
                                        <option value="{{ $appItem->id }}">
                                            {{ $appItem->equipment_type ? (\App\Models\Equipment::$ASSET_TYPES_LABELS[$appItem->equipment_type] ?? Str::title(str_replace('_', ' ', $appItem->equipment_type))) : 'N/A' }}
                                            ({{ __('Lulus') }}: {{ $appItem->quantity_approved ?? 0 }}, {{ __('Dikeluarkan') }}: {{ $appItem->quantity_issued ?? 0 }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('issueItems.'.$index.'.loan_application_item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="issueItems_{{ $index }}_equipment_id" class="form-label">{{ __('Peralatan Spesifik (Tag ID)') }} <span class="text-danger">*</span></label>
                            <select wire:model.defer="issueItems.{{ $index }}.equipment_id" id="issueItems_{{ $index }}_equipment_id" class="form-select @error('issueItems.'.$index.'.equipment_id') is-invalid @enderror" @if(empty($issueItems[$index]['equipment_type'])) disabled @endif>
                                <option value="">-- {{ __('Pilih Peralatan') }} --</option>
                                @foreach ($availableEquipment as $equipment)
                                    @if ($equipment->asset_type === ($issueItems[$index]['equipment_type'] ?? null))
                                        <option value="{{ $equipment->id }}">
                                            {{ $equipment->tag_id ?? $equipment->serial_number ?? ('ID:'.$equipment->id) }} - {{ $equipment->brand }} {{ $equipment->model }}
                                        </option>
                                    @endif
                                @endforeach
                                @if(!empty($issueItems[$index]['equipment_type']) && collect($availableEquipment)->where('asset_type', $issueItems[$index]['equipment_type'])->isEmpty())
                                    <option value="" disabled>{{ __('Tiada peralatan jenis ini tersedia.') }}</option>
                                @endif
                            </select>
                            @error('issueItems.'.$index.'.equipment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="issueItems_{{ $index }}_quantity_issued" class="form-label">{{ __('Kuantiti Dikeluarkan') }} <span class="text-danger">*</span></label>
                        {{-- ADJUSTED: max attribute uses ?? 0 --}}
                        <input type="number" wire:model.live="issueItems.{{ $index }}.quantity_issued" id="issueItems_{{ $index }}_quantity_issued" class="form-control @error('issueItems.'.$index.'.quantity_issued') is-invalid @enderror" min="1" max="{{ $issueItems[$index]['max_quantity_issuable'] ?? 0 }}">
                        <div class="form-text">{{__('Baki boleh dikeluarkan untuk item permohonan ini:')}} {{ $issueItems[$index]['max_quantity_issuable'] ?? 0 }}</div>
                        @error('issueItems.'.$index.'.quantity_issued') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Aksesori Dikeluarkan (Item Ini)') }}:</label>
                        <div class="row">
                            @foreach ($allAccessoriesList as $accessory) {{-- for source of $allAccessoriesList --}}
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model.defer="issueItems.{{ $index }}.accessories_checklist_item" value="{{ $accessory }}" id="accessory_{{ $index }}_{{ Str::slug($accessory) }}" class="form-check-input @error('issueItems.'.$index.'.accessories_checklist_item.'.$loop->index) is-invalid @enderror @error('issueItems.'.$index.'.accessories_checklist_item') is-invalid @enderror">
                                        <label class="form-check-label" for="accessory_{{ $index }}_{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('issueItems.'.$index.'.accessories_checklist_item') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                        {{-- Example for wildcard errors on array items, if needed --}}
                        {{-- @foreach($errors->get('issueItems.'.$index.'.accessories_checklist_item.*') as $message)
                            <div class="d-block invalid-feedback">{{ $message[0] }}</div>
                        @endforeach --}}
                    </div>

                    <div class="mb-3">
                        <label for="issueItems_{{ $index }}_issue_item_notes" class="form-label">{{ __('Catatan (Item Ini)') }}:</label>
                        <textarea wire:model.defer="issueItems.{{ $index }}.issue_item_notes" id="issueItems_{{ $index }}_issue_item_notes" class="form-control @error('issueItems.'.$index.'.issue_item_notes') is-invalid @enderror" rows="2"></textarea>
                        @error('issueItems.'.$index.'.issue_item_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endforeach

            <button type="button" wire:click="addIssueItem" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-plus-circle"></i> {{ __('Tambah Item Pengeluaran') }}
            </button>

            <hr class="my-4">

            {{-- CONDITIONAL UI EXAMPLE: Overall Accessories Checklist for the Transaction --}}
            {{-- To enable this:
                 1. Uncomment this HTML block.
                 2. Add `public array $overall_accessories_checklist = [];` to ProcessIssuance.php component.
                 3. Add validation for `overall_accessories_checklist` in rules() method of ProcessIssuance.php.
                 4. Ensure `LoanTransactionService`'s `processNewIssue` and `createTransaction` methods
                    can receive and store this overall list in `LoanTransaction->accessories_checklist_on_issue`.
            --}}
            {{--
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Keseluruhan (Transaksi)') }}:</label>
                <p class="form-text small mt-0 mb-2 text-muted">
                    {{ __('Sila tandakan aksesori umum yang disertakan untuk keseluruhan transaksi ini, jika berbeza dari item spesifik.') }}
                </p>
                <div class="row">
                    @forelse ($allAccessoriesList as $accessoryKey => $accessoryName)
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model.defer="overall_accessories_checklist" value="{{ $accessoryName }}" id="overall_accessory_{{ Str::slug($accessoryName) }}"
                                       class="form-check-input @error('overall_accessories_checklist') is-invalid @enderror @error('overall_accessories_checklist.'.$accessoryKey) is-invalid @enderror">
                                <label class="form-check-label small" for="overall_accessory_{{ Str::slug($accessoryName) }}">{{ $accessoryName }}</label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="small text-muted fst-italic">{{__('Tiada senarai aksesori standard dikonfigurasi.')}}</p>
                        </div>
                    @endforelse
                </div>
                @error('overall_accessories_checklist') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                @foreach($errors->get('overall_accessories_checklist.*') as $message)
                    <div class="d-block invalid-feedback">{{ $message[0] }}</div>
                @endforeach
            </div>
            <hr class="my-4">
            --}}


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="receiving_officer_id" class="form-label fw-semibold">{{ __('Pegawai Penerima (Pengguna/Wakil)') }} <span class="text-danger">*</span></label>
                    <select wire:model.defer="receiving_officer_id" id="receiving_officer_id" class="form-select @error('receiving_officer_id') is-invalid @enderror">
                        <option value="">-- {{ __('Pilih Pegawai') }} --</option>
                        @foreach($users ?? [] as $user)
                            {{-- Pre-select if it's the loan applicant --}}
                            <option value="{{ $user->id }}" {{ $user->id == $loanApplication->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('receiving_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="transaction_date" class="form-label fw-semibold">{{ __('Tarikh Transaksi Pengeluaran') }} <span class="text-danger">*</span></label>
                    {{-- Current implementation uses type="date". If time input is required, change to datetime-local: --}}
                    {{-- <input type="datetime-local" wire:model.defer="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror"> --}}
                    <input type="date" wire:model.defer="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror">
                    @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="issue_notes" class="form-label fw-semibold">{{ __('Catatan Keseluruhan Pengeluaran') }}:</label>
                <textarea wire:model.defer="issue_notes" id="issue_notes" class="form-control @error('issue_notes') is-invalid @enderror" rows="3" placeholder="{{__('cth: Sila jaga peralatan dengan baik.')}}"></textarea>
                @error('issue_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Diproses Oleh (Pegawai Pengeluar)') }}:</label>
                <p class="form-control-plaintext">{{ Auth::user()->name ?? __('N/A') }}</p>
            </div>

        </x-card>

        <div class="text-center mt-4 d-flex justify-content-end">
            <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle me-1"></i>
                {{ __('Batal') }}
            </a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitIssue">
                <span wire:loading wire:target="submitIssue" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span wire:loading.remove wire:target="submitIssue"><i class="bi bi-check-lg me-1"></i></span>
                {{ __('Sahkan & Rekod Pengeluaran Peralatan') }}
            </button>
        </div>
    </form>
</div>
