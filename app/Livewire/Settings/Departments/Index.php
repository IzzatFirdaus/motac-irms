<?php

namespace App\Livewire\Settings\Departments;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule as ValidationRule;

// Consider using a DepartmentService for business logic if it grows complex
// use App\Services\DepartmentService;

#[Layout('layouts.app')]
#[Title('Pengurusan Jabatan')]
class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?Department $editingDepartment = null;

    // Form fields
    public string $name = '';
    public string $code = '';
    public string $branch_type = '';
    public string $description = '';
    public bool $is_active = true;
    // public ?int $head_of_department_id = null; // Add if you want to manage HOD here

    public bool $showDeleteConfirmationModal = false;
    public ?int $departmentIdToDelete = null;
    public string $departmentNameToDelete = '';

    public array $branchTypeOptions = [];

    protected array $queryString = ['search', 'sortField', 'sortDirection'];
    protected string $paginationTheme = 'bootstrap';

    // Optional: Inject DepartmentService if you implement it
    // protected DepartmentService $departmentService;

    // public function boot(DepartmentService $departmentService)
    // {
    //     $this->departmentService = $departmentService;
    // }

    public function mount(): void
    {
        $this->authorize('viewAny', Department::class); // Assuming DepartmentPolicy exists
        $this->editingDepartment = new Department(); // Initialize for type hinting and new creations
        $this->branchTypeOptions = Department::getBranchTypeOptions(); // Assumes a static method on Department model
                                                                    // or define directly: ['headquarters' => 'Headquarters', 'state' => 'State'];
        if (empty($this->branch_type) && !empty($this->branchTypeOptions)) {
            $this->branch_type = array_key_first($this->branchTypeOptions);
        }
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

    public function getDepartmentsProperty()
    {
        // Consider moving complex query logic to DepartmentService or a UserRepository
        $query = Department::query()
            // ->with('headOfDepartment:id,name') // Eager load HOD if managing here
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });

        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('name', 'asc'); // Default sort
        }
        return $query->paginate(10);
    }

    public function create(): void
    {
        $this->authorize('create', Department::class);
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(Department $department): void
    {
        $this->authorize('update', $department);
        $this->resetInputFields();
        $this->editingDepartment = $department;
        $this->name = $department->name;
        $this->code = $department->code ?? '';
        $this->branch_type = $department->branch_type;
        $this->description = $department->description ?? '';
        $this->is_active = $department->is_active;
        // $this->head_of_department_id = $department->head_of_department_id; // If managing HOD
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function saveDepartment(): void
    {
        $this->isEditMode ? $this->authorize('update', $this->editingDepartment) : $this->authorize('create', Department::class);

        $validatedData = $this->validate();
        $data = [
            'name' => $validatedData['name'],
            'code' => $validatedData['code'],
            'branch_type' => $validatedData['branch_type'],
            'description' => $validatedData['description'],
            'is_active' => $validatedData['is_active'],
            // 'head_of_department_id' => $validatedData['head_of_department_id'], // If managing HOD
        ];

        // Consider moving saving logic to a DepartmentService
        // E.g., if ($this->isEditMode) { $this->departmentService->updateDepartment($this->editingDepartment, $data); }
        // else { $this->departmentService->createDepartment($data); }

        if ($this->isEditMode && $this->editingDepartment && $this->editingDepartment->exists) {
            $this->editingDepartment->update($data);
            session()->flash('message', __('Jabatan :name berjaya dikemaskini.', ['name' => $this->editingDepartment->name]));
        } else {
            $newDepartment = Department::create($data);
            session()->flash('message', __('Jabatan :name berjaya dicipta.', ['name' => $newDepartment->name]));
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function confirmDepartmentDeletion(int $id): void
    {
        $department = Department::find($id);
        if ($department) {
            $this->authorize('delete', $department);

            // Check if department is linked to users
            // Requires Department model to have a users() relationship.
            // Example: public function users() { return $this->hasMany(User::class); }
            if ($department->users()->count() > 0) {
                 session()->flash('error', __('Jabatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $department->name]));
                 return;
            }
            // Add other checks if department is linked to other models like equipment [cite: 79]

            $this->departmentIdToDelete = $id;
            $this->departmentNameToDelete = $department->name;
            $this->showDeleteConfirmationModal = true;
        } else {
             session()->flash('error', __('Jabatan tidak ditemui.'));
        }
    }

    public function deleteDepartment(): void
    {
        if ($this->departmentIdToDelete) {
            $department = Department::findOrFail($this->departmentIdToDelete);
            $this->authorize('delete', $department);

            // Consider moving deletion logic (with checks) to a DepartmentService
            // E.g., $result = $this->departmentService->deleteDepartment($department); if ($result['error']) ...

            // Double check dependencies before deleting
            if ($department->users()->count() > 0) { // Assuming users relationship
                session()->flash('error', __('Jabatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $department->name]));
                $this->closeDeleteConfirmationModal();
                return;
            }
            // Add other dependency checks here (e.g., equipment)

            $department->delete();
            session()->flash('message', __('Jabatan :name berjaya dipadam.', ['name' => $this->departmentNameToDelete]));
        }
        $this->closeDeleteConfirmationModal();
    }

    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->departmentIdToDelete = null;
        $this->departmentNameToDelete = '';
    }

    protected function rules(): array
    {
        $departmentIdToIgnore = ($this->isEditMode && $this->editingDepartment && $this->editingDepartment->id)
                                ? $this->editingDepartment->id
                                : null;
        return [
            'name' => ['required', 'string', 'max:255', ValidationRule::unique('departments', 'name')->ignore($departmentIdToIgnore)],
            'code' => ['nullable', 'string', 'max:50', ValidationRule::unique('departments', 'code')->ignore($departmentIdToIgnore)],
            'branch_type' => ['required', 'string', ValidationRule::in(array_keys($this->branchTypeOptions))],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
            // 'head_of_department_id' => 'nullable|exists:users,id', // If managing HOD
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->code = '';
        // $this->branch_type = ''; // Reset or set to default below
        $this->description = '';
        $this->is_active = true; // Default to active for new entries
        // $this->head_of_department_id = null; // If managing HOD

        if (!empty($this->branchTypeOptions)) {
            $this->branch_type = array_key_first($this->branchTypeOptions);
        } else {
            $this->branch_type = '';
        }

        $this->editingDepartment = new Department(); // Reset to a new instance
        $this->isEditMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        // View path assumes blade file is at resources/views/livewire/settings/departments/index.blade.php
        return view('livewire.settings.departments.index', [
            'departments' => $this->departments, // Accesses getDepartmentsProperty computed property
            'branchTypeOptions' => $this->branchTypeOptions,
            // Pass other options if managing HOD, e.g., user list for HOD dropdown
        ]);
    }
}
