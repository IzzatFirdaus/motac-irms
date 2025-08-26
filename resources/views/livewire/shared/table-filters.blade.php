{{-- resources/views/livewire/shared/table-filters.blade.php --}}
{{--
  Reusable Livewire component view for table filtering UI.
  Uses MOTAC token classes and accessible controls.
--}}
<div class="motac-card shadow-sm mb-4"> {{-- Use MOTAC card styling --}}
    <div class="motac-card-body p-3">
        <div class="row g-3 align-items-end">
            {{-- General Search Term Input --}}
            <div class="col-md-6 col-lg-{{ $hasOtherFilters ?? false ? '5' : '10' }}">
                <label for="globalSearchTerm_{{ $this->id }}" class="form-label small fw-medium">
                    {{ $searchLabel ?? __('Carian Umum') }}
                </label>
                <input
                    wire:model.live.debounce.500ms="searchTerm"
                    type="search"
                    id="globalSearchTerm_{{ $this->id }}"
                    placeholder="{{ $searchPlaceholder ?? __('Masukkan kata kunci carian...') }}"
                    class="form-control form-control-sm"
                >
            </div>

            {{-- Slot for additional, context-specific filter inputs --}}
            {{ $slot }}

            {{-- Reset Button --}}
            <div class="col-md-{{ $hasOtherFilters ?? false ? '3' : 'auto' }} col-lg-2 mt-3 mt-md-0">
                <button wire:click="resetFilters" type="button"
                        class="motac-btn-outline btn-sm w-100" title="{{ __('Set Semula Semua Tapisan') }}">
                    <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>
                    {{ __('Set Semula') }}
                </button>
            </div>
        </div>
    </div>
</div>
                        class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline"
