<?php

namespace App\Livewire\Settings\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // Added Title attribute
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role; // Changed alias from RoleContract to use the concrete Role model

#[Layout('layouts.app')]
#[Title('Pengurusan Peranan')] // Set the title for the page
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Role $editingRole = null; // Changed type hint to concrete Role model

    public ?Role $deletingRole = null; // Declared missing property

    public string $name = '';

    public array $selectedPermissions = [];

    public bool $showDeleteConfirmationModal = false;

    public ?int $roleIdToDelete = null;

    public string $roleNameToDelete = '';

    public array $allPermissionsForView = [];

    protected string $paginationTheme = 'bootstrap';

    public array $coreRoles = []; // Public for Blade access

    public function mount(): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $this->coreRoles = config('motac.core_roles', ['Admin', 'BPM Staff', 'IT Admin', 'User']);

        // Initialize with an instance of the configured Role model
        $roleModelClass = config('permission.models.role');
        $this->editingRole = new $roleModelClass;

        $this->loadAllPermissions(); // Load permissions when component mounts
    }

    // Using a computed property for roles list
    public function getRolesProperty()
    {
        $roleModelClass = config('permission.models.role');

        return $roleModelClass::query()
            ->withCount([
                'permissions',
                'users as users_count', // Assumes your Role model has a 'users' relationship
            ])
            ->orderBy('name')
            ->paginate(10);
    }

    // Computed property for all permissions, cached
    public function getAllPermissionsForDropdownProperty(): array
    {
        return Permission::orderBy('name')->pluck('name', 'id')->all();
    }

    protected function loadAllPermissions(): void
    {
        $this->allPermissionsForView = $this->getAllPermissionsForDropdownProperty();
    }

    public function createRole(): void
    {
        $this->authorize('create', config('permission.models.role')); // Authorize create action for roles
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function storeRole(): void
    {
        $this->authorize('create', config('permission.models.role')); // Authorize create action for roles
        $this->validate();

        $roleModelClass = config('permission.models.role');
        $role = $roleModelClass::create(['name' => $this->name, 'guard_name' => 'web']);
        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('toastr', type: 'success', message: __('Peranan berjaya dicipta!'));
        $this->closeModal();
    }

    public function editRole(int $roleId): void
    {
        $this->authorize('update', config('permission.models.role')); // Authorize update action for roles

        $roleModelClass = config('permission.models.role');
        $this->editingRole = $roleModelClass::findById($roleId, 'web');

        // Prevent editing core roles or roles with users
        if (in_array($this->editingRole->name, $this->coreRoles, true) || $this->editingRole->users()->exists()) { // Corrected access to users and core roles check
            $this->dispatch('toastr', type: 'error', message: 'Tidak boleh mengedit peranan sistem atau peranan yang mempunyai pengguna bersekutu.');
            $this->closeModal();

            return;
        }

        $this->name = $this->editingRole->name;
        $this->selectedPermissions = $this->editingRole->permissions()->pluck('id')->toArray();
        $this->isEditMode = true;
        $this->showModal = true;
    }


    public function updateRole(): void
    {
        $this->authorize('update', config('permission.models.role')); // Authorize update action for roles
        $this->validate();

        // Prevent updating core roles or roles with users
        if (in_array($this->editingRole->name, $this->coreRoles, true) || $this->editingRole->users()->exists()) { // Corrected access to users and core roles check
            $this->dispatch('toastr', type: 'error', message: 'Tidak boleh mengemaskini peranan sistem atau peranan yang mempunyai pengguna bersekutu.');
            $this->closeModal();

            return;
        }

        $this->editingRole->name = $this->name; // Assign the new name
        $this->editingRole->save(); // Save the changes to the role name
        $this->editingRole->syncPermissions($this->selectedPermissions);

        $this->dispatch('toastr', type: 'success', message: __('Peranan berjaya dikemaskini!'));
        $this->closeModal();
    }

    public function confirmDelete(int $roleId): void
    {
        $this->authorize('delete', config('permission.models.role')); // Authorize delete action for roles

        $roleModelClass = config('permission.models.role');
        $this->deletingRole = $roleModelClass::findById($roleId, 'web');

        if (! $this->deletingRole) {
            $this->dispatch('toastr', type: 'error', message: __('Peranan tidak ditemui.'));
            $this->closeDeleteConfirmationModal();

            return;
        }

        // Prevent deleting core roles or roles with associated users
        if ($this->deletingRole->users()->exists() || in_array($this->deletingRole->name, $this->coreRoles, true)) { // Corrected access to users
            $this->dispatch('toastr', type: 'error', message: __('Tidak boleh memadam peranan sistem atau peranan yang mempunyai pengguna bersekutu.'));
            $this->closeDeleteConfirmationModal();

            return;
        }

        $this->roleIdToDelete = $roleId;
        $this->roleNameToDelete = $this->deletingRole->name;
        $this->showDeleteConfirmationModal = true;
        $this->dispatch('openModal', elementId: 'deleteConfirmationModal');
    }


    public function deleteRole(): void
    {
        $this->authorize('delete', config('permission.models.role')); // Authorize delete action for roles

        if ($this->roleIdToDelete) {
            $roleModelClass = config('permission.models.role');
            $role = $roleModelClass::findById($this->roleIdToDelete, 'web');

            if ($role) {
                // Double-check conditions before final delete
                if ($role->users()->exists() || in_array($role->name, $this->coreRoles, true)) {
                    $this->dispatch('toastr', type: 'error', message: __('Tidak boleh memadam peranan sistem atau peranan yang mempunyai pengguna bersekutu.'));
                } else {
                    $role->delete();
                    $this->dispatch('toastr', type: 'success', message: __('Peranan berjaya dipadam!'));
                }
            } else {
                $this->dispatch('toastr', type: 'error', message: __('Peranan tidak ditemui.'));
            }
        }

        $this->closeDeleteConfirmationModal();
        $this->resetPage(); // Refresh the list after deletion
    }


    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDeleteConfirmationModal = false;
        $this->resetInputFields();
        $this->resetErrorBag(); // Clear validation errors
    }

    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->roleIdToDelete = null;
        $this->roleNameToDelete = '';
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.roles.index', [
            'roles' => $this->roles,
            'allPermissionsForView' => $this->allPermissionsForView,
        ]);
    }

    protected function rules(): array
    {
        $roleIdToIgnore = ($this->isEditMode && $this->editingRole instanceof Role && $this->editingRole->exists) ? $this->editingRole->id : null;

        return [
            'name' => [
                'required', 'string', 'min:3', 'max:125',
                ValidationRule::unique(config('permission.table_names.roles', 'roles'), 'name')->ignore($roleIdToIgnore),
            ],
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'exists:'.config('permission.table_names.permissions', 'permissions').',id',
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->selectedPermissions = [];
        $roleModelClass = config('permission.models.role');
        $this->editingRole = new $roleModelClass;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }
}
