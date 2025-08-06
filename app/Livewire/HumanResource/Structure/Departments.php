<?php

namespace App\Livewire\HumanResource\Structure;

use App\Models\Department;
use App\Models\User; // For Head of Department selection
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component; // Alias for Laravel's Rule class

class Departments extends Component
{
    public $departments = [];

    public ?Department $departmentInstance = null;

    // Form fields based on System Design & your Department Model
    public string $name = '';

    public string $branch_type = ''; // Will hold keys like 'headquarters', 'state'

    public ?string $code = null;

    public ?string $description = null;

    public bool $is_active = true;

    // Corrected to match the database column name and Department model relationship
    public ?int $head_of_department_id = null;

    public bool $isEditMode = false;

    public ?int $confirmedId = null; // For delete confirmation

    // Options for dropdowns
    public array $branchTypeOptions = [];

    public array $userOptions = [];

    public function mount(): void
    {
        // Use the static method from your App\Models\Department model
        $this->branchTypeOptions = Department::getBranchTypeOptions();
        $this->userOptions = User::orderBy('name')->pluck('name', 'id')->all();
        $this->loadDepartments();
        $this->resetForm(); // Initialize form fields including default for branch_type
    }

    protected function rules(): array
    {
        $nameRule = ValidationRule::unique('departments', 'name');
        $codeRule = ValidationRule::unique('departments', 'code')->whereNull('deleted_at'); // Unique check should also consider soft deletes

        if ($this->isEditMode && $this->departmentInstance instanceof Department) { // Simplified
            $nameRule->ignore($this->departmentInstance->id);
            $codeRule->ignore($this->departmentInstance->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'branch_type' => ['required', 'string', ValidationRule::in(array_keys($this->branchTypeOptions))],
            'code' => ['nullable', 'string', 'max:50', $codeRule],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            // Corrected to match the property name
            'head_of_department_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('Nama jabatan diperlukan.'),
            'name.unique' => __('Nama jabatan ini telah wujud.'),
            'branch_type.required' => __('Jenis cawangan diperlukan.'),
            'code.unique' => __('Kod jabatan ini telah wujud.'),
            // Corrected to match the property name
            'head_of_department_id.exists' => __('Ketua jabatan yang dipilih tidak sah.'),
        ];
    }

    public function loadDepartments(): void
    {
        // Changed to headOfDepartment to match relationship name in Department model
        $this->departments = Department::with('headOfDepartment')->orderBy('name')->get();
    }

    public function render()
    {
        // Removed ->title() as it's not a standard method on Illuminate\Contracts\View\View
        // You should handle page title in your main Blade layout if not using a specific package.
        return view('livewire.human-resource.structure.departments');
    }

    public function submitDepartment(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'branch_type' => $this->branch_type,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
            // Corrected to match the database column name and Department model
            'head_of_department_id' => $this->head_of_department_id,
        ];

        if ($this->isEditMode && $this->departmentInstance instanceof Department) { // Simplified
            $this->departmentInstance->update($data);
            // Use session flash for toastr as per your original component style
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan berjaya dikemaskini.')]);
        } else {
            Department::create($data);
            session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan baru berjaya ditambah.')]);
        }

        $this->dispatch('closeModal', elementId: '#departmentModal');
        $this->resetForm();
        $this->loadDepartments();
    }

    public function showNewDepartmentModal(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->departmentInstance = null;
        $this->dispatch('openModal', elementId: '#departmentModal');
    }

    public function showEditDepartmentModal(Department $department): void
    {
        $this->resetForm(); // Reset first to clear any previous state/errors
        $this->isEditMode = true;
        $this->departmentInstance = $department;

        $this->name = $department->name;
        $this->branch_type = $department->branch_type;
        $this->code = $department->code;
        $this->description = $department->description;
        $this->is_active = $department->is_active;
        // Corrected to match the database column name and Department model
        $this->head_of_department_id = $department->head_of_department_id;
        $this->dispatch('openModal', elementId: '#departmentModal');
    }

    public function confirmDeleteDepartment(?int $id): void
    {
        $this->confirmedId = $id;
        // You might dispatch an event here to show a custom confirmation modal in Bootstrap
        // e.g., $this->dispatch('showDeleteConfirmationModal', ['id' => $id, 'name' => Department::find($id)?->name]);
    }

    public function deleteDepartment(Department $department): void
    {
        // It's good practice to add a policy check here if applicable
        // $this->authorize('delete', $department);

        // Consider checking for dependencies if a department cannot be deleted if it has users, etc.
        // For example:
        // if ($department->users()->exists() || $department->related_records()->exists()) {
        //     session()->flash('toastr', ['type' => 'error', 'message' => __('Tidak boleh memadam jabatan ini kerana ia mempunyai rekod berkaitan.')]);
        //     $this->confirmedId = null;
        //     return;
        // }

        $department->delete();
        session()->flash('toastr', ['type' => 'success', 'message' => __('Jabatan berjaya dipadam.')]);
        $this->loadDepartments();
        $this->confirmedId = null;
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->name = '';
        // Set default branch_type using a key from your options, or a specific default if options could be empty
        // Using Department::BRANCH_TYPE_HQ as a fallback default if options are somehow not loaded yet
        $this->branch_type = ($this->branchTypeOptions === [] ? Department::BRANCH_TYPE_HQ : array_key_first($this->branchTypeOptions));
        $this->code = null;
        $this->description = null;
        $this->is_active = true;
        // Corrected to match the database column name and Department model
        $this->head_of_department_id = null;

        $this->departmentInstance = null;
        $this->isEditMode = false;
    }

    // The getMembersCount method from your original component.
    // Ensure App\Models\Timeline exists and is correctly namespaced if you use this.
    // This was not part of the MOTAC System Design for the Department model itself.
    /*
    public function getMembersCount($department_id)
    {
        // Assuming Timeline model exists and is correctly namespaced
        // return \App\Models\Timeline::where('department_id', $department_id)
        // ->whereNull('end_date')
        // ->distinct('employee_id') // Assuming 'employee_id' is the correct column
        // ->count();
    }
    */
}
