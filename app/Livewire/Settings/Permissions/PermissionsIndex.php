<?php

namespace App\Livewire\Settings\Permissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

/**
 * PermissionsIndex Livewire Component.
 *
 * Handles listing, creating, editing, and deleting permissions for the system.
 * Uses Bootstrap 5 theming and expects the blade view at resources/views/livewire/settings/permissions/permissions-index.blade.php.
 */
#[Layout('layouts.app')]
#[Title('Pengurusan Kebenaran')]
class PermissionsIndex extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Permission $editingPermission = null;

    public string $name = ''; // Permission name (form input)

    public bool $showDeleteConfirmationModal = false;

    public ?int $permissionIdToDelete = null;

    public string $permissionNameToDelete = '';

    protected string $paginationTheme = 'bootstrap';

    /**
     * Ensure user has manage_permissions on mount.
     */
    public function mount(): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->editingPermission = new Permission;
    }

    /**
     * Open the create permission modal.
     */
    public function create(): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal  = true;
    }

    /**
     * Open the edit permission modal.
     */
    public function edit(Permission $permission): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->resetInputFields();
        $this->editingPermission = $permission;
        $this->name              = $permission->name;
        $this->isEditMode        = true;
        $this->showModal         = true;
    }

    /**
     * Save a new or updated permission.
     */
    public function savePermission(): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $validatedData = $this->validate();

        $permissionData = ['name' => $validatedData['name'], 'guard_name' => 'web'];

        if ($this->isEditMode && $this->editingPermission instanceof Permission && $this->editingPermission->exists) {
            $this->editingPermission->update($permissionData);
            session()->flash('message', __('Kebenaran :name berjaya dikemaskini.', ['name' => $this->editingPermission->name]));
        } else {
            $newPermission = Permission::create($permissionData);
            session()->flash('message', __('Kebenaran :name berjaya dicipta.', ['name' => $newPermission->name]));
        }

        $this->closeModal();
    }

    /**
     * Close the modal and reset state.
     */
    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    /**
     * Prompt the delete confirmation modal.
     */
    public function confirmPermissionDeletion(int $id): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $permission = Permission::find($id);
        if ($permission) {
            if ($permission->roles()->count() > 0) {
                session()->flash('error', __('Kebenaran ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada peranan.', ['name' => $permission->name]));

                return;
            }
            $this->permissionIdToDelete        = $id;
            $this->permissionNameToDelete      = $permission->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Kebenaran tidak ditemui.'));
        }
    }

    /**
     * Delete the selected permission.
     */
    public function deletePermission(): void
    {
        abort_unless(Auth::user()?->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        if ($this->permissionIdToDelete !== null && $this->permissionIdToDelete !== 0) {
            $permission = Permission::findOrFail($this->permissionIdToDelete);

            if ($permission->roles()->count() > 0) {
                session()->flash('error', __('Kebenaran ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada peranan.', ['name' => $permission->name]));
                $this->closeDeleteConfirmationModal();

                return;
            }

            $permission->delete();
            session()->flash('message', __('Kebenaran :name berjaya dipadam.', ['name' => $this->permissionNameToDelete]));
        }

        $this->closeDeleteConfirmationModal();
    }

    /**
     * Close the delete confirmation modal.
     */
    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->permissionIdToDelete        = null;
        $this->permissionNameToDelete      = '';
    }

    /**
     * Validation rules for the permission form.
     */
    protected function rules(): array
    {
        $permissionIdToIgnore = ($this->isEditMode && $this->editingPermission instanceof Permission && $this->editingPermission->id)
                                ? $this->editingPermission->id
                                : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:125',
                ValidationRule::unique('permissions', 'name')->ignore($permissionIdToIgnore),
            ],
        ];
    }

    /**
     * Reset all input fields and editing state.
     */
    private function resetInputFields(): void
    {
        $this->name              = '';
        $this->editingPermission = new Permission;
        $this->isEditMode        = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $permissions = Permission::orderBy('name')->paginate(10);

        return view('livewire.settings.permissions.permissions-index', [
            'permissions' => $permissions,
        ]);
    }
}
