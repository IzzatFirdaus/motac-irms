<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * EquipmentInventoryReport Livewire Component
 * Generates an inventory report for ICT equipment with filters, sorting, and pagination.
 */
#[Layout('layouts.app')]
#[Title('Laporan Inventori Peralatan ICT')]
class EquipmentInventoryReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties (public for Livewire binding)
    public ?string $filterAssetType = '';
    public ?string $filterStatus = '';
    public ?string $filterCondition = '';
    public ?int $filterDepartmentId = null;
    public ?int $filterLocationId = null;
    public ?int $filterCategoryId = null;
    public string $searchTerm = '';

    // Sorting controls
    public string $sortBy = 'tag_id';
    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';
    public int $perPage = 15;

    /**
     * Mount the component, logs the report page view for audit purposes.
     */
    public function mount(): void
    {
        Log::info("Livewire\EquipmentInventoryReport: Generating Equipment Inventory Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Computed property: Get filtered, sorted, and paginated inventory data.
     */
    public function getReportDataProperty()
    {
        $query = Equipment::query()
            ->with(['department', 'location', 'category'])
            ->when($this->filterAssetType, fn ($q) => $q->where('asset_type', $this->filterAssetType))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCondition, fn ($q) => $q->where('condition_status', $this->filterCondition))
            ->when($this->filterDepartmentId, fn ($q) => $q->where('department_id', $this->filterDepartmentId))
            ->when($this->filterLocationId, fn ($q) => $q->where('location_id', $this->filterLocationId)) // Corrected field to 'location_id'
            ->when($this->filterCategoryId, fn ($q) => $q->where('category_id', $this->filterCategoryId))
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('tag_id', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('serial_number', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('model', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('brand', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('item_code', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // Computed options for select filters
    public function getAssetTypeOptionsProperty(): array
    {
        return Equipment::getAssetTypeOptions();
    }

    public function getStatusOptionsProperty(): array
    {
        return Equipment::getStatusOptions();
    }

    public function getConditionStatusOptionsProperty(): array
    {
        return Equipment::getConditionStatusesList();
    }

    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    public function getLocationOptionsProperty(): \Illuminate\Support\Collection
    {
        return Location::orderBy('name')->pluck('name', 'id');
    }

    public function getCategoryOptionsProperty(): \Illuminate\Support\Collection
    {
        return EquipmentCategory::orderBy('name')->pluck('name', 'id');
    }

    /**
     * Reset pagination when filter or search properties are updated.
     */
    public function updating($property): void
    {
        if (in_array($property, [
            'filterAssetType', 'filterStatus', 'filterCondition',
            'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm'
        ])) {
            $this->resetPage();
        }
    }

    /**
     * Set the column to sort by and toggle the direction.
     */
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

    /**
     * Reset all filters and sorting to their default values.
     */
    public function resetFilters(): void
    {
        $this->reset([
            'filterAssetType', 'filterStatus', 'filterCondition',
            'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm',
            'sortBy', 'sortDirection'
        ]);
        $this->sortBy = 'tag_id';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    /**
     * Render the Blade view for the inventory report.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.reports.equipment-inventory-report', [
            'reportData' => $this->reportDataProperty,
            'assetTypeOptions' => $this->assetTypeOptionsProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'conditionStatusOptions' => $this->conditionStatusOptionsProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
            'locationOptions' => $this->locationOptionsProperty,
            'categoryOptions' => $this->categoryOptionsProperty,
        ]);
    }
}
