<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Url;

class TableFilters extends Component
{
    #[Url(as: 'q', history: true, keep: true)] // Keep search term in URL
    public string $searchTerm = '';

    // Example other filters (add #[Url] if needed)
    // public string $dateFrom = '';
    // public string $dateTo = '';
    // public string $statusFilter = '';

    public function updatedSearchTerm(string $value): void
    {
        $this->dispatch('filtersUpdated', ['searchTerm' => $value]);
    }

    // Example for another filter
    // public function updatedStatusFilter(string $value): void
    // {
    //    $this->dispatch('filtersUpdated', ['statusFilter' => $value]);
    // }

    public function resetFilters(): void
    {
        $this->reset();
        // Dispatch an event to notify parent components to clear their filters too
        $this->dispatch('filtersCleared');
        // Or specifically update if only searchTerm is used by parent
        $this->dispatch('filtersUpdated', ['searchTerm' => '']);

    }

    public function render()
    {
        return view('livewire.shared.table-filters');
    }
}
