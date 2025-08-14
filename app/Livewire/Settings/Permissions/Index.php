<?php

namespace App\Livewire\Settings\Permissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

// Consider using a PermissionService for business logic if it grows more complex
// use App\Services\PermissionService;

#[Layout('layouts.app')]
#[Title('Pengurusan Kebenaran')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public bool $isEditMode = false;

    public ?Permission $editingPermission = null;

    public string $name = ''; // For the form input

    public bool $showDeleteConfirmationModal = false;

    public ?int $permissionIdToDelete = null;

    public string $permissionNameToDelete = '';

    protected string $paginationTheme = 'bootstrap';

    // Optional: Inject PermissionService if you implement it
    // protected PermissionService $permissionService;

    // public function boot(PermissionService $permissionService)
    // {
    //     $this->permissionService = $permissionService;
    // }

    public function mount(): void
    {
        // Ensure a 'manage_permissions' gate or permission is defined
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->editingPermission = new Permission; // Initialize for type-hinting and create form
    }

    public function create(): void
    {
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(Permission $permission): void
    {
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $this->resetInputFields(); // Reset before populating for a clean state
        $this->editingPermission = $permission;
        $this->name = $permission->name;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function savePermission(): void
    {
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $validatedData = $this->validate();

        $permissionData = ['name' => $validatedData['name'], 'guard_name' => 'web']; // Standard guard for web

        // Consider moving saving logic to a PermissionService
        // E.g., if ($this->isEditMode) { $this->permissionService->updatePermission($this->editingPermission, $permissionData); }
        // else { $this->permissionService->createPermission($permissionData); }

        if ($this->isEditMode && $this->editingPermission instanceof \Spatie\Permission\Models\Permission && $this->editingPermission->exists) {
            $this->editingPermission->update($permissionData);
            session()->flash('message', __('Kebenaran :name berjaya dikemaskini.', ['name' => $this->editingPermission->name]));
        } else {
            $newPermission = Permission::create($permissionData);
            session()->flash('message', __('Kebenaran :name berjaya dicipta.', ['name' => $newPermission->name]));
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function confirmPermissionDeletion(int $id): void
    {
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        $permission = Permission::find($id);
        if ($permission) {
            // Important: Check if the permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                session()->flash('error', __('Kebenaran ":name" tidak boleh dipadam kerana ia telah ditugaskan kepada peranan.', ['name' => $permission->name]));

                return;
            }

            $this->permissionIdToDelete = $id;
            $this->permissionNameToDelete = $permission->name;
            $this->showDeleteConfirmationModal = true;
        } else {
            session()->flash('error', __('Kebenaran tidak ditemui.'));
        }
    }

    public function deletePermission(): void
    {
        abort_unless(Auth::user()->can('manage_permissions'), 403, __('Tindakan tidak dibenarkan.'));
        if ($this->permissionIdToDelete !== null && $this->permissionIdToDelete !== 0) {
            $permission = Permission::findOrFail($this->permissionIdToDelete); // Use findOrFail to catch if not found

            // Consider moving deletion logic (with checks) to a PermissionService
            // E.g., $result = $this->permissionService->deletePermission($permission); if ($result['error']) ...

            // Double-check if permission is in use before deleting
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

    public function closeDeleteConfirmationModal(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->permissionIdToDelete = null;
        $this->permissionNameToDelete = '';
    }

    public function render()
    {
        $permissions = Permission::orderBy('name')->paginate(10);

        // View path assumes blade file is at resources/views/livewire/settings/permissions/index.blade.php
        return view('livewire.settings.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    protected function rules(): array
    {
        $permissionIdToIgnore = ($this->isEditMode && $this->editingPermission instanceof \Spatie\Permission\Models\Permission && $this->editingPermission->id)
                                ? $this->editingPermission->id
                                : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',    // Ensures permission names are reasonably descriptive
                'max:125',  // Max length based on Spatie's default migration
                ValidationRule::unique('permissions', 'name')->ignore($permissionIdToIgnore),
            ],
        ];
    }

    private function resetInputFields(): void
    {
        $this->name = '';
        $this->editingPermission = new Permission; // Reset to a new instance
        $this->isEditMode = false;
        $this->resetErrorBag(); // Clear validation errors
        $this->resetValidation(); // Reset validation state
    }
}
