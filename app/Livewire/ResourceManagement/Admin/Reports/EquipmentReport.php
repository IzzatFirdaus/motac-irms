<?php

namespace App\Livewire\ResourceManagement\Admin\Reports;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Location; // Added based on Revision 3 for equipment
use App\Models\EquipmentCategory; // Added based on Revision 3
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
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

    public function mount()
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
            'definedLocation:id,name', // Assuming relation name in Equipment model for location_id
            'equipmentCategory:id,name' // Assuming relation name
        ]);

        if (!empty($this->searchTerm)) {
            $search = '%' . strtolower($this->searchTerm) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(tag_id) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(serial_number) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(model) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(brand) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(item_code) LIKE ?', [$search]); // Added from Revision 3
            });
        }
        if (!empty($this->filterAssetType)) {
            $query->where('asset_type', $this->filterAssetType);
        }
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        if (!empty($this->filterCondition)) {
            $query->where('condition_status', $this->filterCondition);
        }
        if (!empty($this->filterDepartmentId)) {
            $query->where('department_id', $this->filterDepartmentId);
        }
        if (!empty($this->filterLocationId)) {
            $query->where('location_id', $this->filterLocationId); // Added from Revision 3
        }
        if (!empty($this->filterCategoryId)) {
            $query->where('equipment_category_id', $this->filterCategoryId); // Added from Revision 3
        }


        $query->orderBy($this->sortBy, $this->sortDirection);

        $reportData = $query->paginate($this->perPage);
        Log::info("Livewire\EquipmentReport: Fetched {$reportData->total()} equipment.", ['admin_user_id' => Auth::id()]);
        return $reportData;
    }

    // Options for filters
    public function getAssetTypeOptionsProperty(): array
    {
        return Equipment::$ASSET_TYPES_LABELS ?? (defined(Equipment::class . '::ASSET_TYPE_LAPTOP') ? Equipment::getAssetTypeOptions() : []);
    }
    public function getStatusOptionsProperty(): array
    {
        return Equipment::$STATUSES_LABELS ?? (defined(Equipment::class . '::STATUS_AVAILABLE') ? Equipment::getStatusOptions() : []);
    }
    public function getConditionStatusOptionsProperty(): array
    {
        return Equipment::$CONDITION_STATUSES_LABELS ?? (defined(Equipment::class . '::CONDITION_NEW') ? Equipment::getConditionStatusOptions() : []);
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
        ])->title(__('Laporan Peralatan ICT'));
    }
}
