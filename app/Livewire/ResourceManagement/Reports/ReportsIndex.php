<?php

namespace App\Livewire\ResourceManagement\Reports;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * ReportsIndex Livewire component.
 *
 * This component displays the reports dashboard/landing page,
 * showing cards/links to each available report in the system.
 * It can be extended in the future for dynamic report listings or permissions.
 */
#[Layout('layouts.app')]
class ReportsIndex extends Component
{
    /**
     * Render the reports index Blade view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Currently, this component does not need dynamic data,
        // but could be extended for permissions or dynamic report lists.
        return view('livewire.resource-management.reports.reports-index');
    }
}
