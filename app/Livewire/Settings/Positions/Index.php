<?php

namespace App\Livewire\Settings\Positions;

use App\Models\Grade;
use App\Models\Position; // Import Grade model
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
// Although not directly used if relying on $this->authorize, it's fine to keep.
use Livewire\WithPagination; // Added missing import

// Consider using a PositionService for business logic if it grows complex
// use App\Services\PositionService;

#[Layout('layouts.app')]
#[Title('Pengurusan Jawatan')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Position $editingPosition = null;

    public string $name = '';

    public ?int $grade_id = null;

    public string $description = '';

    public bool $is_active = true;

    public bool $showDeleteConfirmationModal = false;

    public ?int $positionIdToDelete = null;

    public string $positionNameToDelete = '';

    public array $gradeOptions = [];

    protected string $paginationTheme = 'bootstrap';

    // Optional: Inject PositionService if you implement it
    // protected PositionService $positionService;

    // public function boot(PositionService $positionService)
    // {
    //     $this->positionService = $positionService;
    // }

    public function mount(): void
    {
        $this->authorize('viewAny', Position::class); // Assuming PositionPolicy exists and is registered
        $this->gradeOptions = Grade::orderBy('name')->pluck('name', 'id')->all();
        $this->editingPosition = new Position; // Initialize for type hinting and new creations
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function getPositionsProperty()
    {
        // Ensure Position model has a scopeSearch($query, $term) method.
        $query = Position::with('grade:id,name') // Eager load grade for display
            ->search($this->search); // Assumes a 'search' scope on Position model

        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('name', 'asc'); // Default sort
        }

        return $query->paginate(10);
    }

    public function create(): void
    {
        $this->authorize('create', Position::class);
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(Position $position): void
    {
        $this->authorize('update', $position);
        $this->resetInputFields(); // Reset before populating to ensure clean state
        $this->editingPosition = $position;
        $this->name = $position->name;
        $this->grade_id = $position->grade_id;
        $this->description = $position->description ?? '';
        $this->is_active = $position->is_active;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function savePosition(): void
    {
        $this->isEditMode ? $this->authorize('update', $this->editingPosition) : $this->authorize('create', Position::class);

        $validatedData = $this->validate();
        $data = [
            'name' => $validatedData['name'],
            'grade_id' => $validatedData['grade_id'],
            'description' => $validatedData['description'],
            'is_active' => $validatedData['is_active'],
        ];

        // Consider moving saving logic to a PositionService
        // E.g., if ($this->isEditMode) { $this->positionService->updatePosition($this->editingPosition, $data); }
        // else { $this->positionService->createPosition($data); }

        if ($this->isEditMode && $this->editingPosition && $this->editingPosition->exists) {
            $this->editingPosition->update($data);
            session()->flash('message', __('Jawatan :name berjaya dikemaskini.', ['name' => $this->editingPosition->name]));
        } else {
            $newPosition = Position::create($data);
            session()->flash('message', __('Jawatan :name berjaya dicipta.', ['name' => $newPosition->name]));
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function confirmPositionDeletion(int $id): void
    {
        $position = Position::find($id);
        if ($position) {
            $this->authorize('delete', $position);

            // Crucial check: Ensure Position model has a users() relationship defined.
            // E.g., public function users() { return $this->hasMany(User::class); }
            if ($position->users()->count() > 0) {
                session()->flash('error', __('Jawatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $position->name]));

                return;
            }
            $this->positionIdToDelete = $id;
            $this->positionNameToDelete = $position->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Jawatan tidak ditemui.'));
        }
    }

    public function deletePosition(): void
    {
        if ($this->positionIdToDelete) {
            $position = Position::findOrFail($this->positionIdToDelete);
            $this->authorize('delete', $position);

            // Consider moving deletion logic (with checks) to a PositionService
            // E.g., $result = $this->positionService->deletePosition($position); if ($result['error']) ...

            // Double check if position is in use before deleting
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

    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->positionIdToDelete = null;
        $this->positionNameToDelete = '';
    }

    public function render()
    {
        // View path assumes blade file is at resources/views/livewire/settings/positions/index.blade.php
        return view('livewire.settings.positions.index', [
            'positions' => $this->positions, // Accesses getPositionsProperty computed property
            'gradeOptions' => $this->gradeOptions,
        ]);
    }

    protected function rules(): array
    {
        $positionIdToIgnore = ($this->isEditMode && $this->editingPosition && $this->editingPosition->id) ? $this->editingPosition->id : null;

        return [
            'name' => ['required', 'string', 'max:255', ValidationRule::unique('positions', 'name')->ignore($positionIdToIgnore)],
            'grade_id' => 'nullable|exists:grades,id', // A position might not always be tied to a grade
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->grade_id = null;
        $this->description = '';
        $this->is_active = true; // Default to active for new entries
        $this->editingPosition = new Position; // Reset to a new instance
        $this->isEditMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
