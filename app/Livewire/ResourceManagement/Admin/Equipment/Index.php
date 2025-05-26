<?php

namespace App\Livewire\ResourceManagement\Admin\Equipment;

use App\Models\Equipment;
use App\Models\Department; // For department filter/selection
// use App\Models\User; // For created_by/updated_by names, not directly instantiated
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
// use App\Helpers\Helpers; // For status colors (used in Blade)
use Illuminate\View\View;
use Illuminate\Validation\Rule as ValidationRule; // Aliased

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $searchTerm = '';
    public string $filterAssetType = '';
    public string $filterStatus = '';
    public string $filterCondition = '';
    public ?int $filterDepartmentId = null;

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?Equipment $editingEquipment = null;
    public ?Equipment $deletingEquipment = null;

    // Form fields for create/edit
    public string $asset_type = '';
    public ?string $brand = null;
    public ?string $model_name = null; // Using model_name to avoid conflict with Eloquent's $model
    public string $serial_number = '';
    public string $tag_id = '';
    public ?string $purchase_date = null;
    public ?string $warranty_expiry_date = null;
    public string $status = '';
    public ?string $current_location = null;
    public ?string $notes = null;
    public string $condition_status = '';
    public ?int $department_id = null;

    protected string $paginationTheme = 'bootstrap'; // Updated to Bootstrap

    public function mount(): void
    {
        $this->authorize('viewAny', Equipment::class);
        $this->editingEquipment = new Equipment(); // For create form binding
        // Set default values for new equipment
        if (defined(Equipment::class . '::STATUS_AVAILABLE')) {
            $this->status = Equipment::STATUS_AVAILABLE;
        }
        if (defined(Equipment::class . '::CONDITION_GOOD')) {
            $this->condition_status = Equipment::CONDITION_GOOD;
        }
    }

    // Computed property for equipment list
    public function getEquipmentListProperty()
    {
        $query = Equipment::with(['department:id,name', 'creator:id,name', 'updater:id,name'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('tag_id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('model', 'like', '%' . $this->searchTerm . '%'); // search 'model' column
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

        return $query->paginate(10);
    }

    // Computed properties for filter options
    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

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

    // Validation rules defined as a method
    protected function formRules(bool $isEditMode = false, ?int $equipmentId = null): array
    {
        return [
            'asset_type' => ['required', 'string', 'max:255', ValidationRule::in(array_keys($this->assetTypeOptionsProperty))],
            'brand' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
            'serial_number' => ['required', 'string', 'max:255', $isEditMode ? ValidationRule::unique('equipment', 'serial_number')->ignore($equipmentId) : ValidationRule::unique('equipment', 'serial_number')],
            'tag_id' => ['required', 'string', 'max:255', $isEditMode ? ValidationRule::unique('equipment', 'tag_id')->ignore($equipmentId) : ValidationRule::unique('equipment', 'tag_id')],
            'purchase_date' => 'nullable|date_format:Y-m-d',
            'warranty_expiry_date' => 'nullable|date_format:Y-m-d|after_or_equal:purchase_date',
            'status' => ['required', 'string', ValidationRule::in(array_keys($this->statusOptionsProperty))],
            'current_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'condition_status' => ['required', 'string', ValidationRule::in(array_keys($this->conditionStatusOptionsProperty))],
            'department_id' => 'nullable|exists:departments,id',
        ];
    }


    public function openCreateModal(): void
    {
        $this->authorize('create', Equipment::class);
        $this->resetForm();
        // Defaults are set in resetForm or mount
        $this->showCreateModal = true;
    }

    public function createEquipment(): void
    {
        $this->authorize('create', Equipment::class);
        $validated = $this->validate($this->formRules());
        $validatedData = $validated;
        $validatedData['model'] = $this->model_name; // Map model_name back to model
        unset($validatedData['model_name']);

        Equipment::create($validatedData);
        $this->dispatch('toastr', type: 'success', message: __('Peralatan ICT berjaya ditambah.'));
        $this->closeModal();
    }

    public function openEditModal(Equipment $equipment): void
    {
        $this->authorize('update', $equipment);
        $this->resetForm();
        $this->editingEquipment = $equipment;

        $this->asset_type = $equipment->asset_type;
        $this->brand = $equipment->brand;
        $this->model_name = $equipment->model; // Populate model_name from equipment's model field
        $this->serial_number = $equipment->serial_number;
        $this->tag_id = $equipment->tag_id;
        $this->purchase_date = $equipment->purchase_date ? ($equipment->purchase_date instanceof \Carbon\Carbon ? $equipment->purchase_date->format('Y-m-d') : $equipment->purchase_date) : null;
        $this->warranty_expiry_date = $equipment->warranty_expiry_date ? ($equipment->warranty_expiry_date instanceof \Carbon\Carbon ? $equipment->warranty_expiry_date->format('Y-m-d') : $equipment->warranty_expiry_date) : null;
        $this->status = $equipment->status;
        $this->current_location = $equipment->current_location;
        $this->notes = $equipment->notes;
        $this->condition_status = $equipment->condition_status;
        $this->department_id = $equipment->department_id;

        $this->showEditModal = true;
    }

    public function updateEquipment(): void
    {
        if (!$this->editingEquipment || !$this->editingEquipment->exists) {
             $this->dispatch('toastr', type: 'error', message: __('Ralat: Tiada peralatan dipilih untuk dikemaskini.'));
            return;
        }
        $this->authorize('update', $this->editingEquipment);
        $validated = $this->validate($this->formRules(true, $this->editingEquipment->id));
        $validatedData = $validated;
        $validatedData['model'] = $this->model_name; // Map model_name back to model
        unset($validatedData['model_name']);

        $this->editingEquipment->update($validatedData);
        $this->dispatch('toastr', type: 'success', message: __('Maklumat peralatan ICT berjaya dikemaskini.'));
        $this->closeModal();
    }

    public function openDeleteModal(Equipment $equipment): void
    {
        $this->authorize('delete', $equipment);
        $this->deletingEquipment = $equipment;
        $this->showDeleteModal = true;
    }

    public function deleteEquipment(): void
    {
        if (!$this->deletingEquipment) return;
        $this->authorize('delete', $this->deletingEquipment);

        try {
            // Check for related loan application items or transaction items before deleting
            if ($this->deletingEquipment->loanApplicationItems()->exists() || $this->deletingEquipment->loanTransactionItems()->exists()) {
                 $this->dispatch('toastr', type: 'error', message: __('Peralatan ini tidak boleh dipadam kerana mempunyai rekod pinjaman berkaitan.'));
            } else {
                $this->deletingEquipment->delete();
                $this->dispatch('toastr', type: 'success', message: __('Peralatan ICT berjaya dipadam.'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error("Error deleting equipment ID {$this->deletingEquipment->id}: {$e->getMessage()}");
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1451 || str_contains(strtolower($e->getMessage()), 'foreign key constraint')) {
                $this->dispatch('toastr', type: 'error', message: __('Peralatan ini tidak boleh dipadam kerana mempunyai rekod berkaitan (cth: dalam transaksi pinjaman).'));
            } else {
                $this->dispatch('toastr', type: 'error', message: __('Gagal memadam peralatan ICT. Sila hubungi pentadbir.'));
            }
        } catch (\Exception $e) {
            Log::error("General error deleting equipment ID {$this->deletingEquipment->id}: {$e->getMessage()}");
            $this->dispatch('toastr', type: 'error', message: __('Gagal memadam peralatan ICT disebabkan ralat tidak dijangka.'));
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetErrorBag(); // Clear validation errors
    }

    private function resetForm(): void
    {
        $this->asset_type = '';
        $this->brand = null;
        $this->model_name = null;
        $this->serial_number = '';
        $this->tag_id = '';
        $this->purchase_date = null;
        $this->warranty_expiry_date = null;
        $this->current_location = null;
        $this->notes = null;
        $this->department_id = null;

        if (defined(Equipment::class . '::STATUS_AVAILABLE')) {
            $this->status = Equipment::STATUS_AVAILABLE;
        } else { $this->status = ''; }
        if (defined(Equipment::class . '::CONDITION_GOOD')) {
            $this->condition_status = Equipment::CONDITION_GOOD;
        } else { $this->condition_status = ''; }

        $this->editingEquipment = new Equipment(); // Fresh model for potential creation
        $this->deletingEquipment = null;
    }

    // Reset pagination when filters change
    public function updatingSearchTerm(): void { $this->resetPage(); }
    public function updatedFilterAssetType(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedFilterCondition(): void { $this->resetPage(); }
    public function updatedFilterDepartmentId(): void { $this->resetPage(); }

    public function render(): View
    {
        return view('livewire.resource-management.admin.equipment.index', [
            'equipmentList' => $this->equipmentListProperty,
            'departmentOptions' => $this->departmentOptionsProperty,
            'assetTypeOptions' => $this->assetTypeOptionsProperty,
            'statusOptions' => $this->statusOptionsProperty,
            'conditionStatusOptions' => $this->conditionStatusOptionsProperty,
        ])->title(__('Pengurusan Peralatan ICT'));
    }
}
