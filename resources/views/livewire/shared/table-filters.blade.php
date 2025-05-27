<<<<<<< HEAD
{{-- resources/views/livewire/shared/table-filters.blade.php --}}
{{--
  Reusable Livewire component view for table filtering UI.
  Design Language Alignment:
  - 1.2 User-Centricity: Bahasa Melayu First for labels and placeholders.
  - 2.1 Color Palette: Form controls and buttons should use MOTAC theme colors.
  - 2.2 Typography: Noto Sans for all text. Labels are 'small fw-medium'.
  - 2.4 Iconography: Uses Bootstrap Icons (bi-*).
  - 3.2 Data Inputs: Adheres to form field standards.
--}}
<div class="card shadow-sm mb-4 motac-card"> {{-- Using .motac-card for consistency --}}
    <div class="card-body p-3 motac-card-body"> {{-- Using .motac-card-body --}}
        <div class="row g-3 align-items-end">
            {{-- General Search Term Input --}}
            <div class="col-md-6 col-lg-{{ $hasOtherFilters ?? false ? '5' : '10' }}">
                {{-- Label Styling: Design Language 2.2 & 3.2. Using 'small fw-medium'. --}}
                <label for="globalSearchTerm_{{ $this->id }}"
                    class="form-label small fw-medium">{{ $searchLabel ?? __('Carian Umum') }}</label>
                <input wire:model.live.debounce.500ms="searchTerm" type="search" id="globalSearchTerm_{{ $this->id }}"
                    placeholder="{{ $searchPlaceholder ?? __('Masukkan kata kunci carian...') }}"
                    class="form-control form-control-sm"> {{-- Ensure form-control is MOTAC themed --}}
            </div>

            {{-- Slot for additional, context-specific filter inputs --}}
            {{-- Ensure any inputs within this slot also use MOTAC themed styles and Noto Sans font --}}
            {{ $slot }}

            {{-- Reset Button --}}
            <div class="col-md-{{ $hasOtherFilters ?? false ? '3' : 'auto' }} col-lg-2 mt-3 mt-md-0">
                <button wire:click="resetFilters" type="button"
                    class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline"
                    title="{{ __('Set Semula Semua Tapisan') }}"> {{-- Ensure .motac-btn-outline or themed .btn-outline-secondary --}}
                    {{-- Iconography: Design Language 2.4. Changed from ti-rotate-clockwise-2 --}}
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    {{-- Language: Design Language 1.2. Changed from "Reset" --}}
                    {{ __('Set Semula') }}
=======
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
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
                </button>
            </div>
        </div>
    </div>
</div>
