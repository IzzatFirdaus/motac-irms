<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Grade;
use App\Models\Position;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component;

/**
 * Positions Livewire Component
 *
 * Handles CRUD operations for positions in the organizational structure.
 */
class Positions extends Component
{
    public $positions = [];
    public ?Position $positionInstance = null;

    // Form fields
    public string $name = '';
    public ?string $description = null;
    public ?int $grade_id = null;
    public bool $is_active = true;

    public bool $isEditMode = false;
    public ?int $confirmedId = null;

    // Dropdown options
    public array $gradeOptions = [];

    /**
     * Initialize component state and load initial data.
     */
    public function mount(): void
    {
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->loadPositions();
    }

    /**
     * Validation rules for the position form.
     */
    protected function rules(): array
    {
        $nameRule = ValidationRule::unique('positions', 'name');
        if ($this->isEditMode && $this->positionInstance instanceof Position) {
            $nameRule->ignore($this->positionInstance->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'description' => ['nullable', 'string', 'max:1000'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Load all positions with their grade relation.
     */
    public function loadPositions(): void
    {
        $this->positions = Position::with('grade')->orderBy('name')->get();
    }

    /**
     * Render the positions Blade view.
     */
    public function render()
    {
        return view('livewire.human-resource.structure.positions');
    }

    /**
     * Handle form submission for create/update.
     */
    public function submitPosition(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'grade_id' => $this->grade_id,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditMode && $this->positionInstance instanceof Position) {
            $this->positionInstance->update($data);
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jawatan berjaya dikemaskini.')]);
        } else {
            Position::create($data);
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jawatan baru berjaya ditambah.')]);
        }

        $this->dispatch('closeModal', elementId: '#positionModal');
        $this->resetForm();
        $this->loadPositions();
    }

    /**
     * Show the modal for creating a new position.
     */
    public function showNewPositionModal(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->positionInstance = null;
        $this->dispatch('openModal', elementId: '#positionModal');
    }

    /**
     * Show the modal for editing an existing position.
     */
    public function showEditPositionModal(Position $position): void
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->positionInstance = $position;

        $this->name = $position->name;
        $this->description = $position->description;
        $this->grade_id = $position->grade_id;
        $this->is_active = $position->is_active;
        $this->dispatch('openModal', elementId: '#positionModal');
    }

    /**
     * Confirm deletion of a position.
     */
    public function confirmDeletePosition(?int $id): void
    {
        $this->confirmedId = $id;
    }

    /**
     * Actually delete the position after confirmation.
     */
    public function deletePosition(Position $position): void
    {
        $position->delete();
        session()->flash('toastr', ['type' => 'success', 'message' => __('Jawatan berjaya dipadam.')]);
        $this->loadPositions();
        $this->confirmedId = null;
    }

    /**
     * Reset the position form to default state.
     */
    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = '';
        $this->description = null;
        $this->grade_id = null;
        $this->is_active = true;
        $this->positionInstance = null;
        $this->isEditMode = false;
    }
}
