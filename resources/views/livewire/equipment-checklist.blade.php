<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transactionType === 'issue' ? __('Pengeluaran Peralatan') : __('Pulangan Peralatan') }} - MOTAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #F8F9FA;
            font-family: 'Noto Sans', sans-serif;
            color: #212529;
        }
        .card-title { font-weight: 600; }
    </style>
    @livewireStyles
</head>

<body class="p-3 p-md-4">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
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
                            <h3 class="card-title h4 mb-4 text-dark">
                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                Pengeluaran Peralatan untuk Permohonan Pinjaman #{{ $loanApplicationId }}
                            </h3>
                        @elseif ($transactionType === 'return')
                            <h3 class="card-title h4 mb-4 text-dark">
                                <i class="bi bi-box-arrow-in-left me-2"></i>
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
                                                    $accessoriesIssued = $decodedAccessories;
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
                                <select wire:model="selectedEquipmentIds" id="selectedEquipmentIds"
                                    class="form-select @error('selectedEquipmentIds') is-invalid @enderror"
                                    {{ $transactionType === 'issue' ? 'multiple' : '' }}
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
                                    @forelse ($allAccessoriesList as $accessory)
                                        <div class="col">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model.defer="accessories"
                                                    value="{{ $accessory }}"
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

                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">{{ __('Diproses Oleh:') }}</label>
                                <p class="form-control-plaintext ps-0">{{ Auth::user()->name ?? 'N/A' }}</p>
                            </div>

                            <div class="text-center mt-4 pt-2">
                                <button type="submit" class="btn btn-primary btn-lg px-4" wire:loading.attr="disabled"
                                    wire:target="saveTransaction">
                                    <span wire:loading.remove wire:target="saveTransaction">
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
