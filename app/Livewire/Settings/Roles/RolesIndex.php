<?php

namespace App\Livewire\Settings\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * RolesIndex Livewire Component
 * Handles listing, creating, editing, and deleting roles and assigning permissions.
 */
#[Layout('layouts.app')]
#[Title('Pengurusan Peranan')]
class RolesIndex extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Role $editingRole = null;

    public ?Role $deletingRole = null;

    public string $name = '';

    public array $selectedPermissions = [];

    public bool $showDeleteConfirmationModal = false;

    public ?int $roleIdToDelete = null;

    public string $roleNameToDelete = '';

    public array $allPermissionsForView = [];

    public array $coreRoles = [];

    protected string $paginationTheme = 'bootstrap';

    /**
     * Component mount: authorization, setup core roles and permission dropdown.
     */
    public function mount(): void
    {
        abort_unless(Auth::user()?->can('manage_roles'), 403, __('Tindakan tidak dibenarkan.'));
        $this->coreRoles   = config('motac.core_roles', ['Admin', 'BPM Staff', 'IT Admin', 'User']);
        $roleModelClass    = config('permission.models.role');
        $this->editingRole = new $roleModelClass;
        $this->loadAllPermissions();
    }

    /**
     * Computed property for roles list (paginated).
     */
    public function getRolesProperty()
    {
        $roleModelClass = config('permission.models.role');

        return $roleModelClass::query()
            ->withCount([
                'permissions',
                'users as users_count',
            ])
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Computed property for all permissions (for dropdowns).
     */
    public function getAllPermissionsForDropdownProperty(): array
    {
        return Permission::orderBy('name')->pluck('name', 'id')->all();
    }

    protected function loadAllPermissions(): void
    {
        $this->allPermissionsForView = $this->getAllPermissionsForDropdownProperty();
    }

    /**
     * Open the modal for creating a new role.
     */
    public function createRole(): void
    {
        $this->authorize('create', config('permission.models.role'));
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal  = true;
    }

    /**
     * Store the new role.
     */
    public function storeRole(): void
    {
        $this->authorize('create', config('permission.models.role'));
        $this->validate();

        $roleModelClass = config('permission.models.role');
        $role           = $roleModelClass::create(['name' => $this->name, 'guard_name' => 'web']);
        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('toastr', type: 'success', message: __('Peranan berjaya dicipta!'));
        $this->closeModal();
    }

    /**
     * Open the modal for editing a role.
     */
    public function editRole(int $roleId): void
    {
        $this->authorize('update', config('permission.models.role'));
        $roleModelClass    = config('permission.models.role');
        $this->editingRole = $roleModelClass::findById($roleId, 'web');

        // Prevent editing core roles or roles with users
        if (
            in_array($this->editingRole->name, $this->coreRoles, true)
            || $this->editingRole->users()->exists()
        ) {
            $this->dispatch('toastr', type: 'error', message: 'Tidak boleh mengedit peranan sistem atau peranan yang mempunyai pengguna bersekutu.');
            $this->closeModal();

            return;
        }

        $this->name                = $this->editingRole->name;
        $this->selectedPermissions = $this->editingRole->permissions()->pluck('id')->toArray();
        $this->isEditMode          = true;
        $this->showModal           = true;
    }

    /**
     * Update the selected role.
     */
    public function updateRole(): void
    {
        $this->authorize('update', config('permission.models.role'));
        $this->validate();

        if (
            in_array($this->editingRole->name, $this->coreRoles, true)
            || $this->editingRole->users()->exists()
        ) {
            $this->dispatch('toastr', type: 'error', message: 'Tidak boleh mengemaskini peranan sistem atau peranan yang mempunyai pengguna bersekutu.');
            $this->closeModal();

            return;
        }

        $this->editingRole->name = $this->name;
        $this->editingRole->save();
        $this->editingRole->syncPermissions($this->selectedPermissions);

        $this->dispatch('toastr', type: 'success', message: __('Peranan berjaya dikemaskini!'));
        $this->closeModal();
    }

    /**
     * Prompt for role deletion.
     */
    public function confirmDelete(int $roleId): void
    {
        $this->authorize('delete', config('permission.models.role'));

        $roleModelClass     = config('permission.models.role');
        $this->deletingRole = $roleModelClass::findById($roleId, 'web');

        if (! $this->deletingRole) {
            $this->dispatch('toastr', type: 'error', message: __('Peranan tidak ditemui.'));
            $this->closeDeleteConfirmationModal();

            return;
        }

        // Prevent deleting core roles or roles with associated users
        if ($this->deletingRole->users()->exists() || in_array($this->deletingRole->name, $this->coreRoles, true)) {
            $this->dispatch('toastr', type: 'error', message: __('Tidak boleh memadam peranan sistem atau peranan yang mempunyai pengguna bersekutu.'));
            $this->closeDeleteConfirmationModal();

            return;
        }

        $this->roleIdToDelete              = $roleId;
        $this->roleNameToDelete            = $this->deletingRole->name;
        $this->showDeleteConfirmationModal = true;
        $this->dispatch('openModal', elementId: 'deleteConfirmationModal');
    }

    /**
     * Delete the selected role.
     */
    public function deleteRole(): void
    {
        $this->authorize('delete', config('permission.models.role'));

        if ($this->roleIdToDelete) {
            $roleModelClass = config('permission.models.role');
            $role           = $roleModelClass::findById($this->roleIdToDelete, 'web');

            if ($role) {
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
        $this->resetPage();
    }

    /**
     * Close any open modals and reset fields.
     */
    public function closeModal(): void
    {
        $this->showModal                   = false;
        $this->showDeleteConfirmationModal = false;
        $this->resetInputFields();
        $this->resetErrorBag();
    }

    /**
     * Close the delete confirmation modal and reset state.
     */
    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->roleIdToDelete              = null;
        $this->roleNameToDelete            = '';
        $this->resetErrorBag();
    }

    /**
     * Render the Livewire view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.roles.roles-index', [
            'roles'                 => $this->roles,
            'allPermissionsForView' => $this->allPermissionsForView,
            'coreRoles'             => $this->coreRoles,
        ]);
    }

    /**
     * Validation rules for the form.
     */
    protected function rules(): array
    {
        $roleIdToIgnore = ($this->isEditMode && $this->editingRole instanceof Role && $this->editingRole->exists) ? $this->editingRole->id : null;

        return [
            'name' => [
                'required', 'string', 'min:3', 'max:125',
                ValidationRule::unique(config('permission.table_names.roles', 'roles'), 'name')->ignore($roleIdToIgnore),
            ],
            'selectedPermissions'   => 'nullable|array',
            'selectedPermissions.*' => 'exists:'.config('permission.table_names.permissions', 'permissions').',id',
        ];
    }

    /**
     * Reset form fields and editing state.
     */
    private function resetInputFields(): void
    {
        $this->name                = '';
        $this->selectedPermissions = [];
        $roleModelClass            = config('permission.models.role');
        $this->editingRole         = new $roleModelClass;
        $this->isEditMode          = false;
        $this->resetErrorBag();
    }
}
