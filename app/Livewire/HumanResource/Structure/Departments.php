<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Department;
use App\Models\User;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component;

/**
 * Departments Livewire Component
 *
 * Handles CRUD operations for the Departments structure.
 * Allows creating, editing, listing, and deleting departments,
 * including managing head of department and branch type.
 */
class Departments extends Component
{
    public $departments = [];

    public ?Department $departmentInstance = null;

    // Form fields for department management
    public string $name = '';
    public string $branch_type = '';
    public ?string $code = null;
    public ?string $description = null;
    public bool $is_active = true;
    public ?int $head_of_department_id = null;

    public bool $isEditMode = false;
    public ?int $confirmedId = null; // For delete confirmation

    // Dropdown options
    public array $branchTypeOptions = [];
    public array $userOptions = [];

    /**
     * Initialize component state and load initial data.
     */
    public function mount(): void
    {
        $this->branchTypeOptions = Department::getBranchTypeOptions();
        $this->userOptions = User::orderBy('name')->pluck('name', 'id')->all();
        $this->loadDepartments();
        $this->resetForm(); // Set default form state
    }

    /**
     * Validation rules for department form.
     */
    protected function rules(): array
    {
        $nameRule = ValidationRule::unique('departments', 'name');
        $codeRule = ValidationRule::unique('departments', 'code')->whereNull('deleted_at');

        if ($this->isEditMode && $this->departmentInstance instanceof Department) {
            $nameRule->ignore($this->departmentInstance->id);
            $codeRule->ignore($this->departmentInstance->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'branch_type' => ['required', 'string', ValidationRule::in(array_keys($this->branchTypeOptions))],
            'code' => ['nullable', 'string', 'max:50', $codeRule],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'head_of_department_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('Nama jabatan diperlukan.'),
            'name.unique' => __('Nama jabatan ini telah wujud.'),
            'branch_type.required' => __('Jenis cawangan diperlukan.'),
            'code.unique' => __('Kod jabatan ini telah wujud.'),
            'head_of_department_id.exists' => __('Ketua jabatan yang dipilih tidak sah.'),
        ];
    }

    /**
     * Load all departments with their head of department relation.
     */
    public function loadDepartments(): void
    {
        $this->departments = Department::with('headOfDepartment')->orderBy('name')->get();
    }

    /**
     * Render the departments Blade view.
     */
    public function render()
    {
        return view('livewire.human-resource.structure.departments');
    }

    /**
     * Handle form submission for create/update.
     */
    public function submitDepartment(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'branch_type' => $this->branch_type,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'head_of_department_id' => $this->head_of_department_id,
        ];

        if ($this->isEditMode && $this->departmentInstance instanceof Department) {
            $this->departmentInstance->update($data);
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan berjaya dikemaskini.')]);
        } else {
            Department::create($data);
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan baru berjaya ditambah.')]);
        }

        $this->dispatch('closeModal', elementId: '#departmentModal');
        $this->resetForm();
        $this->loadDepartments();
    }

    /**
     * Show the modal for creating a new department.
     */
    public function showNewDepartmentModal(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->departmentInstance = null;
        $this->dispatch('openModal', elementId: '#departmentModal');
    }

    /**
     * Show the modal for editing an existing department.
     */
    public function showEditDepartmentModal(Department $department): void
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->departmentInstance = $department;

        $this->name = $department->name;
        $this->branch_type = $department->branch_type;
        $this->code = $department->code;
        $this->description = $department->description;
        $this->is_active = $department->is_active;
        $this->head_of_department_id = $department->head_of_department_id;
        $this->dispatch('openModal', elementId: '#departmentModal');
    }

    /**
     * Confirm deletion of a department.
     */
    public function confirmDeleteDepartment(?int $id): void
    {
        $this->confirmedId = $id;
        // Additional logic for confirmation modal can be added here.
    }

    /**
     * Actually delete the department after confirmation.
     */
    public function deleteDepartment(Department $department): void
    {
        $department->delete();
        session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan berjaya dipadam.')]);
        $this->loadDepartments();
        $this->confirmedId = null;
    }

    /**
     * Reset the department form to default state.
     */
    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->name = '';
        $this->branch_type = ($this->branchTypeOptions === [] ? Department::BRANCH_TYPE_HQ : array_key_first($this->branchTypeOptions));
        $this->code = null;
        $this->description = null;
        $this->is_active = true;
        $this->head_of_department_id = null;

        $this->departmentInstance = null;
        $this->isEditMode = false;
    }

    /**
     * Example for member count (if needed).
     */
    public function getMembersCount($department_id)
    {
        // Implement with your own logic if you want to show number of users in a department.
        // return \App\Models\User::where('department_id', $department_id)->count();
        return 0;
    }
}
