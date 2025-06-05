{{-- resources/views/livewire/resource-management/admin/bpm/process-return.blade.php --}}
<div>
    <h3 class="mb-1">{{ __('Rekod Pulangan Peralatan untuk Permohonan Pinjaman') }} #{{ $loanApplication->id }}</h3>
    @if ($issueTransaction)
        <p class="text-muted mb-4 small">{{ __('Memproses pemulangan untuk Transaksi Pengeluaran Asal #') }}{{ $issueTransaction->id }}</p>
    @else
        <x-alert type="warning" class="mb-4" :message="__('Amaran: Transaksi pengeluaran asal tidak dapat dikenal pasti. Sila pastikan permohonan ini mempunyai item yang telah dikeluarkan.')" />
    @endif


    @if (session()->has('success'))
        <x-alert type="success" :message="session('success')" class="mb-4" />
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4" />
    @endif
    @if ($errors->any())
        <x-alert type="danger" class="mb-4">
            <p class="fw-semibold">{{ __('Sila perbetulkan ralat berikut:') }}</p>
            <ul class="mt-1 list-unstyled ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Loan Application Details Card --}}
    <x-card card-title="{{ __('Butiran Permohonan Pinjaman') }}" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon') }}:</span>
                    {{ $loanApplication->user->name ?? __('N/A') }}</p>
                <p class="mb-1"><span class="fw-semibold">{{ __('No. Permohonan') }}:</span> {{ $loanApplication->id }}</p>
                 @if ($loanApplication->responsibleOfficer)
                <p class="mb-1"><span class="fw-semibold">{{ __('Pegawai Bertanggungjawab') }}:</span>
                    {{ $loanApplication->responsibleOfficer->name ?? __('N/A') }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan') }}:</span>
                    {{ $loanApplication->purpose ?? __('N/A') }}</p>
                <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang') }}:</span>
                    {{ $loanApplication->loan_end_date ? $loanApplication->loan_end_date->translatedFormat(config('app.date_format_my', 'd M Y')) : __('N/A') }}
                </p>
            </div>
        </div>

        @if ($issueTransaction && $itemsAvailableForReturn->isNotEmpty())
            <h6 class="mt-3 mb-2 fw-semibold">{{ __('Peralatan Yang Telah Dikeluarkan (Transaksi Pengeluaran Asal #') }}{{ $issueTransaction->id }}):</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small px-3 py-2">{{ __('Peralatan (Tag ID)') }}</th>
                            <th class="small px-3 py-2">{{ __('Tarikh Dikeluarkan') }}</th>
                            <th class="small px-3 py-2">{{ __('Aksesori Semasa Dikeluarkan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itemsAvailableForReturn as $issuedItem)
                            @if($issuedItem->loan_transaction_id == $issueTransaction->id) {{-- Only show items from the current issue transaction being processed --}}
                            <tr>
                                <td class="small px-3 py-2">
                                    {{ $issuedItem->equipment->brand_model_serial ?? ($issuedItem->equipment->tag_id ?? __('N/A')) }}
                                </td>
                                <td class="small px-3 py-2">
                                    {{ $issueTransaction->issue_timestamp?->translatedFormat(config('app.datetime_format_my')) ?? ($issueTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my')) ?? __('N/A')) }}
                                </td>
                                <td class="small px-3 py-2">
                                    {{ $issuedItem->accessories_checklist_issue ? implode(', ', $issuedItem->accessories_checklist_issue) : '-' }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted fst-italic mt-3">{{ __('Tiada butiran peralatan ditemui untuk transaksi pengeluaran ini atau tiada item yang sedang berstatus "issued".') }}</p>
        @endif
    </x-card>

    {{-- Show form only if there's an issue transaction identified --}}
    @if ($issueTransaction)
        <form wire:submit.prevent="submitReturn">
            <x-card card-title="{{ __('Rekod Pemulangan Peralatan Sebenar') }}">
                <div class="mb-3">
                    <label for="selectedTransactionItemIds"
                        class="form-label fw-semibold">{{ __('Pilih Peralatan yang Dipulangkan dari Transaksi #')}}{{ $issueTransaction->id }}*:</label>
                    <p class="form-text small mt-0 mb-2 text-muted">{{__('Pilih satu atau lebih item yang sedang dipulangkan dari senarai di bawah. Hanya item yang berstatus "issued" dari transaksi pengeluaran ini akan disenaraikan.')}}</p>
                    <select wire:model.live="selectedTransactionItemIds" id="selectedTransactionItemIds" {{-- Changed to .live for dynamic updates --}}
                        class="form-select @error('selectedTransactionItemIds') is-invalid @enderror @error('selectedTransactionItemIds.*') is-invalid @enderror"
                        multiple required size="{{ max(5, $itemsAvailableForReturn->where('loan_transaction_id', $issueTransaction->id)->where('status', \App\Models\LoanTransactionItem::STATUS_ITEM_ISSUED)->count() + 1) }}">
                        @php
                            $itemsFromThisIssueTx = $itemsAvailableForReturn->where('loan_transaction_id', $issueTransaction->id)
                                                    ->where('status', \App\Models\LoanTransactionItem::STATUS_ITEM_ISSUED);
                        @endphp
                        @forelse ($itemsFromThisIssueTx as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->equipment->brand_model_serial ?? ($item->equipment->tag_id ?? __('N/A')) }}
                            </option>
                        @empty
                            <option value="" disabled>{{ __('Tiada peralatan dari transaksi ini untuk dipulangkan atau semuanya telah direkodkan pemulangan.') }}</option>
                        @endforelse
                    </select>
                    @error('selectedTransactionItemIds')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('selectedTransactionItemIds.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Dynamic inputs for each selected item --}}
                @if (!empty($selectedTransactionItemIds))
                    <h6 class="mt-4 mb-3 fw-semibold">{{ __('Butiran untuk Peralatan Dipulangkan:') }}</h6>
                    @foreach ($selectedTransactionItemIds as $selectedItemId)
                        @php
                            // Find the item from the $itemsAvailableForReturn collection to get its details
                            // Ensure $selectedItemId is an integer if keys are integers
                            $selectedItem = $itemsAvailableForReturn->firstWhere('id', (int)$selectedItemId);
                        @endphp
                        @if ($selectedItem)
                            <div class="border p-3 mb-3 rounded bg-light">
                                <p class="fw-bold mb-2">{{ __('Peralatan') }}: {{ $selectedItem->equipment->brand_model_serial ?? ($selectedItem->equipment->tag_id ?? __('N/A')) }}</p>

                                {{-- Condition --}}
                                <div class="mb-3">
                                    <label for="condition_item_{{ $selectedItemId }}" class="form-label">{{ __('Keadaan Semasa Pulangan') }}*:</label>
                                    <select wire:model.defer="itemConditions.{{ $selectedItemId }}" id="condition_item_{{ $selectedItemId }}" class="form-select @error('itemConditions.'.$selectedItemId) is-invalid @enderror" required>
                                        <option value="">-- {{ __('Sila Pilih Keadaan') }} --</option>
                                        @foreach (\App\Models\Equipment::getConditionStatusOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('itemConditions.'.$selectedItemId) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Item-Specific Notes --}}
                                <div class="mb-3">
                                    <label for="notes_item_{{ $selectedItemId }}" class="form-label">{{ __('Catatan untuk Item Ini') }}:</label>
                                    <textarea wire:model.defer="itemReturnNotes.{{ $selectedItemId }}" id="notes_item_{{ $selectedItemId }}" class="form-control @error('itemReturnNotes.'.$selectedItemId) is-invalid @enderror" rows="2" placeholder="Cth: Sedikit calar pada bucu."></textarea>
                                    @error('itemReturnNotes.'.$selectedItemId) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Item-Specific Accessories --}}
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Aksesori Dipulangkan Bersama Item Ini') }}:</label>
                                    @if (!empty($allAccessoriesList))
                                    <div class="row">
                                        @foreach ($allAccessoriesList as $accessory)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model.defer="itemSpecificAccessories.{{ $selectedItemId }}" value="{{ $accessory }}" id="item-{{ $selectedItemId }}-accessory-{{ Str::slug($accessory) }}" class="form-check-input @error('itemSpecificAccessories.'.$selectedItemId.'.*') is-invalid @enderror">
                                                <label class="form-check-label small" for="item-{{ $selectedItemId }}-accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('itemSpecificAccessories.'.$selectedItemId.'.*') <div class="d-block invalid-feedback">{{ $message }}</div> @enderror
                                    @else
                                    <p class="small text-muted fst-italic">{{__('Tiada senarai aksesori standard dikonfigurasi.')}}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
                <hr>
                {{-- Returning Officer --}}
                 <div class="mb-3">
                    <label for="returningOfficerId" class="form-label fw-semibold">{{ __('Peralatan Dipulangkan Oleh (Nama Staf)') }}*:</label>
                    <select wire:model.defer="returningOfficerId" id="returningOfficerId" class="form-select @error('returningOfficerId') is-invalid @enderror" required>
                        <option value="">-- {{ __('Sila Pilih Staf') }} --</option>
                        @php
                            // Prepare a list of users relevant for returning: applicant and responsible officer
                            // You might want to expand this list (e.g., all users from applicant's department, or any user)
                            $relevantUsers = collect();
                            if ($loanApplication->user) $relevantUsers->push($loanApplication->user);
                            if ($loanApplication->responsibleOfficer) $relevantUsers->push($loanApplication->responsibleOfficer);
                            $relevantUsers = $relevantUsers->unique('id');
                        @endphp
                        @foreach ($relevantUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                        {{-- Consider adding an option for "Other User" if the list is not exhaustive,
                             which might then reveal a text input for name, or a broader user search --}}
                    </select>
                    @error('returningOfficerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                {{-- Overall Return Notes --}}
                <div class="mb-3">
                    <label for="return_notes" class="form-label fw-semibold">{{ __('Catatan Keseluruhan Pemulangan (Cth: kerosakan, item hilang yang tidak dinyatakan per item)') }}:</label>
                    <textarea wire:model.defer="return_notes" id="return_notes" class="form-control @error('return_notes') is-invalid @enderror" rows="3" placeholder="Catatan umum untuk transaksi pemulangan ini."></textarea>
                    @error('return_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Diterima Oleh (Pegawai BPM)') }}:</label>
                    <p class="form-control-plaintext">{{ Auth::user()->name ?? __('N/A') }}</p>
                </div>
            </x-card>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitReturn" @if(empty($selectedTransactionItemIds)) disabled @endif>
                    <span wire:loading wire:target="submitReturn" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <span wire:loading.remove wire:target="submitReturn"><i class="bi bi-check-lg me-1"></i></span>
                    {{ __('Rekod Pulangan Peralatan') }}
                </button>
            </div>
        </form>
    @endif

    <div class="mt-4 text-center">
        {{-- Corrected route name --}}
        <a href="{{ route('loan-applications.show', $loanApplication) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            {{ __('Kembali ke Butiran Permohonan') }}
        </a>
    </div>
</div>
