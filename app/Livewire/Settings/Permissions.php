<?php

namespace App\Livewire\Settings;

use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.app')] // Standardized layout
class Permissions extends Component
{
  use WithPagination;

  public bool $showModal = false;
  public bool $isEditMode = false;
  public ?Permission $editingPermission = null;
  public ?int $permissionId = null; // Keep track of ID for editing
  public string $name = '';
  public bool $showDeleteConfirmationModal = false;
  public ?int $permissionIdToDelete = null;

  protected string $paginationTheme = 'bootstrap'; // Added for Bootstrap pagination

  protected $listeners = ['closePermissionModalEvent' => 'closeModal'];

  public function mount(): void
  {
    // It's good practice to authorize here, e.g., using a policy
    // $this->authorize('viewAny', Permission::class);
    $this->editingPermission = new Permission();
  }

  public function create(): void
  {
    // $this->authorize('create', Permission::class);
    $this->resetInputFields();
    $this->isEditMode = false;
    $this->showModal = true;
  }

  public function edit(Permission $permission): void
  {
    // $this->authorize('update', $permission);
    $this->resetInputFields();
    $this->editingPermission = $permission;
    $this->name = $permission->name;
    $this->permissionId = $permission->id;
    $this->isEditMode = true;
    $this->showModal = true;
  }

  public function savePermission(): void
  {
    $this->validate();

    $data = ['name' => $this->name, 'guard_name' => 'web']; // Assuming 'web' guard

    if ($this->isEditMode && $this->editingPermission && $this->editingPermission->exists) {
      // $this->authorize('update', $this->editingPermission);
      $this->editingPermission->update($data);
      session()->flash('message', __('Kebenaran berjaya dikemaskini.'));
    } else {
      // $this->authorize('create', Permission::class);
      Permission::create($data);
      session()->flash('message', __('Kebenaran berjaya dicipta.'));
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
    $permission = Permission::find($id);
    // if ($permission) {
    //     $this->authorize('delete', $permission);
    // }
    $this->permissionIdToDelete = $id;
    $this->showDeleteConfirmationModal = true;
  }

  public function deletePermission(): void
  {
    if ($this->permissionIdToDelete) {
      $permission = Permission::findOrFail($this->permissionIdToDelete);
      // $this->authorize('delete', $permission);
      $permission->delete();
      session()->flash('message', __('Kebenaran berjaya dipadam.'));
    }
    $this->showDeleteConfirmationModal = false;
    $this->permissionIdToDelete = null;
  }

  public function render()
  {
    $permissions = Permission::orderBy('name')->paginate(10);
    return view('livewire.settings.permissions', [
      'permissions' => $permissions,
    ]);
  }

  protected function rules(): array
  {
    $permissionIdToIgnore = $this->isEditMode && $this->editingPermission ? $this->editingPermission->id : null;
    return [
      'name' => [
        'required',
        'string',
        'min:3',
        'max:255',
        ValidationRule::unique('permissions', 'name')->ignore($permissionIdToIgnore),
      ],
    ];
  }

  private function resetInputFields(): void
  {
    $this->name = '';
    $this->permissionId = null;
    $this->editingPermission = new Permission();
    $this->isEditMode = false;
    $this->resetErrorBag();
    $this->resetValidation();
  }
}
