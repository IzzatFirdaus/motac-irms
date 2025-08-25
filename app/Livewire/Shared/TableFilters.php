<?php

namespace App\Livewire\Shared;

use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * TableFilters Livewire Component.
 *
 * Provides a reusable, URL-synced search and filter bar for tables.
 * Emits events when filters are updated or reset.
 */
class TableFilters extends Component
{
    // Sync search term with URL (as 'q'), keep in browser history, and on page reload.
    #[Url(as: 'q', history: true, keep: true)]
    public string $searchTerm = '';

    // Example for additional filters (add #[Url] as needed for deep-linking)
    // public string $statusFilter = '';

    /**
     * Handle updates to the search term.
     * Emits 'filtersUpdated' event with latest state.
     */
    public function updatedSearchTerm(string $value): void
    {
        $this->dispatch('filtersUpdated', ['searchTerm' => $value]);
    }

    // Example for another filter:
    // public function updatedStatusFilter(string $value): void
    // {
    //     $this->dispatch('filtersUpdated', ['statusFilter' => $value]);
    // }

    /**
     * Reset all filters to their initial state.
     * Emits 'filtersCleared' event, and also 'filtersUpdated' with empty values.
     */
    public function resetFilters(): void
    {
        $this->reset();
        $this->dispatch('filtersCleared');
        $this->dispatch('filtersUpdated', ['searchTerm' => '']);
    }

    /**
     * Render the table filters view.
     */
    public function render()
    {
        // You can pass in $searchLabel, $searchPlaceholder, $hasOtherFilters via component attributes.
        return view('livewire.shared.table-filters');
    }
}
