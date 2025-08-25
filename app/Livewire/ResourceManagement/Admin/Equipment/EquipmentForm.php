<?php

namespace App\Livewire\ResourceManagement\Admin\Equipment;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component;

/**
 * EquipmentForm Livewire component.
 * Handles the creation and updating of ICT equipment records.
 */
class EquipmentForm extends Component
{
    public ?Equipment $equipmentInstance = null;

    // Form fields
    public string $tag_id = '';
    public string $asset_type = '';
    public string $brand = '';
    public string $model = '';
    public ?string $serial_number = null;
    public ?string $purchase_date = null;
    public ?string $warranty_end_date = null;
    public string $status = '';
    public ?int $location_id = null;
    public ?int $department_id = null;
    public ?string $notes = null;
    public bool $isEditMode = false;

    // Dropdown options
    public array $assetTypeOptions = [];
    public array $statusOptions = [];
    public array $locationOptions = [];
    public array $departmentOptions = [];

    /**
     * Validation rules for the equipment form.
     */
    protected function rules(): array
    {
        $tagIdRule = ValidationRule::unique('equipment', 'tag_id');
        if ($this->isEditMode && $this->equipmentInstance instanceof Equipment) {
            $tagIdRule->ignore($this->equipmentInstance->id);
        }

        return [
            'tag_id' => ['required', 'string', 'max:255', $tagIdRule],
            'asset_type' => ['required', 'string', ValidationRule::in(array_keys(Equipment::getAssetTypeOptions()))],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_end_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['required', 'string', ValidationRule::in(array_keys(Equipment::getStatusOptions()))],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'tag_id.required' => __('ID Tag diperlukan.'),
            'tag_id.unique' => __('ID Tag ini telah wujud.'),
            'asset_type.required' => __('Jenis Aset diperlukan.'),
            'brand.required' => __('Jenama diperlukan.'),
            'model.required' => __('Model diperlukan.'),
            'status.required' => __('Status diperlukan.'),
            'warranty_end_date.after_or_equal' => __('Tarikh tamat waranti mesti selepas atau sama dengan tarikh pembelian.'),
        ];
    }

    /**
     * Mount the form, load dropdowns, and fill form if editing.
     */
    public function mount(?int $equipmentId = null): void
    {
        $this->assetTypeOptions = Equipment::getAssetTypeOptions();
        $this->statusOptions = Equipment::getStatusOptions();
        $this->locationOptions = Location::orderBy('name')->pluck('name', 'id')->all();
        $this->departmentOptions = Department::orderBy('name')->pluck('name', 'id')->all();

        if ($equipmentId) {
            $this->equipmentInstance = Equipment::findOrFail($equipmentId);
            $this->isEditMode = true;
            $this->fillForm();
        } else {
            $this->resetForm();
        }
    }

    /**
     * Fill the form with existing equipment data (edit mode).
     */
    public function fillForm(): void
    {
        if ($this->equipmentInstance) {
            $this->tag_id = $this->equipmentInstance->tag_id;
            $this->asset_type = $this->equipmentInstance->asset_type;
            $this->brand = $this->equipmentInstance->brand;
            $this->model = $this->equipmentInstance->model;
            $this->serial_number = $this->equipmentInstance->serial_number;
            $this->purchase_date = $this->equipmentInstance->purchase_date?->format('Y-m-d');
            $this->warranty_end_date = $this->equipmentInstance->warranty_end_date?->format('Y-m-d');
            $this->status = $this->equipmentInstance->status;
            $this->location_id = $this->equipmentInstance->location_id;
            $this->department_id = $this->equipmentInstance->department_id;
            $this->notes = $this->equipmentInstance->notes;
        }
    }

    /**
     * Save the equipment record (create or update).
     */
    public function saveEquipment(): void
    {
        $this->validate();

        $data = [
            'tag_id' => $this->tag_id,
            'asset_type' => $this->asset_type,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'purchase_date' => $this->purchase_date,
            'warranty_end_date' => $this->warranty_end_date,
            'status' => $this->status,
            'location_id' => $this->location_id,
            'department_id' => $this->department_id,
            'notes' => $this->notes,
        ];

        if ($this->isEditMode && $this->equipmentInstance) {
            $this->equipmentInstance->update($data);
            session()->flash('success', __('Peralatan berjaya dikemaskini.'));
        } else {
            Equipment::create($data);
            session()->flash('success', __('Peralatan baru berjaya ditambah.'));
        }

        $this->redirectRoute('resource-management.admin.equipment.equipment-index', navigate: true);
    }

    /**
     * Reset the form fields to default.
     */
    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->tag_id = '';
        $this->asset_type = '';
        $this->brand = '';
        $this->model = '';
        $this->serial_number = null;
        $this->purchase_date = null;
        $this->warranty_end_date = null;
        $this->status = Equipment::STATUS_AVAILABLE;
        $this->location_id = null;
        $this->department_id = null;
        $this->notes = null;
        $this->equipmentInstance = null;
        $this->isEditMode = false;
    }

    /**
     * Render the equipment form Blade view.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.equipment.equipment-form');
    }
}
