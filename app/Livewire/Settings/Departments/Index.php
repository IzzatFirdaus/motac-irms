<?php

namespace App\Livewire\Settings\Departments;

use App\Models\Department;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pengurusan Jabatan')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

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

    public bool $showDeleteConfirmationModal = false;

    public ?int $departmentIdToDelete = null;

    public string $departmentNameToDelete = '';

    public array $branchTypeOptions = [];

    protected array $queryString = ['search', 'sortField', 'sortDirection'];

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', Department::class);
        $this->editingDepartment = new Department;
        $this->branchTypeOptions = Department::getBranchTypeOptions();
        if (($this->branch_type === '' || $this->branch_type === '0') && $this->branchTypeOptions !== []) {
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
        $query = Department::query()
            // EDITED: Eager load the relationship to fix the N+1 query issue.
            ->with('headOfDepartment:id,name')
            ->when($this->search, function ($q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });

        if ($this->sortField !== '' && $this->sortField !== '0') {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('name', 'asc');
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
        ];

        if ($this->isEditMode && $this->editingDepartment instanceof \App\Models\Department && $this->editingDepartment->exists) {
            $this->editingDepartment->update($data);
            session()->flash('success', __('Jabatan :name berjaya dikemaskini.', ['name' => $this->editingDepartment->name]));
        } else {
            $newDepartment = Department::create($data);
            session()->flash('success', __('Jabatan :name berjaya dicipta.', ['name' => $newDepartment->name]));
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

            if ($department->users()->count() > 0) {
                session()->flash('error', __('Jabatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $department->name]));

                return;
            }

            $this->departmentIdToDelete = $id;
            $this->departmentNameToDelete = $department->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Jabatan tidak ditemui.'));
        }
    }

    public function deleteDepartment(): void
    {
        if ($this->departmentIdToDelete !== null && $this->departmentIdToDelete !== 0) {
            $department = Department::findOrFail($this->departmentIdToDelete);
            $this->authorize('delete', $department);

            if ($department->users()->count() > 0) {
                session()->flash('error', __('Jabatan ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.', ['name' => $department->name]));
                $this->closeDeleteConfirmationModal();

                return;
            }

            $department->delete();
            session()->flash('success', __('Jabatan :name berjaya dipadam.', ['name' => $this->departmentNameToDelete]));
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
        $departmentIdToIgnore = ($this->isEditMode && $this->editingDepartment instanceof \App\Models\Department && $this->editingDepartment->id)
                                ? $this->editingDepartment->id
                                : null;

        return [
            'name' => ['required', 'string', 'max:255', ValidationRule::unique('departments', 'name')->ignore($departmentIdToIgnore)],
            'code' => ['nullable', 'string', 'max:50', ValidationRule::unique('departments', 'code')->ignore($departmentIdToIgnore)],
            'branch_type' => ['required', 'string', ValidationRule::in(array_keys($this->branchTypeOptions))],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->is_active = true;

        $this->branch_type = $this->branchTypeOptions === [] ? '' : array_key_first($this->branchTypeOptions);

        $this->editingDepartment = new Department;
        $this->isEditMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.settings.departments.index', [
            'departments' => $this->departments,
            'branchTypeOptions' => $this->branchTypeOptions,
        ]);
    }
}
