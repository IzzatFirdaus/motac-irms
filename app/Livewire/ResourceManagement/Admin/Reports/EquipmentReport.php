<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department; // Example model
use App\Models\Equipment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EquipmentReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterAssetType = null;
    public ?string $filterStatus = null;
    public ?string $filterCondition = null;
    public ?int $filterDepartmentId = null;
    public string $searchTerm = '';

    // Sorting properties
    public string $sortBy = 'tag_id';
    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';

    public function mount()
    {
        // $this->authorize('viewEquipmentReport');
    }

    public function getReportDataProperty()
    {
        $query = Equipment::with(['department', 'creator']); // Eager load

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('tag_id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('model', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $this->searchTerm . '%');
            });
        }
        if ($this->filterAssetType) {
            $query->where('asset_type', $this->filterAssetType);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterCondition) {
            $query->where('condition_status', $this->filterCondition);
        }
        if ($this->filterDepartmentId) {
            $query->where('department_id', $this->filterDepartmentId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);
        return $query->paginate(15);
    }

    // Options for filters
    public function getAssetTypeOptionsProperty(): array
    {
        return defined(Equipment::class . '::$ASSET_TYPES_LABELS') ? Equipment::$ASSET_TYPES_LABELS : [];
    }
    public function getStatusOptionsProperty(): array
    {
        return defined(Equipment::class . '::$STATUSES_LABELS') ? Equipment::$STATUSES_LABELS : [];
    }
    public function getConditionStatusOptionsProperty(): array
    {
        return defined(Equipment::class . '::$CONDITION_STATUSES_LABELS') ? Equipment::$CONDITION_STATUSES_LABELS : [];
    }
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.resource-management.admin.reports.equipment-report', [
            'reportData' => $this->reportDataProperty,
            'assetTypeOptions' => $this->assetTypeOptionsProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'conditionStatusOptions' => $this->conditionStatusOptionsProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
        ])->title(__('Laporan Peralatan ICT'));
    }
}
