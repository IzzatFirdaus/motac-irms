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
 * EquipmentReport Livewire Component
 * Generates a report of ICT equipment with filters, sorting, and pagination.
 */
#[Layout('layouts.app')]
#[Title('Laporan Peralatan ICT')]
/**
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $reportData
 * @property-read array $assetTypeOptions
 * @property-read array $statusOptions
 * @property-read array $conditionStatusOptions
 * @property-read \Illuminate\Support\Collection $departmentOptions
 * @property-read \Illuminate\Support\Collection $locationOptions
 * @property-read \Illuminate\Support\Collection $categoryOptions
 */
class EquipmentReport extends Component
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

    public string $searchTerm = '';

    // Sorting properties
    public string $sortBy = 'tag_id';

    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 15;

    /**
     * Mount the component and log the report page view for audit purposes.
     */
    public function mount(): void
    {
        Log::info("Livewire\EquipmentReport: Generating Equipment Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address'    => request()->ip(),
        ]);
    }

    /**
     * Computed property: Get filtered, sorted, and paginated equipment report data.
     */
    public function getReportDataProperty()
    {
        $query = Equipment::with([
            'department:id,name',
            'creator:id,name',
            'location:id,name',
            'category:id,name',
        ]);

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search): void {
                $q->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(model) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(brand) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(item_code) LIKE ?', [$search]);
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
        if ($this->filterLocationId) {
            $query->where('location_id', $this->filterLocationId);
        }
        if ($this->filterCategoryId) {
            $query->where('category_id', $this->filterCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info(sprintf('Livewire\EquipmentReport: Fetched %d equipment.', $reportData->total()), ['admin_user_id' => Auth::id()]);

        return $reportData;
    }

    // Filter options as computed properties for use in the Blade view
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
        return Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function getLocationOptionsProperty(): \Illuminate\Support\Collection
    {
        return Location::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function getCategoryOptionsProperty(): \Illuminate\Support\Collection
    {
        return EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Livewire lifecycle hook for when a public property is updated.
     * Resets pagination when filters or search are updated.
     */
    public function updating($property): void
    {
        if (
            in_array($property, [
                'filterAssetType', 'filterStatus', 'filterCondition',
                'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm',
            ])
        ) {
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
            $this->sortBy        = $column;
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
            'sortBy', 'sortDirection',
        ]);
        $this->sortBy        = 'tag_id';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    /**
     * Render the Blade view for the equipment report.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.reports.equipment-report', [
            'reportData'             => $this->getReportDataProperty(),
            'assetTypeOptions'       => $this->getAssetTypeOptionsProperty(),
            'statusOptions'          => $this->getStatusOptionsProperty(),
            'conditionStatusOptions' => $this->getConditionStatusOptionsProperty(),
            'departmentOptions'      => $this->getDepartmentOptionsProperty(),
            'locationOptions'        => $this->getLocationOptionsProperty(),
            'categoryOptions'        => $this->getCategoryOptionsProperty(),
        ]);
    }
}
