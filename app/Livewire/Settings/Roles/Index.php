<?php

namespace App\Livewire\Settings\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
// use Livewire\Attributes\Title; // Title is set in Blade view using @section
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

// use Spatie\Permission\Models\Role; // We will use the configured model string

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?\Spatie\Permission\Contracts\Role $editingRole = null; // Use contract for broader compatibility

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

        $this->allPermissionsForView = Permission::orderBy('name')->pluck('name', 'id')->all();
    }

    public function create(): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
        $this->dispatch('showRoleModal');
    }

    public function edit(int $roleId): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $roleModelClass = config('permission.models.role');
        /** @var \Spatie\Permission\Contracts\Role $role */
        $role = $roleModelClass::findOrFail($roleId);

        $this->resetInputFields(); // Reset before populating
        $this->editingRole = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($id): string => (string) $id)->toArray();
        $this->isEditMode = true;
        $this->showModal = true;
        $this->dispatch('showRoleModal');
    }

    public function saveRole(): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $validatedData = $this->validate();
        $roleData = ['name' => $validatedData['name'], 'guard_name' => 'web'];
        $roleModelClass = config('permission.models.role');

        if ($this->isEditMode && $this->editingRole instanceof \Spatie\Permission\Contracts\Role && $this->editingRole->exists) {
            if (in_array($this->editingRole->getOriginal('name'), $this->coreRoles) && $this->name !== $this->editingRole->getOriginal('name')) {
                session()->flash('error', __('Peranan teras ":roleName" tidak boleh dinamakan semula.', ['roleName' => $this->editingRole->getOriginal('name')]));
                $this->name = $this->editingRole->getOriginal('name');

                return;
            }

            $this->editingRole->update($roleData);
            $this->editingRole->permissions()->sync($this->selectedPermissions);
            session()->flash('message', __('Peranan :name berjaya dikemaskini.', ['name' => $this->editingRole->name]));
        } else {
            /** @var \Spatie\Permission\Contracts\Role $role */
            $role = $roleModelClass::create($roleData);
            $role->permissions()->sync($this->selectedPermissions);
            session()->flash('message', __('Peranan :name berjaya dicipta.', ['name' => $role->name]));
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
        $this->dispatch('hideRoleModal');
    }

    public function confirmRoleDeletion(int $id): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $roleModelClass = config('permission.models.role');
        /** @var \Spatie\Permission\Contracts\Role $role */
        $role = $roleModelClass::find($id);

        if ($role) {
            if (in_array($role->name, $this->coreRoles) || ($role->name === config('permission.super_admin_name', 'Super Admin') && config('permission.protect_super_admin_role', true))) {
                session()->flash('error', __('Peranan teras atau Super Admin ":roleName" tidak boleh dipadam.', ['roleName' => $role->name]));

                return;
            }

            if ($role->users()->count() > 0) {
                session()->flash('error', __('Peranan tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.'));

                return;
            }

            $this->roleIdToDelete = $id;
            $this->roleNameToDelete = $role->name;
            $this->showDeleteConfirmationModal = true;
            $this->dispatch('showDeleteRoleConfirmationModal');
        } else {
            session()->flash('error', __('Peranan tidak ditemui.'));
        }
    }

    public function deleteRole(): void
    {
        abort_unless(Auth::user()->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        if ($this->roleIdToDelete !== null && $this->roleIdToDelete !== 0) {
            $roleModelClass = config('permission.models.role');
            /** @var \Spatie\Permission\Contracts\Role $role */
            $role = $roleModelClass::findOrFail($this->roleIdToDelete);

            if (in_array($role->name, $this->coreRoles) ||
                ($role->name === config('permission.super_admin_name', 'Super Admin') && config('permission.protect_super_admin_role', true)) ||
                $role->users()->count() > 0) {
                session()->flash('error', __('Pemadaman peranan :roleName tidak dibenarkan atau peranan masih mempunyai pengguna.', ['roleName' => $role->name]));
                $this->closeDeleteConfirmationModal();

                return;
            }

            $role->delete();
            session()->flash('message', __('Peranan :name berjaya dipadam.', ['name' => $this->roleNameToDelete]));
        }

        $this->closeDeleteConfirmationModal();
    }

    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->roleIdToDelete = null;
        $this->roleNameToDelete = '';
        $this->dispatch('hideDeleteRoleConfirmationModal');
    }

    public function render()
    {
        $roleModelClass = config('permission.models.role');

        $roles = $roleModelClass::withCount([
            'permissions',
            'users as users_count', // Assumes your Role model has a 'users' relationship
        ])
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.roles.index', [
            'roles' => $roles,
            'allPermissionsForView' => $this->allPermissionsForView,
        ]);
    }

    protected function rules(): array
    {
        $roleIdToIgnore = ($this->isEditMode && $this->editingRole instanceof \Spatie\Permission\Contracts\Role && $this->editingRole->exists) ? $this->editingRole->id : null;

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
        $this->resetValidation();
    }
}
