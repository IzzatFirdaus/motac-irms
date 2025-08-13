<?php

namespace App\Livewire\ResourceManagement\Reports;

use App\Models\Equipment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: EquipmentReport
 * Displays a paginated, filterable report of all ICT equipment in the system.
 */
#[Layout('layouts.app')]
class EquipmentReport extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterDepartment = '';
    public int $perPage = 15;

    protected string $paginationTheme = 'bootstrap';

    /**
     * Get the list of equipment for the report, with applied filters and search.
     */
    public function getEquipmentListProperty()
    {
        // Use tag_id instead of asset_tag for sorting and searching
        $query = Equipment::query()
            ->with(['department'])
            ->orderBy('tag_id', 'asc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tag_id', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Reset pagination when filters or search are updated.
     */
    public function updatingSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterDepartment() { $this->resetPage(); }

    /**
     * Render the equipment report Blade view.
     */
    public function render(): View
    {
        // For filter options - you can improve with a repository/service for real projects
        $departments = \App\Models\Department::orderBy('name')->get();
        $statusOptions = [
            '' => __('All'),
            'available' => __('Available'),
            'on_loan' => __('On Loan'),
            'in_repair' => __('In Repair'),
            'disposed' => __('Disposed'),
            'lost' => __('Lost'),
        ];

        return view('livewire.resource-management.reports.equipment-report', [
            'equipmentList' => $this->equipmentList,
            'departments' => $departments,
            'statusOptions' => $statusOptions,
        ]);
    }
}
