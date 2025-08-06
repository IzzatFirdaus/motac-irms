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

#[Layout('layouts.app')]
#[Title('Laporan Inventori Peralatan ICT')]
class EquipmentInventoryReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterAssetType = '';
    public ?string $filterStatus = '';
    public ?string $filterCondition = '';
    public ?int $filterDepartmentId = null;
    public ?int $filterLocationId = null;
    public ?int $filterCategoryId = null;
    public string $searchTerm = ''; // Search by tag, serial, model, brand, item_code

    // Sorting properties
    public string $sortBy = 'tag_id';
    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 15;

    public function mount(): void
    {
        // Example authorization, ensure policy exists
        // $this->authorize('viewAny', Equipment::class); // Or a specific report permission
        Log::info("Livewire\EquipmentInventoryReport: Generating Equipment Inventory Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Computed property to get the report data for equipment inventory.
     */
    public function getReportDataProperty()
    {
        $query = Equipment::query()
            ->with(['department', 'location', 'category'])
            ->when($this->filterAssetType, fn ($q) => $q->where('asset_type', $this->filterAssetType))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCondition, fn ($q) => $q->where('condition_status', $this->filterCondition))
            ->when($this->filterDepartmentId, fn ($q) => $q->where('department_id', $this->filterDepartmentId))
            ->when($this->filterLocationId, fn ($q) => $q->where('current_location_id', $this->filterLocationId)) // Assuming current_location_id for Location model
            ->when($this->filterCategoryId, fn ($q) => $q->where('category_id', $this->filterCategoryId))
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('tag_id', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('serial_number', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('model', 'like', '%'.$this->searchTerm.'%') // Changed model_name to model
                        ->orWhere('brand', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('item_code', 'like', '%'.$this->searchTerm.'%'); // If item_code exists on Equipment
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // Computed properties for filter options
    public function getAssetTypeOptionsProperty(): array
    {
        return Equipment::getAssetTypeOptions(); // Corrected to call the method
    }

    public function getStatusOptionsProperty(): array
    {
        return Equipment::getStatusOptions(); // Corrected to call the method
    }

    public function getConditionStatusOptionsProperty(): array
    {
        return Equipment::getConditionStatusesList(); // Corrected to call the method
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
     * Resets pagination when filters or search term are updated.
     */
    public function updating($property): void
    {
        if (in_array($property, ['filterAssetType', 'filterStatus', 'filterCondition', 'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm'])) {
            $this->resetPage();
        }
    }

    /**
     * Sets the column to sort by and toggles direction.
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
     * Resets all filters and sorting to their default values.
     */
    public function resetFilters(): void
    {
        $this->reset(['filterAssetType', 'filterStatus', 'filterCondition', 'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm', 'sortBy', 'sortDirection']);
        $this->sortBy = 'tag_id'; // Default sort
        $this->sortDirection = 'asc'; // Default direction
        $this->resetPage();
    }

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
