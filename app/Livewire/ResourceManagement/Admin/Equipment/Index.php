<?php

namespace App\Livewire\ResourceManagement\Admin\Equipment;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\LoanTransactionItem; // Correctly import the model
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // Import the Title attribute
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pengurusan Peralatan ICT')] // Use a literal string for the attribute
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $filterAssetType = '';

    public string $filterStatus = '';

    public string $filterCondition = '';

    public ?int $filterDepartmentId = null;

    // Sorting properties
    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public bool $showViewModal = false;

    public ?Equipment $editingEquipment = null;

    public ?Equipment $deletingEquipment = null;

    public ?Equipment $viewingEquipment = null;

    // Form fields for create/edit modals
    public string $asset_type = '';

    public ?string $brand = null;

    public ?string $model_name = null; // Maps to Equipment 'model'

    public string $serial_number = '';

    public string $tag_id = '';

    public ?string $purchase_date = null;

    public ?string $warranty_expiry_date = null;

    public string $status = '';

    public ?string $current_location = null;

    public ?string $notes = null;

    public string $condition_status = '';

    public ?int $department_id = null;

    public ?string $item_code = null;

    public ?string $description = null;

    public ?float $purchase_price = null;

    public ?string $acquisition_type = null;

    public ?string $classification = null;

    public ?string $funded_by = null;

    public ?string $supplier_name = null;

    public ?array $specifications = null;

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', Equipment::class);
        $this->editingEquipment = new Equipment;
        if (defined(Equipment::class.'::STATUS_AVAILABLE')) {
            $this->status = Equipment::STATUS_AVAILABLE;
        }

        if (defined(Equipment::class.'::CONDITION_GOOD')) {
            $this->condition_status = Equipment::CONDITION_GOOD;
        }

        // If you still want the title to be dynamically translatable,
        // you could dispatch an event here to your layout file:
        // $this->dispatch('update-page-title', title: __('Pengurusan Peralatan ICT'));
        // Your layout would then need to listen for this event.
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['tag_id', 'asset_type', 'brand', 'model', 'status', 'condition_status', 'created_at'])) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function getEquipmentListProperty()
    {
        $query = Equipment::with([
            'department:id,name',
            'creator:id,name',
            'updater:id,name',
            'activeLoanTransactionItem.loanTransaction.loanApplication.user:id,name',
        ]);

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $query->where(function ($q): void {
                $q->where('tag_id', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('serial_number', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('brand', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('model', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('item_code', 'like', '%'.$this->searchTerm.'%');
            });
        }

        if ($this->filterAssetType !== '' && $this->filterAssetType !== '0') {
            $query->where('asset_type', $this->filterAssetType);
        }

        if ($this->filterStatus !== '' && $this->filterStatus !== '0') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCondition !== '' && $this->filterCondition !== '0') {
            $query->where('condition_status', $this->filterCondition);
        }

        if ($this->filterDepartmentId !== null && $this->filterDepartmentId !== 0) {
            $query->where('department_id', $this->filterDepartmentId);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    public function getDepartmentOptionsProperty(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->pluck('name', 'id');
    }

    public function getAssetTypeOptionsProperty(): array
    {
        return defined(Equipment::class.'::$ASSET_TYPES_LABELS') ? Equipment::$ASSET_TYPES_LABELS : [];
    }

    public function getStatusOptionsProperty(): array
    {
        return defined(Equipment::class.'::$STATUSES_LABELS') ? Equipment::$STATUSES_LABELS : [];
    }

    public function getConditionStatusOptionsProperty(): array
    {
        return defined(Equipment::class.'::$CONDITION_STATUSES_LABELS') ? Equipment::$CONDITION_STATUSES_LABELS : [];
    }

    public function getAcquisitionTypeOptionsProperty(): array
    {
        return defined(Equipment::class.'::$ACQUISITION_TYPES_LABELS') ? Equipment::$ACQUISITION_TYPES_LABELS : [];
    }

    public function getClassificationOptionsProperty(): array
    {
        return defined(Equipment::class.'::$CLASSIFICATION_LABELS') ? Equipment::$CLASSIFICATION_LABELS : [];
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Equipment::class);
        $this->resetForm();
        $this->editingEquipment = new Equipment;
        $this->status = Equipment::STATUS_AVAILABLE;
        $this->condition_status = Equipment::CONDITION_GOOD;
        $this->showEditModal = false;
        $this->showCreateModal = true;
        $this->dispatch('open-modal', modalId: 'equipmentFormModal');
    }

    public function createEquipment(): void
    {
        $this->authorize('create', Equipment::class);
        $validated = $this->validate($this->formRules(false));

        $validatedData = $validated;
        $validatedData['model'] = $this->model_name;
        $fillableFields = (new Equipment)->getFillable();
        foreach ($fillableFields as $field) {
            if (! (property_exists($this, $field) && ! isset($validatedData[$field]))) {
                continue;
            }
            if ($field === 'model_name') {
                continue;
            }
            if ($this->$field === null) {
                continue;
            }
            $validatedData[$field] = $this->$field;
        }

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
        $this->model_name = $equipment->model;
        $this->serial_number = $equipment->serial_number;
        $this->tag_id = $equipment->tag_id;
        $this->purchase_date = $equipment->purchase_date ? ($equipment->purchase_date instanceof \Carbon\Carbon ? $equipment->purchase_date->format('Y-m-d') : $equipment->purchase_date) : null;
        $this->warranty_expiry_date = $equipment->warranty_expiry_date ? ($equipment->warranty_expiry_date instanceof \Carbon\Carbon ? $equipment->warranty_expiry_date->format('Y-m-d') : $equipment->warranty_expiry_date) : null;
        $this->status = $equipment->status;
        $this->current_location = $equipment->current_location;
        $this->notes = $equipment->notes;
        $this->condition_status = $equipment->condition_status;
        $this->department_id = $equipment->department_id;

        $this->item_code = $equipment->item_code;
        $this->description = $equipment->description;
        $this->purchase_price = $equipment->purchase_price;
        $this->acquisition_type = $equipment->acquisition_type;
        $this->classification = $equipment->classification;
        $this->funded_by = $equipment->funded_by;
        $this->supplier_name = $equipment->supplier_name;
        $this->specifications = $equipment->specifications;

        $this->showCreateModal = false;
        $this->showEditModal = true;
        $this->dispatch('open-modal', modalId: 'equipmentFormModal');
    }

    public function updateEquipment(): void
    {
        if (! $this->editingEquipment instanceof \App\Models\Equipment || ! $this->editingEquipment->exists) {
            $this->dispatch('toastr', type: 'error', message: __('Ralat: Tiada peralatan dipilih untuk dikemaskini.'));

            return;
        }

        $this->authorize('update', $this->editingEquipment);
        $validated = $this->validate($this->formRules(true, $this->editingEquipment->id));

        $validatedData = $validated;
        $validatedData['model'] = $this->model_name;
        $fillableFields = (new Equipment)->getFillable();
        foreach ($fillableFields as $field) {
            if (property_exists($this, $field) && ! isset($validatedData[$field]) && $field !== 'model_name') {
                $validatedData[$field] = $this->$field;
            }
        }

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
        $this->dispatch('open-modal', modalId: 'deleteConfirmationModal');
    }

    public function deleteEquipment(): void
    {
        if (! $this->deletingEquipment instanceof \App\Models\Equipment) {
            $this->dispatch('toastr', type: 'error', message: __('Tiada peralatan dipilih untuk dipadam.'));

            return;
        }

        $this->authorize('delete', $this->deletingEquipment);

        try {
            if ($this->deletingEquipment->loanTransactionItems()->exists()) {
                $activeLoan = $this->deletingEquipment->loanTransactionItems()
                    ->whereNotIn('status', [
                        LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
                        LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
                        LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
                        LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
                        LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
                    ])->exists();
                if ($activeLoan) {
                    $this->dispatch('toastr', type: 'error', message: __('Peralatan ini tidak boleh dipadam kerana mempunyai rekod pinjaman aktif atau item yang belum dipulangkan sepenuhnya.'));
                    $this->closeModal();

                    return;
                }
            }

            $this->deletingEquipment->delete();
            $this->dispatch('toastr', type: 'success', message: __('Peralatan ICT berjaya dipadam.'));
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error(sprintf('Error deleting equipment ID %d: %s', $this->deletingEquipment->id, $e->getMessage()));
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1451 || str_contains(strtolower($e->getMessage()), 'foreign key constraint')) {
                $this->dispatch('toastr', type: 'error', message: __('Peralatan ini tidak boleh dipadam kerana mempunyai rekod berkaitan (cth: dalam transaksi pinjaman lampau). Sila pastikan semua rekod berkaitan telah diarkib atau dialih.'));
            } else {
                $this->dispatch('toastr', type: 'error', message: __('Gagal memadam peralatan ICT. Sila hubungi pentadbir. Error: '.$e->getMessage()));
            }
        } catch (\Exception $e) {
            Log::error(sprintf('General error deleting equipment ID %d: %s', $this->deletingEquipment->id, $e->getMessage()));
            $this->dispatch('toastr', type: 'error', message: __('Gagal memadam peralatan ICT disebabkan ralat tidak dijangka.'));
        }

        $this->closeModal();
    }

    public function openViewModal(Equipment $equipment): void
    {
        $this->authorize('view', $equipment);
        $this->viewingEquipment = $equipment->loadMissing([
            'department:id,name',
            'creator:id,name',
            'updater:id,name',
            'equipmentCategory:id,name',
            'subCategory:id,name',
            'definedLocation:id,name',
            'activeLoanTransactionItem.loanTransaction.loanApplication.user:id,name',
        ]);
        $this->showViewModal = true;
        $this->dispatch('open-modal', modalId: 'viewEquipmentModal');
    }

    public function closeModal(): void
    {
        if ($this->showCreateModal || $this->showEditModal) {
            $this->dispatch('close-modal', modalId: 'equipmentFormModal');
        }

        if ($this->showDeleteModal) {
            $this->dispatch('close-modal', modalId: 'deleteConfirmationModal');
        }

        if ($this->showViewModal) {
            $this->dispatch('close-modal', modalId: 'viewEquipmentModal');
        }

        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->showViewModal = false;

        $this->resetForm();
        $this->editingEquipment = new Equipment;
        $this->deletingEquipment = null;
        $this->viewingEquipment = null;
        $this->resetErrorBag();
    }

    public function resetFilters(): void
    {
        $this->searchTerm = '';
        $this->filterAssetType = '';
        $this->filterStatus = '';
        $this->filterCondition = '';
        $this->filterDepartmentId = null;
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAssetType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCondition(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDepartmentId(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.admin.equipment.index', [
            'equipmentList' => $this->equipmentList,
            'departmentOptions' => $this->departmentOptions,
            'assetTypeOptions' => $this->assetTypeOptions,
            'statusOptions' => $this->statusOptions,
            'conditionStatusOptions' => $this->conditionStatusOptions,
        ]);
        // If you opt to set title via render():
        // ->title(__('Pengurusan Peralatan ICT'));
        // However, ensure this doesn't re-introduce the constant expression error.
        // The #[Title] attribute with a literal string is safer for compile-time evaluation.
    }

    protected function formRules(bool $isEditMode = false, ?int $equipmentIdToIgnore = null): array
    {
        $assetTypeKeys = array_keys(Equipment::getAssetTypeOptions());
        $statusKeys = array_keys(Equipment::getOperationalStatusesList());
        $conditionStatusKeys = array_keys(Equipment::getConditionStatusesList());
        $acquisitionTypeKeys = array_keys(Equipment::getAcquisitionTypeOptions());
        $classificationKeys = array_keys(Equipment::getClassificationOptions());

        return [
            'asset_type' => ['required', 'string', 'max:50', ValidationRule::in($assetTypeKeys)],
            'brand' => 'nullable|string|max:100',
            'model_name' => 'nullable|string|max:100',
            'serial_number' => ['nullable', 'string', 'max:100', $isEditMode ? ValidationRule::unique('equipment', 'serial_number')->ignore($equipmentIdToIgnore) : ValidationRule::unique('equipment', 'serial_number')],
            'tag_id' => ['required', 'string', 'max:50', $isEditMode ? ValidationRule::unique('equipment', 'tag_id')->ignore($equipmentIdToIgnore) : ValidationRule::unique('equipment', 'tag_id')],
            'item_code' => ['nullable', 'string', 'max:50', $isEditMode ? ValidationRule::unique('equipment', 'item_code')->ignore($equipmentIdToIgnore) : ValidationRule::unique('equipment', 'item_code')],
            'description' => 'nullable|string|max:1000',
            'purchase_date' => 'nullable|date_format:Y-m-d',
            'purchase_price' => 'nullable|numeric|min:0|max:9999999.99',
            'warranty_expiry_date' => 'nullable|date_format:Y-m-d|after_or_equal:purchase_date',
            'status' => ['required', 'string', ValidationRule::in($statusKeys)],
            'condition_status' => ['required', 'string', ValidationRule::in($conditionStatusKeys)],
            'current_location' => 'nullable|string|max:255',
            'acquisition_type' => ['nullable', 'string', ValidationRule::in($acquisitionTypeKeys)],
            'classification' => ['nullable', 'string', ValidationRule::in($classificationKeys)],
            'funded_by' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'specifications' => 'nullable|array',
            'department_id' => 'nullable|integer|exists:departments,id',
        ];
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

        $this->item_code = null;
        $this->description = null;
        $this->purchase_price = null;
        $this->acquisition_type = null;
        $this->classification = null;
        $this->funded_by = null;
        $this->supplier_name = null;
        $this->specifications = null;

        if (defined(Equipment::class.'::STATUS_AVAILABLE')) {
            $this->status = Equipment::STATUS_AVAILABLE;
        } else {
            $this->status = '';
        }

        if (defined(Equipment::class.'::CONDITION_GOOD')) {
            $this->condition_status = Equipment::CONDITION_GOOD;
        } else {
            $this->condition_status = '';
        }

        $this->editingEquipment = new Equipment;
        $this->deletingEquipment = null;
        $this->resetErrorBag();
    }
}
