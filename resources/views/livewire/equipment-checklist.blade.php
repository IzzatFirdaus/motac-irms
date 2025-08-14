<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transactionType === 'issue' ? __('Pengeluaran Peralatan') : __('Pulangan Peralatan') }} - MOTAC</title>
    {{-- In a full application, these CSS/JS links would typically be in a master layout file --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{-- Noto Sans font and custom MOTAC theme CSS should be linked here or in a master layout --}}
    <style>
        /* Basic body styling, assuming your main MOTAC theme CSS will provide the rest */
        body {
            background-color: #F8F9FA;
            /* Corresponds to --motac-background in light mode */
            font-family: 'Noto Sans', sans-serif;
            /* As per Design Doc */
            color: #212529;
            /* Corresponds to --motac-text in light mode */
        }

        .card-title {
            /* This class is used in the h3, ensure it aligns with h4 styles if needed */
            font-weight: 600;
            /* Semibold, aligns with h4 in Design Doc if using h3 as h4 visual */
        }

        /* Additional custom styles for this page can be added here or in the main theme file */
    </style>
    @livewireStyles
</head>

<body class="p-3 p-md-4">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Standard Bootstrap card, will inherit MOTAC theme from global CSS --}}
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($transactionType === 'issue')
                            {{-- Using h4 as per Design Doc typography for section/page titles if this is the main title --}}
                            <h3 class="card-title h4 mb-4 text-dark">
                                <i class="bi bi-box-arrow-up-right me-2"></i>{{-- Icon for "Issue" --}}
                                Pengeluaran Peralatan untuk Permohonan Pinjaman #{{ $loanApplicationId }}
                            </h3>
                        @elseif ($transactionType === 'return')
                            <h3 class="card-title h4 mb-4 text-dark">
                                <i class="bi bi-box-arrow-in-left me-2"></i>{{-- Icon for "Return" --}}
                                Proses Pulangan Peralatan untuk Permohonan Pinjaman
                                #{{ $loanTransaction->loanApplication->id ?? $loanApplicationId }}
                            </h3>
                            @if ($loanTransaction)
                                <div class="mb-4 text-muted small p-3 bg-light rounded border">
                                    <p class="mb-1"><strong>{{ __('ID Transaksi Pengeluaran:') }}</strong>
                                        #{{ $loanTransaction->id }}</p>
                                    <p class="mb-1"><strong>{{ __('Dikeluarkan Pada:') }}</strong>
                                        {{ $loanTransaction->issue_timestamp?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                                    </p>
                                    <p class="mb-1"><strong>{{ __('Dikeluarkan Oleh:') }}</strong>
                                        {{ $loanTransaction->issuingOfficer->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>{{ __('Peralatan Dikeluarkan:') }}</strong>
                                        {{ $loanTransaction->equipment->brand ?? 'N/A' }}
                                        {{ $loanTransaction->equipment->model ?? 'N/A' }}
                                        (Tag: {{ $loanTransaction->equipment->tag_id ?? 'N/A' }})
                                    </p>
                                    <p class="mb-0"><strong>{{ __('Aksesori Dikeluarkan:') }}</strong>
                                        @php
                                            $accessoriesIssued = 'Tiada';
                                            if ($loanTransaction->accessories_checklist_on_issue) {
                                                $decodedAccessories = json_decode(
                                                    $loanTransaction->accessories_checklist_on_issue,
                                                    true,
                                                );
                                                if (is_array($decodedAccessories) && !empty($decodedAccessories)) {
                                                    $accessoriesIssued = implode(', ', $decodedAccessories);
                                                } elseif (
                                                    is_string($decodedAccessories) &&
                                                    !empty($decodedAccessories)
                                                ) {
                                                    $accessoriesIssued = $decodedAccessories; // If it's a non-JSON string already
                                                }
                                            }
                                        @endphp
                                        {{ $accessoriesIssued }}
                                    </p>
                                </div>
                            @endif
                        @endif

                        <form wire:submit.prevent="saveTransaction">
                            <div class="mb-3">
                                <label for="selectedEquipmentIds"
                                    class="form-label fw-bold">{{ __('Pilih Peralatan:') }} <span
                                        class="text-danger">*</span></label>
                                {{-- For multiple select, consider a more user-friendly component if list is very long (e.g., TomSelect or Select2 styled with Bootstrap) --}}
                                <select wire:model="selectedEquipmentIds" id="selectedEquipmentIds"
                                    class="form-select @error('selectedEquipmentIds') is-invalid @enderror"
                                    {{ $transactionType === 'issue' ? 'multiple' : '' }} {{-- Return usually one specific item, issue might be multiple for a request --}}
                                    size="{{ $transactionType === 'issue' ? '5' : '3' }}">
                                    @if ($transactionType === 'issue')
                                        @forelse ($availableEquipment as $equipment)
                                            <option value="{{ $equipment->id }}">
                                                {{ $equipment->brand }} {{ $equipment->model }} (Tag:
                                                {{ $equipment->tag_id ?? 'N/A' }})
                                            </option>
                                        @empty
                                            <option value="" disabled>
                                                {{ __('Tiada peralatan tersedia untuk jenis yang diminta') }}</option>
                                        @endforelse
                                    @elseif ($transactionType === 'return')
                                        @forelse ($onLoanEquipment as $equipment)
                                            {{-- Assuming $onLoanEquipment is correctly populated for the transaction --}}
                                            <option value="{{ $equipment->id }}">
                                                {{ $equipment->brand }} {{ $equipment->model }} (Tag:
                                                {{ $equipment->tag_id ?? 'N/A' }})
                                            </option>
                                        @empty
                                            <option value="" disabled>
                                                {{ __('Tiada peralatan dikeluakan untuk transaksi ini.') }}</option>
                                        @endforelse
                                    @endif
                                </select>
                                @error('selectedEquipmentIds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($transactionType === 'issue')
                                    <small
                                        class="form-text text-muted">{{ __('Tahan CTRL/CMD untuk memilih lebih dari satu.') }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Senarai Semak Aksesori:') }}</label>
                                <p class="text-muted small mb-2">
                                    {{ __('Sila tandakan aksesori yang disertakan bersama peralatan.') }}</p>
                                <div class="row row-cols-1 row-cols-sm-2 g-2">
                                    {{-- $allAccessoriesList should be from config or a shared source as per Design Doc Section 3.3 System Configuration --}}
                                    @forelse ($allAccessoriesList as $accessory)
                                        <div class="col">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model.defer="accessories"
                                                    {{-- Use .defer if no immediate action on check --}} value="{{ $accessory }}"
                                                    id="accessory-{{ Str::slug($accessory) }}"
                                                    class="form-check-input">
                                                <label class="form-check-label small"
                                                    for="accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted small">
                                                {{ __('Tiada senarai aksesori standard ditetapkan.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                                @error('accessories')
                                    <div class="d-block text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label fw-bold">
                                    {{ $transactionType === 'return' ? __('Catatan Pulangan (cth: kerosakan, item hilang)') : __('Catatan Pengeluaran') }}:
                                </label>
                                <textarea wire:model.defer="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                    rows="3"></textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4"> {{-- Increased bottom margin --}}
                                <label class="form-label fw-bold d-block">{{ __('Diproses Oleh:') }}</label>
                                {{-- Using form-control-plaintext for a read-only display that aligns with form inputs --}}
                                <p class="form-control-plaintext ps-0">{{ Auth::user()->name ?? 'N/A' }}</p>
                            </div>

                            <div class="text-center mt-4 pt-2">
                                {{-- Using MOTAC primary button color (via Bootstrap's .btn-primary which should be themed) --}}
                                <button type="submit" class="btn btn-primary btn-lg px-4" wire:loading.attr="disabled"
                                    wire:target="saveTransaction">
                                    <span wire:loading.remove wire:target="saveTransaction">
                                        {{-- Icons added for clarity --}}
                                        @if ($transactionType === 'issue')
                                            <i
                                                class="bi bi-check-circle-fill me-2"></i>{{ __('Rekod Pengeluaran Peralatan') }}
                                        @else
                                            <i
                                                class="bi bi-check-circle-fill me-2"></i>{{ __('Rekod Pulangan Peralatan') }}
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="saveTransaction"
                                        class="d-inline-flex align-items-center">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                            aria-hidden="true"></span>
                                        {{ __('Menyimpan...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
