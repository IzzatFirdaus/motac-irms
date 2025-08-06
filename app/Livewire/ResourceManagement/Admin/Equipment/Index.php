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

    // Add this line to declare the $isEditing property
    public bool $isEditing = false;

    // Form fields for create/edit modals
    public string $asset_type = '';

    public ?string $brand = null;

    public ?string $model_name = null; // Maps to Equipment->model

    public string $serial_number = '';

    public string $tag_id = '';

    public ?string $purchase_date = null;

    public ?string $warranty_expiry_date = null;

    public string $status = '';

    public ?string $current_location = null;

    public ?string $notes = null;

    public string $condition_status = '';

    public ?int $department_id = null;

    // Additional fields from System Design
    public ?string $item_code = null; // New field from system design
    public ?string $description = null; // New field from system design, distinct from notes
    public ?float $purchase_price = null; // New field from system design
    public ?string $acquisition_type = null; // New field from system design (e.g., 'purchase', 'donation')
    public ?string $classification = null; // New field from system design (e.g., 'ICT', 'Non-ICT')
    public ?string $funded_by = null; // New field from system design
    public ?string $supplier_name = null; // New field from system design
    public ?array $specifications = null; // New field for JSON specifications

    // Dropdown options
    public array $assetTypeOptions = [];

    public array $statusOptions = [];

    public array $conditionStatusOptions = [];

    public array $departmentOptions = [];

    public array $acquisitionTypeOptions = [];

    public array $classificationOptions = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Equipment::class);

        // Populate dropdown options from the Equipment model constants
        $this->assetTypeOptions = Equipment::getAssetTypeOptions();
        $this->statusOptions = Equipment::getStatusOptions();
        $this->conditionStatusOptions = Equipment::getConditionStatusesList(); // Using the correct method from Equipment model
        $this->acquisitionTypeOptions = Equipment::getAcquisitionTypeOptions(); // Assuming this method exists
        $this->classificationOptions = Equipment::getClassificationOptions(); // Assuming this method exists

        // Department options for filter and form
        $this->departmentOptions = Department::orderBy('name')->pluck('name', 'id')->toArray();

        // Initialize form fields for creation
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'asset_type' => ['required', 'string', ValidationRule::in(array_keys($this->assetTypeOptions))],
            'brand' => ['nullable', 'string', 'max:255'],
            'model_name' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', ValidationRule::unique('equipment', 'serial_number')->ignore($this->editingEquipment?->id)],
            'tag_id' => ['required', 'string', 'max:255', ValidationRule::unique('equipment', 'tag_id')->ignore($this->editingEquipment?->id)],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'warranty_expiry_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:purchase_date'],
            'status' => ['required', 'string', ValidationRule::in(array_keys($this->statusOptions))],
            'current_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'condition_status' => ['required', 'string', ValidationRule::in(array_keys($this->conditionStatusOptions))],
            'item_code' => ['nullable', 'string', 'max:50', ValidationRule::unique('equipment', 'item_code')->ignore($this->editingEquipment?->id)], // Validate uniqueness
            'description' => 'nullable|string|max:1000',
            'purchase_price' => 'nullable|numeric|min:0',
            'acquisition_type' => ['nullable', 'string', ValidationRule::in(array_keys($this->acquisitionTypeOptions))],
            'classification' => ['nullable', 'string', ValidationRule::in(array_keys($this->classificationOptions))],
            'funded_by' => 'nullable|string|max:100',
            'supplier_name' => 'nullable|string|max:100',
            'specifications' => 'nullable|array',
            'department_id' => 'nullable|integer|exists:departments,id',
        ];
    }

    public function getEquipmentProperty()
    {
        $query = Equipment::query()
            ->with(['department']) // Eager load department
            ->search($this->searchTerm) // Use the search scope
            ->when($this->filterAssetType, fn ($q) => $q->where('asset_type', $this->filterAssetType))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCondition, fn ($q) => $q->where('condition_status', $this->filterCondition))
            ->when($this->filterDepartmentId, fn ($q) => $q->where('department_id', $this->filterDepartmentId));

        // Apply sorting
        if (in_array($this->sortField, ['tag_id', 'asset_type', 'brand', 'model', 'serial_number', 'status', 'condition_status', 'created_at'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('created_at', 'desc'); // Default sort
        }

        return $query->paginate(10);
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAssetType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCondition(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDepartmentId(): void
    {
        $this->resetPage();
    }

    public function createEquipment(): void
    {
        $this->authorize('create', Equipment::class);
        $this->resetForm();
        $this->showCreateModal = true;
        $this->dispatch('open-modal', elementId: '#createEditEquipmentModal');
        Log::info('Opened create equipment modal.');
    }

    public function storeEquipment(): void
    {
        $this->authorize('create', Equipment::class);
        $validatedData = $this->validate();

        try {
            // Remove 'model_name' from validated data before creating, as 'model' is the actual column
            $validatedData['model'] = $validatedData['model_name'];
            unset($validatedData['model_name']);

            // Handle specifications if provided
            if (isset($validatedData['specifications']) && is_array($validatedData['specifications'])) {
                $validatedData['specifications'] = json_encode($validatedData['specifications']);
            } else {
                $validatedData['specifications'] = null;
            }

            Equipment::create($validatedData);
            session()->flash('success', __('Peralatan ICT berjaya ditambah.'));
            Log::info('Equipment created successfully.', ['tag_id' => $validatedData['tag_id']]);
            $this->closeModals();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during equipment creation.', ['errors' => $e->errors()]);
            throw $e; // Re-throw to show validation errors on the form
        } catch (\Exception $e) {
            session()->flash('error', __('Gagal menambah peralatan ICT: ').$e->getMessage());
            Log::error('Failed to create equipment: '.$e->getMessage(), ['exception' => $e]);
        }
    }

    public function editEquipment(Equipment $equipment): void
    {
        $this->authorize('update', $equipment);
        $this->editingEquipment = $equipment;
        $this->isEditing = true; // This line should now work correctly
        $this->populateFields();
        $this->showEditModal = true;
        $this->dispatch('open-modal', elementId: '#createEditEquipmentModal');
        Log::info('Opened edit equipment modal for ID: '.$equipment->id);
    }

    public function updateEquipment(): void
    {
        $this->authorize('update', $this->editingEquipment);

        $validatedData = $this->validate();

        try {
            // Remove 'model_name' from validated data before updating, as 'model' is the actual column
            $validatedData['model'] = $validatedData['model_name'];
            unset($validatedData['model_name']);

            // Handle specifications if provided
            if (isset($validatedData['specifications']) && is_array($validatedData['specifications'])) {
                $validatedData['specifications'] = json_encode($validatedData['specifications']);
            } else {
                $validatedData['specifications'] = null;
            }

            $this->editingEquipment->update($validatedData);
            session()->flash('success', __('Maklumat peralatan ICT berjaya dikemaskini.'));
            Log::info('Equipment updated successfully.', ['tag_id' => $this->editingEquipment->tag_id]);
            $this->closeModals();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during equipment update.', ['errors' => $e->errors(), 'equipment_id' => $this->editingEquipment->id]);
            throw $e; // Re-throw to show validation errors on the form
        } catch (\Exception $e) {
            session()->flash('error', __('Gagal mengemaskini maklumat peralatan ICT: ').$e->getMessage());
            Log::error('Failed to update equipment ID: '.$this->editingEquipment->id.' error: '.$e->getMessage(), ['exception' => $e]);
        }
    }

    public function confirmDeleteEquipment(Equipment $equipment): void
    {
        $this->authorize('delete', $equipment);
        $this->deletingEquipment = $equipment;
        $this->showDeleteModal = true;
        $this->dispatch('open-modal', elementId: '#deleteEquipmentModal');
        Log::info('Opened delete confirmation modal for equipment ID: '.$equipment->id);
    }

    public function deleteEquipment(): void
    {
        $this->authorize('delete', $this->deletingEquipment);

        if ($this->deletingEquipment->status === Equipment::STATUS_LOANED) { // Changed STATUS_ON_LOAN to STATUS_LOANED
            session()->flash('error', __('Peralatan tidak boleh dipadam kerana sedang dalam pinjaman.'));
            Log::warning('Attempted to delete equipment on loan.', ['equipment_id' => $this->deletingEquipment->id]);
            $this->closeModals();

            return;
        }

        try {
            $this->deletingEquipment->delete();
            session()->flash('success', __('Peralatan ICT berjaya dipadam.'));
            Log::info('Equipment deleted successfully.', ['equipment_id' => $this->deletingEquipment->id]);
            $this->closeModals();
        } catch (\Exception $e) {
            session()->flash('error', __('Gagal memadam peralatan ICT: ').$e->getMessage());
            Log::error('Failed to delete equipment ID: '.$this->deletingEquipment->id.' error: '.$e->getMessage(), ['exception' => $e]);
        }
    }

    public function viewEquipment(Equipment $equipment): void
    {
        $this->authorize('view', $equipment);
        $this->viewingEquipment = $equipment;
        $this->showViewModal = true;
        $this->dispatch('open-modal', elementId: '#viewEquipmentModal');
        Log::info('Opened view equipment modal for ID: '.$equipment->id);
    }

    private function populateFields(): void
    {
        if ($this->editingEquipment) {
            $this->asset_type = $this->editingEquipment->asset_type;
            $this->brand = $this->editingEquipment->brand;
            $this->model_name = $this->editingEquipment->model; // Map 'model' column to 'model_name' property
            $this->serial_number = $this->editingEquipment->serial_number;
            $this->tag_id = $this->editingEquipment->tag_id;
            $this->purchase_date = $this->editingEquipment->purchase_date?->format('Y-m-d');
            $this->warranty_expiry_date = $this->editingEquipment->warranty_end_date?->format('Y-m-d');
            $this->status = $this->editingEquipment->status;
            $this->current_location = $this->editingEquipment->current_location;
            $this->notes = $this->editingEquipment->notes;
            $this->condition_status = $this->editingEquipment->condition_status;
            $this->department_id = $this->editingEquipment->department_id;
            $this->item_code = $this->editingEquipment->item_code;
            $this->description = $this->editingEquipment->description;
            $this->purchase_price = $this->editingEquipment->purchase_price;
            $this->acquisition_type = $this->editingEquipment->acquisition_type;
            $this->classification = $this->editingEquipment->classification;
            $this->funded_by = $this->editingEquipment->funded_by;
            $this->supplier_name = $this->editingEquipment->supplier_name;
            $this->specifications = $this->editingEquipment->specifications ? json_decode($this->editingEquipment->specifications, true) : null;
        }
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();

        // Reset all public properties to their initial/default states
        $this->asset_type = key($this->assetTypeOptions); // Reset to first option
        $this->brand = null;
        $this->model_name = null;
        $this->serial_number = '';
        $this->tag_id = '';
        $this->purchase_date = null;
        $this->warranty_expiry_date = null;
        $this->status = key($this->statusOptions); // Reset to first option
        $this->current_location = null;
        $this->notes = null;
        $this->condition_status = key($this->conditionStatusOptions); // Reset to first option
        $this->department_id = null;

        $this->item_code = null;
        $this->description = null;
        $this->purchase_price = null;
        $this->acquisition_type = key($this->acquisitionTypeOptions); // Reset to first option
        $this->classification = key($this->classificationOptions); // Reset to first option
        $this->funded_by = null;
        $this->supplier_name = null;
        $this->specifications = null;

        $this->isEditing = false; // This line should now work correctly
    }

    private function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->showViewModal = false;
        $this->dispatch('close-modal');
        $this->resetForm(); // Reset form fields after closing
    }

    public function render(): View
    {
        return view('livewire.resource-management.admin.equipment.index', [
            'equipment' => $this->equipment,
            'departments' => $this->departmentOptions, // Pass department options to the view
        ]);
    }
}
