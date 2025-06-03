<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
// Consider using a UserService for business logic if it grows complex
// use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Pengurusan Pengguna Sistem')]
class Index extends Component
{
  use AuthorizesRequests;
  use WithPagination;

  public string $search = '';
  public ?string $filterRole = null;
  public ?string $filterStatus = null;

  public array $rolesForFilter = [];
  public array $statusOptions = [];

  protected string $paginationTheme = 'bootstrap';

  // Optional: Inject UserService
  // protected UserService $userService;

  // public function boot(UserService $userService)
  // {
  //     $this->userService = $userService;
  // }

  public function mount(): void
  {
    $this->authorize('viewAny', User::class);
    $this->rolesForFilter = Role::orderBy('name')->pluck('name', 'name')->all(); // Using role name as key and value for filter
    $this->statusOptions = User::getStatusOptions();
  }

  public function getUsersListProperty() // Renamed from getUsersProperty for clarity with Livewire conventions
  {
    // Consider moving complex query logic to UserService or a UserRepository
    $query = User::with(['department:id,name', 'roles:id,name'])
      ->orderBy('name', 'asc');

    if (!empty($this->search)) {
      $query->where(function ($q) {
        $q->where('name', 'like', '%' . $this->search . '%')
          ->orWhere('email', 'like', '%' . $this->search . '%')
          ->orWhere('identification_number', 'like', '%' . $this->search . '%')
          ->orWhereHas('department', function ($deptQuery) {
            $deptQuery->where('name', 'like', '%' . $this->search . '%');
          });
      });
    }

    if (!empty($this->filterRole)) {
      $query->whereHas('roles', function ($roleQuery) {
        $roleQuery->where('name', $this->filterRole);
      });
    }

    if (!empty($this->filterStatus)) {
      $query->where('status', $this->filterStatus);
    }

    return $query->paginate(15);
  }

  public function updatingSearch(): void
  {
    $this->resetPage();
  }
  public function updatedFilterRole(): void
  {
    $this->resetPage();
  }
  public function updatedFilterStatus(): void
  {
    $this->resetPage();
  }

  public function redirectToCreateUser(): \Illuminate\Http\RedirectResponse
  {
    $this->authorize('create', User::class);
    return redirect()->route('settings.users.create');
  }

  public function confirmUserDeletion(int $userId, string $userName): void
  {
    // Authorization check is done again in deleteUser, which is good practice
    $user = User::findOrFail($userId); // Ensure user exists before dispatching
    $this->authorize('delete', $user);


    if (Auth::id() === $user->id) {
      $this->dispatch('toastr', type: 'error', message: __('Anda tidak boleh memadam akaun anda sendiri.'));
      return;
    }

    $this->dispatch('open-delete-modal', [
      'id' => $userId,
      'itemDescription' => __('pengguna') . ' ' . $userName,
      'deleteMethod' => 'deleteUser', // Ensure your modal component calls this method
      'modelClass' => User::class // Can be used by a generic modal to show what's being deleted
    ]);
  }

  public function deleteUser(int $userId): void
  {
    $user = User::findOrFail($userId);
    $this->authorize('delete', $user);

    if (Auth::id() === $user->id) {
      session()->flash('error', __('Anda tidak boleh memadam akaun anda sendiri.'));
      return;
    }

    // Consider moving deletion logic to a UserService
    // E.g., $this->userService->deleteUser($user);
    $userName = $user->name;
    $user->delete(); // Performs soft delete if User model uses SoftDeletes trait

    session()->flash('message', __('Pengguna :name berjaya dipadam.', ['name' => $userName]));
    // If your modal needs an explicit close, dispatch an event
    // $this->dispatch('close-delete-modal');
    // Refresh the list
    // $this->resetPage(); // Or trigger a refresh of the usersList property if needed
  }

  public function render()
  {
    // View path assumes your Blade file is at resources/views/livewire/settings/users/index.blade.php
    return view('livewire.settings.users.index', [
      'usersList' => $this->usersList, // Uses the computed property
      'rolesForFilter' => $this->rolesForFilter,
      'statusOptions' => $this->statusOptions,
    ]);
  }
}
