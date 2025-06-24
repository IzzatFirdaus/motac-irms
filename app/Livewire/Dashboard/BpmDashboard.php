<?php

namespace App\Livewire\Dashboard;

use App\Models\Equipment;
use Livewire\Component;

class BpmDashboard extends Component
{
    public int $availableLaptopsCount = 0;

    public int $availableProjectorsCount = 0;

    public int $availablePrintersCount = 0;

    public function mount(): void
    {
        $this->availableLaptopsCount = Equipment::where('status', 'available')
            ->where('asset_type', 'laptop')
            ->count();

        $this->availableProjectorsCount = Equipment::where('status', 'available')
            ->where('asset_type', 'projector')
            ->count();

        $this->availablePrintersCount = Equipment::where('status', 'available')
            ->where('asset_type', 'printer')
            ->count();
    }

    public function render()
    {
        // This view now contains the calls to the self-contained Livewire list components.
        return view('livewire.dashboard.bpm-dashboard');
    }
}
