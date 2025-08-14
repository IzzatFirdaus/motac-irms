<?php

namespace App\Livewire\ResourceManagement\Admin\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public ?string $filterRole = null; // Store role name or ID

    public array $roleOptions = [];

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class); // Assuming a UserPolicy exists
        $this->roleOptions = Role::orderBy('name')->pluck('name', 'name')->all(); // Use role name for filter value
    }

    public function getUsersProperty()
    {
        $query = User::with(['department:id,name', 'roles:id,name'])
            ->orderBy('name', 'asc');

        if ($this->search !== '' && $this->search !== '0') {
            $query->where(function ($q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('identification_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('department', function ($deptQuery): void {
                        $deptQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->filterRole !== null && $this->filterRole !== '' && $this->filterRole !== '0') {
            $query->whereHas('roles', function ($roleQuery): void {
                $roleQuery->where('name', $this->filterRole);
            });
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

    // Placeholder for navigation to create user page
    public function redirectToCreateUser(): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', User::class);

        return redirect()->route('settings.users.create'); // Assuming you have such a route for the CreateUser component
    }

    // Placeholder for navigation to edit user page
    public function redirectToEditUser(int $userId): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        return redirect()->route('settings.users.edit', $user); // Assuming route for EditUser component
    }

    // Placeholder for delete action - typically involves a confirmation modal
    // For a full implementation, you'd have properties for modal visibility and user to delete.
    // And use Jetstream\DeleteUser action if integrated.
    public function confirmUserDeletion(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);
        // Logic to show a delete confirmation modal
        // $this->userToDeleteId = $userId;
        // $this->showingDeleteConfirmationModal = true;
        $this->dispatch('toastr', type: 'info', message: sprintf('Penghapusan pengguna ID: %d memerlukan pengesahan (logik belum dilaksanakan sepenuhnya).', $userId));
    }

    public function render()
    {
        return view('livewire.resource-management.admin.users.index', [
            'usersList' => $this->users, // Accesses getUsersProperty
            'rolesForFilter' => $this->roleOptions,
        ])->title(__('Pengurusan Pentadbir Pengguna'));
    }
}
