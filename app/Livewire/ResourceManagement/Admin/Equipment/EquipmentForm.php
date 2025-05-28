<?php

namespace App\Livewire\ResourceManagement\Admin\Equipment;

use App\Models\Equipment;
use Illuminate\Validation\Rule;
use Livewire\Component;

// Assuming created_by/updated_by are handled by observer

class EquipmentForm extends Component
{
    public ?Equipment $equipmentInstance = null;

    public string $asset_type = '';
    public string $brand = '';
    public string $model_name = ''; // Using model_name to avoid conflict with Eloquent's internal $model
    public string $serial_number = '';
    public string $tag_id = '';
    public ?string $purchase_date = null;
    public ?string $warranty_expiry_date = null;
    public string $status = '';
    public string $current_location = '';
    public string $notes = '';
    public string $condition_status = '';

    public bool $isEditing = false;

    public array $assetTypeOptions = [];
    public array $statusOptions = [];
    public array $conditionStatusOptions = [];

    public function mount(?int $equipmentId = null): void
    {
        $this->assetTypeOptions = Equipment::$ASSET_TYPES_LABELS; //
        $this->statusOptions = Equipment::$STATUSES_LABELS; //
        $this->conditionStatusOptions = Equipment::$CONDITION_STATUSES_LABELS; //

        if ($equipmentId) {
            $this->equipmentInstance = Equipment::findOrFail($equipmentId);
            $this->isEditing = true;
            $this->populateFields();
        } else {
            $this->equipmentInstance = new Equipment();
            // Set default values from Equipment model attributes or manually
            $this->status = $this->equipmentInstance->getAttributes()['status'] ?? Equipment::STATUS_AVAILABLE; //
            $this->condition_status = $this->equipmentInstance->getAttributes()['condition_status'] ?? Equipment::CONDITION_NEW; //
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function saveEquipment(): void
    {
        $validatedData = $this->validate();

        // Map model_name back to model for database saving
        $dbData = $validatedData;
        $dbData['model'] = $validatedData['model_name'];
        unset($dbData['model_name']);

        if ($this->isEditing && $this->equipmentInstance->exists) {
            $this->equipmentInstance->update($dbData);
            session()->flash('success', 'Peralatan berjaya dikemaskini.');
        } else {
            Equipment::create($dbData); // Assumes BlameableObserver handles created_by
            session()->flash('success', 'Peralatan baru berjaya ditambah.');
            $this->resetForm();
        }

        $this->dispatch('equipmentSaved'); // For refreshing lists or other components
        // Consider redirecting based on your application flow. The web.php routes might use a controller to show this component.
        // If this component is full-page, redirect here. Otherwise, the parent page might handle redirection or modal closing.
        $this->redirectRoute('resource-management.admin.equipment-admin.index'); //
    }

    public function render()
    {
        return view('livewire.resource-management.admin.equipment.equipment-form');
    }

    protected function populateFields(): void
    {
        if ($this->equipmentInstance) {
            $this->asset_type = $this->equipmentInstance->asset_type;
            $this->brand = $this->equipmentInstance->brand ?? '';
            $this->model_name = $this->equipmentInstance->model ?? ''; // Eloquent model's attribute is 'model'
            $this->serial_number = $this->equipmentInstance->serial_number ?? '';
            $this->tag_id = $this->equipmentInstance->tag_id ?? '';
            $this->purchase_date = $this->equipmentInstance->purchase_date ? $this->equipmentInstance->purchase_date->format('Y-m-d') : null;
            $this->warranty_expiry_date = $this->equipmentInstance->warranty_expiry_date ? $this->equipmentInstance->warranty_expiry_date->format('Y-m-d') : null;
            $this->status = $this->equipmentInstance->status;
            $this->current_location = $this->equipmentInstance->current_location ?? '';
            $this->notes = $this->equipmentInstance->notes ?? '';
            $this->condition_status = $this->equipmentInstance->condition_status ?? '';
        }
    }

    protected function rules(): array
    {
        return [
            'asset_type' => ['required', 'string', Rule::in(array_keys(Equipment::$ASSET_TYPES_LABELS))], //
            'brand' => ['required', 'string', 'max:255'],
            'model_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', Rule::unique('equipment', 'serial_number')->ignore($this->equipmentInstance?->id)],
            'tag_id' => ['required', 'string', 'max:255', Rule::unique('equipment', 'tag_id')->ignore($this->equipmentInstance?->id)],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'warranty_expiry_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:purchase_date'],
            'status' => ['required', 'string', Rule::in(array_keys(Equipment::$STATUSES_LABELS))], //
            'current_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'condition_status' => ['required', 'string', Rule::in(array_keys(Equipment::$CONDITION_STATUSES_LABELS))], //
        ];
    }

    private function resetForm()
    {
        $this->resetValidation();
        $this->resetExcept('assetTypeOptions', 'statusOptions', 'conditionStatusOptions');
        $this->isEditing = false;
        $this->equipmentInstance = new Equipment();
        $this->status = $this->equipmentInstance->getAttributes()['status'] ?? Equipment::STATUS_AVAILABLE; //
        $this->condition_status = $this->equipmentInstance->getAttributes()['condition_status'] ?? Equipment::CONDITION_NEW; //
    }
}
