<?php

namespace App\View\Components;

use App\Models\Equipment; // Import the Equipment model
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class EquipmentStatusBadge extends Component
{
    public string $statusKey;

    public string $statusLabel;

    public string $badgeClass;

    /**
     * Create a new component instance.
     *
     * @param string $status The equipment status key.
     */
    public function __construct(string $status = '')
    {
        $this->statusKey   = $status;
        $statusOptions     = Equipment::getStatusOptions();
        $this->statusLabel = $statusOptions[$this->statusKey] ?? Str::title(str_replace('_', ' ', $this->statusKey));

        switch ($this->statusKey) {
            case Equipment::STATUS_AVAILABLE:
                $this->badgeClass = 'text-bg-success';
                break;
            case Equipment::STATUS_ON_LOAN:
                $this->badgeClass = 'text-bg-warning';
                break;
            case Equipment::STATUS_UNDER_MAINTENANCE:
                $this->badgeClass = 'text-bg-info';
                break;
            case Equipment::STATUS_DISPOSED:
            case Equipment::STATUS_LOST:
                $this->badgeClass = 'text-bg-danger';
                break;
            case Equipment::STATUS_DAMAGED_NEEDS_REPAIR:
                $this->badgeClass = 'text-bg-orange'; // Example custom class or use text-bg-warning
                break;
            case Equipment::STATUS_DAMAGED:
                $this->badgeClass = 'text-bg-danger';
                break;
            case Equipment::STATUS_RETIRED:
                $this->badgeClass = 'text-bg-secondary';
                break;
            default:
                $this->badgeClass = 'text-bg-secondary';
                break;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.equipment-status-badge');
    }
}
