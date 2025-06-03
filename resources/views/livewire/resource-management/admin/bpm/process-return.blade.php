{{-- resources/views/livewire/resource-management/admin/bpm/process-return.blade.php --}}
<div>
    <h3 class="mb-4">{{ __('Rekod Pulangan Peralatan untuk Permohonan Pinjaman') }} #{{ $loanApplication->id }}</h3>

    @if (session()->has('success'))
        {{-- Assuming x-alert is your Blade component for alerts, styled by MOTAC theme --}}
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
    {{-- Assuming x-card uses .motac-card styling --}}
    <x-card card-title="{{ __('Butiran Permohonan Pinjaman') }}" class="mb-4">
        <p class="mb-1"><span class="fw-semibold">{{ __('Pemohon') }}:</span>
            {{ $loanApplication->user->name ?? __('N/A') }}</p>
        <p class="mb-1"><span class="fw-semibold">{{ __('Tujuan Permohonan') }}:</span>
            {{ $loanApplication->purpose ?? __('N/A') }}</p>
        {{-- Add other relevant loan application details here as in process-issuance.blade.php if needed --}}
        <p class="mb-0"><span class="fw-semibold">{{ __('Tarikh Dijangka Pulang') }}:</span>
            {{ $loanApplication->loan_end_date ? $loanApplication->loan_end_date->translatedFormat(config('app.date_format_my', 'd/m/Y')) : __('N/A') }}
        </p>


        @if ($issuedTransactionItems->isNotEmpty())
            <h6 class="mt-3 mb-2 fw-semibold">{{ __('Peralatan Sedang Dipinjam Untuk Permohonan Ini:') }}</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light"> {{-- Ensure table-light uses MOTAC theme colors --}}
                        <tr>
                            <th class="small px-3 py-2">{{ __('Peralatan (Tag ID)') }}</th>
                            <th class="small px-3 py-2">{{ __('Tarikh Dikeluarkan') }}</th>
                            {{-- Add other relevant columns if needed --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issuedTransactionItems as $item)
                            <tr>
                                <td class="small px-3 py-2">
                                    {{ $item->equipment->brand ?? __('N/A') }}
                                    {{ $item->equipment->model ?? __('N/A') }}
                                    (Tag: {{ $item->equipment->tag_id ?? __('N/A') }})
                                </td>
                                <td class="small px-3 py-2">
                                    {{ $item->loanTransaction->issue_timestamp?->translatedFormat(config('app.datetime_format_my')) ?? ($item->loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my')) ?? __('N/A')) }}
                                </td>
                                {{-- other columns --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted fst-italic mt-3">{{ __('Tiada peralatan sedang dipinjam untuk permohonan ini.') }}</p>
        @endif
    </x-card>

    <form wire:submit.prevent="submitReturn">
        {{-- Assuming x-card uses .motac-card styling --}}
        <x-card card-title="{{ __('Rekod Pulangan Peralatan Sebenar') }}">
            <div class="mb-3">
                <label for="selectedTransactionItemIds"
                    class="form-label fw-semibold">{{ __('Pilih Peralatan yang Dipulangkan') }}*:</label>
                <select wire:model.defer="selectedTransactionItemIds" id="selectedTransactionItemIds"
                    class="form-select @error('selectedTransactionItemIds') is-invalid @enderror @error('selectedTransactionItemIds.*') is-invalid @enderror"
                    multiple required size="5">
                    @forelse ($issuedTransactionItems as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->equipment->brand ?? __('N/A') }} {{ $item->equipment->model ?? __('N/A') }}
                            (Tag: {{ $item->equipment->tag_id ?? __('N/A') }})
                            - Dikeluarkan:
                            {{ $item->loanTransaction->issue_timestamp?->translatedFormat(config('app.datetime_format_my')) ?? ($item->loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my')) ?? __('N/A')) }}
                        </option>
                    @empty
                        <option value="" disabled>{{ __('Tiada peralatan untuk dipulangkan.') }}</option>
                    @endforelse
                </select>
                @error('selectedTransactionItemIds')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('selectedTransactionItemIds.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Example for condition per item if you implement this logic --}}
            {{-- @foreach ($issuedTransactionItems as $item)
                @if (is_array($selectedTransactionItemIds) && in_array($item->id, $selectedTransactionItemIds))
                    <div class="mb-3">
                        <label for="condition_item_{{$item->id}}" class="form-label">{{ __('Keadaan Semasa Pulangan untuk') }} {{ $item->equipment->tag_id }}:</label>
                        <select wire:model.defer="item_conditions.{{$item->id}}" id="condition_item_{{$item->id}}" class="form-select">
                            @foreach (\App\Models\Equipment::getConditionStatusOptions() as $value => $label) {{-- Assuming this static method exists --}}
            <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
            </select>
</div>
@endif
@endforeach --}}

<div class="mb-3">
    <label class="form-label fw-semibold">{{ __('Senarai Semak Aksesori Dipulangkan') }}:</label>
    <div class="row">
        @foreach ($allAccessoriesList as $accessory) {{-- Ensure $allAccessoriesList is populated from Design Language config or DB --}}
            <div class="col-md-6 col-lg-4">
                <div class="form-check">
                    <input type="checkbox" wire:model.defer="accessories_on_return" value="{{ $accessory }}"
                        id="return-accessory-{{ Str::slug($accessory) }}" class="form-check-input">
                    <label class="form-check-label"
                        for="return-accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                </div>
            </div>
        @endforeach
    </div>
    @error('accessories_on_return')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="return_notes"
        class="form-label fw-semibold">{{ __('Catatan Pulangan (cth: kerosakan, item hilang)') }}:</label>
    <textarea wire:model.defer="return_notes" id="return_notes"
        class="form-control @error('return_notes') is-invalid @enderror" rows="3"></textarea>
    @error('return_notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">{{ __('Diterima Oleh') }}:</label>
    <p class="form-control-plaintext">{{ Auth::user()->name ?? __('N/A') }}</p>
</div>
</x-card>

<div class="text-center mt-4">
    {{-- btn-primary should be MOTAC themed --}}
    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitReturn">
        <span wire:loading wire:target="submitReturn" class="spinner-border spinner-border-sm me-2" role="status"
            aria-hidden="true"></span>
        {{-- Iconography: Design Language 2.4 --}}
        <span wire:loading.remove wire:target="submitReturn"><i class="bi bi-check-lg me-1"></i></span>
        {{ __('Rekod Pulangan Peralatan') }}
    </button>
</div>
</form>

<div class="mt-4 text-center">
    {{-- btn-secondary should be MOTAC themed --}}
    <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApplication) }}"
        class="btn btn-secondary">
        {{-- Iconography: Design Language 2.4 --}}
        <i class="bi bi-arrow-left me-1"></i>
        {{ __('Kembali ke Butiran Permohonan') }}
    </a>
</div>
</div>
