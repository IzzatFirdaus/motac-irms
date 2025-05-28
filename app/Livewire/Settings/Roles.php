<?php

namespace App\Livewire\Settings;

use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role; // We will use the fully qualified name directly

#[Layout('layouts.app')]
class Roles extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?\Spatie\Permission\Models\Role $editingRole = null; // Type hint with FQCN
    public ?int $roleId = null;
    public string $name = '';
    public array $selectedPermissions = [];

    public bool $showDeleteConfirmationModal = false;
    public ?int $roleIdToDelete = null;

    public $allPermissions = [];

    protected string $paginationTheme = 'bootstrap';

    protected $listeners = ['closeRoleModalEvent' => 'closeModal'];

    public function mount(): void
    {
        // $this->authorize('viewAny', \Spatie\Permission\Models\Role::class);
        $this->editingRole = new \Spatie\Permission\Models\Role(); // Use FQCN
        $this->allPermissions = Permission::orderBy('name')->pluck('name', 'id')->all(); // Permission model seems fine
    }

    public function create(): void
    {
        // $this->authorize('create', \Spatie\Permission\Models\Role::class);
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(\Spatie\Permission\Models\Role $role): void // Type hint with FQCN
    {
        // $this->authorize('update', $role);
        $this->resetInputFields();
        $this->editingRole = $role;
        $this->name = $role->name;
        $this->roleId = $role->id;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function saveRole(): void
    {
        $this->validate();

        $data = ['name' => $this->name, 'guard_name' => 'web'];

        if ($this->isEditMode && $this->editingRole && $this->editingRole->exists) {
            // $this->authorize('update', $this->editingRole);
            $this->editingRole->update($data);
            $this->editingRole->permissions()->sync($this->selectedPermissions);
            session()->flash('message', __('Peranan berjaya dikemaskini.'));
        } else {
            // $this->authorize('create', \Spatie\Permission\Models\Role::class);
            // Use FQCN for Role::create
            $role = \Spatie\Permission\Models\Role::create($data);
            $role->permissions()->sync($this->selectedPermissions);
            session()->flash('message', __('Peranan berjaya dicipta.'));
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function confirmRoleDeletion(int $id): void
    {
        // $role = \Spatie\Permission\Models\Role::find($id); // Use FQCN
        // if ($role) {
        //     $this->authorize('delete', $role);
        // }
        $this->roleIdToDelete = $id;
        $this->showDeleteConfirmationModal = true;
    }

    public function deleteRole(): void
    {
        if ($this->roleIdToDelete) {
            // Use FQCN for Role::findOrFail
            $role = \Spatie\Permission\Models\Role::findOrFail($this->roleIdToDelete);
            // $this->authorize('delete', $role);
            if ($role->users()->count() > 0) {
                session()->flash('error', __('Peranan tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.'));
                $this->showDeleteConfirmationModal = false;
                $this->roleIdToDelete = null;
                return;
            }
            $role->delete();
            session()->flash('message', __('Peranan berjaya dipadam.'));
        }
        $this->showDeleteConfirmationModal = false;
        $this->roleIdToDelete = null;
    }

    public function render()
    {
        // *** Critical Change: Use the fully qualified class name (FQCN) ***
        $roles = \Spatie\Permission\Models\Role::withCount('permissions', 'users')->orderBy('name')->paginate(10);

        return view('livewire.settings.roles', [
          'roles' => $roles,
        ]);
    }

    protected function rules(): array
    {
        $roleIdToIgnore = $this->isEditMode && $this->editingRole ? $this->editingRole->id : null;
        return [
          'name' => [
            'required',
            'string',
            'min:3',
            'max:255',
            // Ensure ValidationRule is correctly imported if this line causes issues,
            // or use the FQCN for Rule: \Illuminate\Validation\Rule::unique...
            ValidationRule::unique('roles', 'name')->ignore($roleIdToIgnore),
          ],
          'selectedPermissions' => 'nullable|array',
          'selectedPermissions.*' => 'exists:permissions,id',
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->roleId = null;
        $this->selectedPermissions = [];
        $this->editingRole = new \Spatie\Permission\Models\Role(); // Use FQCN
        $this->isEditMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
