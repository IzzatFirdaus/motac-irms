<?php

namespace App\Livewire\Settings\Positions;

use App\Models\Grade;
use App\Models\Position;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * PositionsIndex Livewire Component
 * Handles listing, searching, sorting, creating, updating, and deleting of positions.
 */
#[Layout('layouts.app')]
#[Title('Pengurusan Jawatan')]
class PositionsIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Position $editingPosition = null;

    // Form fields
    public string $name = '';

    public ?int $grade_id = null;

    public string $description = '';

    public bool $is_active = true;

    // Delete modal state
    public bool $showDeleteConfirmationModal = false;

    public ?int $positionIdToDelete = null;

    public string $positionNameToDelete = '';

    // List of grade options for the dropdown
    public array $gradeOptions = [];

    protected string $paginationTheme = 'bootstrap';

    /**
     * Component initialization. Loads grade options and ensures user has access.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', Position::class);
        $this->gradeOptions    = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->editingPosition = new Position();
    }

    /**
     * Handle sorting the table by column.
     */
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

    /**
     * Computed property: get paginated, searched, and sorted positions list.
     */
    public function getPositionsProperty()
    {
        $query = Position::with('grade:id,name')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('grade', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });

        if ($this->sortField !== '' && $this->sortField !== '0') {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate(10);
    }

    /**
     * Open the modal for creating a new position.
     */
    public function create(): void
    {
        $this->authorize('create', Position::class);
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal  = true;
    }

    /**
     * Open the modal for editing an existing position.
     */
    public function edit(Position $position): void
    {
        $this->authorize('update', $position);
        $this->resetInputFields();
        $this->editingPosition = $position;
        $this->name            = $position->name;
        $this->grade_id        = $position->grade_id;
        $this->description     = $position->description ?? '';
        $this->is_active       = $position->is_active;
        $this->isEditMode      = true;
        $this->showModal       = true;
    }

    /**
     * Save a new or existing position.
     */
    public function savePosition(): void
    {
        $this->isEditMode
            ? $this->authorize('update', $this->editingPosition)
            : $this->authorize('create', Position::class);

        $validatedData = $this->validate();

        $data = [
            'name'        => $validatedData['name'],
            'grade_id'    => $validatedData['grade_id'],
            'description' => $validatedData['description'],
            'is_active'   => $validatedData['is_active'],
        ];

        if ($this->isEditMode && $this->editingPosition instanceof Position && $this->editingPosition->exists) {
            $this->editingPosition->update($data);
            session()->flash('message', __('Jawatan :name berjaya dikemaskini.', ['name' => $this->editingPosition->name]));
        } else {
            $newPosition = Position::create($data);
            session()->flash('message', __('Jawatan :name berjaya dicipta.', ['name' => $newPosition->name]));
        }

        $this->closeModal();
    }

    /**
     * Close the form modal and reset form state.
     */
    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    /**
     * Prompt for position deletion.
     */
    public function confirmPositionDeletion(int $id): void
    {
        $position = Position::find($id);
        if ($position) {
            $this->authorize('delete', $position);
            // Prevent deletion if in use by any user
            if ($position->users()->count() > 0) {
                session()->flash('error', __('Jawatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $position->name]));

                return;
            }
            $this->positionIdToDelete          = $id;
            $this->positionNameToDelete        = $position->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Jawatan tidak ditemui.'));
        }
    }

    /**
     * Delete the selected position.
     */
    public function deletePosition(): void
    {
        if ($this->positionIdToDelete !== null && $this->positionIdToDelete !== 0) {
            $position = Position::findOrFail($this->positionIdToDelete);
            $this->authorize('delete', $position);

            if ($position->users()->count() > 0) {
                session()->flash('error', __('Jawatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $position->name]));
                $this->closeDeleteConfirmationModal();

                return;
            }

            $position->delete();
            session()->flash('message', __('Jawatan :name berjaya dipadam.', ['name' => $this->positionNameToDelete]));
        }
        $this->closeDeleteConfirmationModal();
    }

    /**
     * Close the delete confirmation modal.
     */
    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->positionIdToDelete          = null;
        $this->positionNameToDelete        = '';
    }

    /**
     * Validation rules for the position form.
     */
    protected function rules(): array
    {
        $positionIdToIgnore = ($this->isEditMode && $this->editingPosition instanceof Position && $this->editingPosition->id)
            ? $this->editingPosition->id
            : null;

        return [
            'name'        => ['required', 'string', 'max:255', ValidationRule::unique('positions', 'name')->ignore($positionIdToIgnore)],
            'grade_id'    => 'nullable|exists:grades,id',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'required|boolean',
        ];
    }

    /**
     * Reset all form fields and editing state.
     */
    private function resetInputFields(): void
    {
        $this->name            = '';
        $this->grade_id        = null;
        $this->description     = '';
        $this->is_active       = true;
        $this->editingPosition = new Position();
        $this->isEditMode      = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.settings.positions.positions-index', [
            'positions'    => $this->getPositionsProperty(), // Computed getter
            'gradeOptions' => $this->gradeOptions,
        ]);
    }
}
