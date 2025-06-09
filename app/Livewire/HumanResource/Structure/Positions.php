<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Grade;
use App\Models\Position; // For Grade selection
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component; // Alias for Laravel's Rule

class Positions extends Component
{
    public $positions = [];

    public ?Position $positionInstance = null; // Explicitly type hinting

    // Form fields based on System Design
    // #[Rule('required|string|max:255')] // Livewire 3 attribute validation
    public string $name = '';

    // #[Rule('nullable|string|max:1000')]
    public ?string $description = null;

    // #[Rule('required|exists:grades,id')]
    public ?int $grade_id = null;

    // #[Rule('boolean')]
    public bool $is_active = true;

    // public $vacanciesCount; // Removed as it's not in MOTAC System Design for positions table

    public bool $isEditMode = false;

    public ?int $confirmedId = null;

    // Options for dropdowns
    public array $gradeOptions = [];

    public function mount(): void
    {
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->loadPositions();
    }

    protected function rules(): array
    {
        $nameRule = ValidationRule::unique('positions', 'name');
        if ($this->isEditMode && $this->positionInstance) {
            $nameRule->ignore($this->positionInstance->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'description' => ['nullable', 'string', 'max:1000'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function loadPositions(): void
    {
        $this->positions = Position::with('grade')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.human-resource.structure.positions');
    }

    public function submitPosition()
    {
        $this->validate(); // Uses rules() method

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'grade_id' => $this->grade_id,
            'is_active' => $this->is_active,
            // 'vacancies_count' => $this->vacanciesCount, // Removed
        ];

        if ($this->isEditMode && $this->positionInstance) {
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

    public function showNewPositionModal(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->positionInstance = null;
        $this->dispatch('openModal', elementId: '#positionModal');
    }

    public function showEditPositionModal(Position $position): void
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->positionInstance = $position;

        $this->name = $position->name;
        $this->description = $position->description;
        $this->grade_id = $position->grade_id;
        $this->is_active = $position->is_active;
        // $this->vacanciesCount = $position->vacancies_count; // Removed
        $this->dispatch('openModal', elementId: '#positionModal');
    }

    public function confirmDeletePosition($id): void
    {
        $this->confirmedId = $id;
    }

    public function deletePosition(Position $position): void
    {
        $position->delete();
        session()->flash('toastr', ['type' => 'success', 'message' => __('Jawatan berjaya dipadam.')]);
        $this->loadPositions();
        $this->confirmedId = null;
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = '';
        $this->description = null;
        $this->grade_id = null;
        $this->is_active = true;
        // $this->vacanciesCount = 0; // Removed
        $this->positionInstance = null;
        $this->isEditMode = false;
    }
}
