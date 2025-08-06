<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory; // Added based on Revision 3 for equipment
use App\Models\Location; // Added based on Revision 3
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // Corrected: Added use statement for Title attribute
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Laporan Peralatan ICT')] // Added Livewire Title attribute
class EquipmentReport extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Filter properties
    public ?string $filterAssetType = '';

    public ?string $filterStatus = '';

    public ?string $filterCondition = '';

    public ?int $filterDepartmentId = null;

    public ?int $filterLocationId = null;       // Added from Revision 3

    public ?int $filterCategoryId = null;       // Added from Revision 3

    public string $searchTerm = ''; // Search by tag, serial, model, brand, item_code

    // Sorting properties
    public string $sortBy = 'tag_id';

    public string $sortDirection = 'asc';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 15;

    public function mount(): void
    {
        // $this->authorize('viewAny', Equipment::class); // Example policy
        Log::info("Livewire\EquipmentReport: Generating Equipment Report page.", [
            'admin_user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function getReportDataProperty()
    {
        // Revision 3 Equipment fields: asset_type, brand, model, serial_number, tag_id, status, condition_status, department_id, location_id, equipment_category_id, item_code
        $query = Equipment::with([
            'department:id,name',
            'creator:id,name',
            'location:id,name', // Corrected: Assuming 'location' is the relationship name in Equipment model
            'equipmentCategory:id,name', // Assuming relation name
        ]);

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $search = '%'.strtolower($this->searchTerm).'%';
            $query->where(function ($q) use ($search): void {
                $q->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(model) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(brand) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(item_code) LIKE ?', [$search]); // Added from Revision 3
            });
        }

        if ($this->filterAssetType !== null && $this->filterAssetType !== '' && $this->filterAssetType !== '0') {
            $query->where('asset_type', $this->filterAssetType);
        }

        if ($this->filterStatus !== null && $this->filterStatus !== '' && $this->filterStatus !== '0') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCondition !== null && $this->filterCondition !== '' && $this->filterCondition !== '0') {
            $query->where('condition_status', $this->filterCondition);
        }

        if ($this->filterDepartmentId !== null && $this->filterDepartmentId !== 0) {
            $query->where('department_id', $this->filterDepartmentId);
        }

        if ($this->filterLocationId !== null && $this->filterLocationId !== 0) {
            $query->where('location_id', $this->filterLocationId); // Added from Revision 3
        }

        if ($this->filterCategoryId !== null && $this->filterCategoryId !== 0) {
            $query->where('equipment_category_id', $this->filterCategoryId); // Added from Revision 3
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info(sprintf('Livewire\EquipmentReport: Fetched %d equipment.', $reportData->total()), ['admin_user_id' => Auth::id()]);

        return $reportData;
    }

    // Options for filters
    public function getAssetTypeOptionsProperty(): array
    {
        return Equipment::getAssetTypeOptions(); // Corrected: Call static method
    }

    public function getStatusOptionsProperty(): array
    {
        return Equipment::getStatusOptions(); // Corrected: Call static method
    }

    public function getConditionStatusOptionsProperty(): array
    {
        return Equipment::getConditionStatusesList(); // Corrected: Call static method
    }

    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function getLocationOptionsProperty(): \Illuminate\Support\Collection
    {
        return Location::where('is_active', true)->orderBy('name')->pluck('name', 'id'); // Added from Revision 3
    }

    public function getCategoryOptionsProperty(): \Illuminate\Support\Collection
    {
        return EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id'); // Added from Revision 3
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function updating($property): void
    {
        if (in_array($property, ['filterAssetType', 'filterStatus', 'filterCondition', 'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm'])) {
            $this->resetPage();
        }
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

    public function resetFilters(): void
    {
        $this->reset(['filterAssetType', 'filterStatus', 'filterCondition', 'filterDepartmentId', 'filterLocationId', 'filterCategoryId', 'searchTerm', 'sortBy', 'sortDirection']);
        $this->sortBy = 'tag_id'; // Default sort
        $this->sortDirection = 'asc'; // Default direction
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
            'locationOptions' => $this->locationOptionsProperty,     // Added
            'categoryOptions' => $this->categoryOptionsProperty,     // Added
        ]);
    }
}
