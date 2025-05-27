<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-5">
                <label for="globalSearchTerm" class="form-label small text-muted">{{ __('Carian Umum') }}</label>
                <input wire:model.live.debounce.500ms="searchTerm" type="search" id="globalSearchTerm"
                       placeholder="{{ __('Masukkan kata kunci...') }}" class="form-control form-control-sm">
            </div>
            {{-- Add other common filter inputs here --}}
            {{-- e.g.,
            <div class="col-md-3 col-lg-3">
                <label for="statusFilterShared" class="form-label small text-muted">{{ __('Status') }}</label>
                <select wire:model.live="statusFilter" id="statusFilterShared" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    // Options
                </select>
            </div>
            --}}
             <div class="col-md-3 col-lg-2">
                <button wire:click="resetFilters" type="button" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="ti ti-rotate-clockwise-2 me-1"></i> {{ __('Reset') }}
                </button>
            </div>
        </div>
    </div>
</div>
