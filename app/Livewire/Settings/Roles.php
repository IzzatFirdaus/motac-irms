<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule as ValidationRule;

#[Layout('layouts.app')]
class Roles extends Component
{
  use WithPagination;

  public bool $showModal = false;
  public bool $isEditMode = false;
  public ?Role $editingRole = null;
  public ?int $roleId = null; // Keep track of ID for editing
  public string $name = '';
  public array $selectedPermissions = []; // For assigning permissions to role

  public bool $showDeleteConfirmationModal = false;
  public ?int $roleIdToDelete = null;

  public $allPermissions = [];

  protected string $paginationTheme = 'bootstrap';

  protected $listeners = ['closeRoleModalEvent' => 'closeModal'];

  public function mount(): void
  {
    // $this->authorize('viewAny', Role::class);
    $this->editingRole = new Role();
    $this->allPermissions = Permission::orderBy('name')->pluck('name', 'id')->all();
  }

  public function create(): void
  {
    // $this->authorize('create', Role::class);
    $this->resetInputFields();
    $this->isEditMode = false;
    $this->showModal = true;
  }

  public function edit(Role $role): void
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
      // $this->authorize('create', Role::class);
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
    // $role = Role::find($id);
    // if ($role) {
    //     $this->authorize('delete', $role);
    // }
    $this->roleIdToDelete = $id;
    $this->showDeleteConfirmationModal = true;
  }

  public function deleteRole(): void
  {
    if ($this->roleIdToDelete) {
      $role = Role::findOrFail($this->roleIdToDelete);
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
    $roles = Role::withCount('permissions', 'users')->orderBy('name')->paginate(10);
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
        ValidationRule::unique('roles', 'name')->ignore($roleIdToIgnore),
      ],
      'selectedPermissions' => 'nullable|array',
      'selectedPermissions.*' => 'exists:permissions,id', // Validate permissions by ID
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
