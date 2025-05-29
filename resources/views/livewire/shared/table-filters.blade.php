{{-- resources/views/livewire/shared/table-filters.blade.php (or your chosen path for shared Livewire views) --}}
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-end">
            {{-- General Search Term Input --}}
            <div class="col-md-6 col-lg-{{ $hasOtherFilters ?? false ? '5' : '10' }}"> {{-- Adjust width based on whether other filters are present via slot --}}
                <label for="globalSearchTerm_{{ $this->id }}" class="form-label small text-muted">{{ $searchLabel ?? __('Carian Umum') }}</label>
                <input wire:model.live.debounce.500ms="searchTerm" type="search" id="globalSearchTerm_{{ $this->id }}"
                       placeholder="{{ $searchPlaceholder ?? __('Masukkan kata kunci carian...') }}" class="form-control form-control-sm">
            </div>

            {{-- Slot for additional, context-specific filter inputs --}}
            {{ $slot }}

            {{-- Reset Button --}}
            <div class="col-md-{{ $hasOtherFilters ?? false ? '3' : 'auto' }} col-lg-2 mt-3 mt-md-0">
                <button wire:click="resetFilters" type="button" class="btn btn-sm btn-outline-secondary w-100" title="{{__('Set Semula Semua Tapisan')}}">
                    <i class="ti ti-rotate-clockwise-2 me-1"></i> {{ __('Reset') }}
                </button>
            </div>
        </div>
    </div>
</div>
