<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class Roles extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?Role $editingRole = null;
    public ?int $roleId = null;
    public string $name = '';
    public array $selectedPermissions = [];

    public bool $showDeleteConfirmationModal = false;
    public ?int $roleIdToDelete = null;
    public string $roleNameToDelete = '';

    public $allPermissions = [];

    protected string $paginationTheme = 'bootstrap';

    protected $listeners = ['closeRoleModalEvent' => 'closeModal'];

    protected array $coreRoles = ['Admin', 'BPM Staff', 'IT Admin']; // System Design standardized roles

    public function mount(): void
    {
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }
        $this->editingRole = new Role();
        $this->allPermissions = Permission::orderBy('name')->pluck('name', 'id')->all();
    }

    public function create(): void
    {
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(Role $role): void
    {
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }
        $this->resetInputFields();
        $this->editingRole = $role;
        $this->name = $role->name;
        $this->roleId = $role->id;
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function saveRole(): void
    {
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }
        $this->validate();

        $data = ['name' => $this->name, 'guard_name' => 'web'];

        if ($this->isEditMode && $this->editingRole && $this->editingRole->exists) {
            if (in_array($this->editingRole->getOriginal('name'), $this->coreRoles) && $this->name !== $this->editingRole->getOriginal('name')) {
                session()->flash('error', __('Core role "%roleName" cannot be renamed.', ['roleName' => $this->editingRole->getOriginal('name')]));
                $this->closeModal();
                return;
            }

            $this->editingRole->update($data);
            $this->editingRole->permissions()->sync($this->selectedPermissions);
            session()->flash('message', __('Peranan berjaya dikemaskini.'));
        } else {
            $role = Role::create($data);
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
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }
        $role = Role::find($id);
        if ($role) {
            if (in_array($role->name, $this->coreRoles)) {
                session()->flash('error', __('Core role "%roleName" cannot be deleted.', ['roleName' => $role->name]));
                return;
            }

            $superAdminRoleName = config('permission.default_super_admin_role_name', 'Super Admin');
            if ($role->name === $superAdminRoleName && config('permission.protect_super_admin_role', true)) {
                 session()->flash('error', __('The default Super Admin role cannot be deleted.'));
                 return;
            }

            $this->roleIdToDelete = $id;
            $this->roleNameToDelete = $role->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Peranan tidak ditemui.'));
        }
    }

    public function deleteRole(): void
    {
        if (!Auth::user()->can('manage_roles')) {
            abort(403, 'This action is unauthorized.');
        }

        if ($this->roleIdToDelete) {
            $role = Role::findOrFail($this->roleIdToDelete);

            if (in_array($role->name, $this->coreRoles)) {
                session()->flash('error', __('Core role "%roleName" cannot be deleted.', ['roleName' => $role->name]));
                $this->showDeleteConfirmationModal = false;
                $this->roleIdToDelete = null;
                $this->roleNameToDelete = '';
                return;
            }

            $superAdminRoleName = config('permission.default_super_admin_role_name', 'Super Admin');
            if ($role->name === $superAdminRoleName && config('permission.protect_super_admin_role', true)) {
                 session()->flash('error', __('The default Super Admin role cannot be deleted.'));
                 $this->showDeleteConfirmationModal = false;
                 $this->roleIdToDelete = null;
                 $this->roleNameToDelete = '';
                 return;
            }

            if ($role->users()->count() > 0) {
                session()->flash('error', __('Peranan tidak boleh dipadam kerana ia telah ditugaskan kepada pengguna.'));
                $this->showDeleteConfirmationModal = false;
                $this->roleIdToDelete = null;
                $this->roleNameToDelete = '';
                return;
            }
            $role->delete();
            session()->flash('message', __('Peranan berjaya dipadam.'));
        }
        $this->showDeleteConfirmationModal = false;
        $this->roleIdToDelete = null;
        $this->roleNameToDelete = '';
    }

    public function render()
    {
        // Fetch paginated roles without eager loading counts initially
        $roles = Role::orderBy('name')->paginate(10);

        // Manually load counts for permissions and users for each role in the current page
        // This approach avoids potential boot-time model resolution issues with withCount()
        // but introduces N+1 queries for the counts on each page load.
        $roles->each(function ($role) {
            $role->permissions_count = $role->permissions()->count();
            $role->users_count = $role->users()->count();
        });

        return view('livewire.settings.roles.index', [
          'roles' => $roles,
          'allPermissionsForView' => $this->allPermissions,
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
        $this->editingRole = new Role();
        $this->isEditMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
