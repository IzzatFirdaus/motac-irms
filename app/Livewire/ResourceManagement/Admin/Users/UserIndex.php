<?php

namespace App\Livewire\ResourceManagement\Admin\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * UserIndex Livewire component for user admin listing.
 * Handles listing, searching, filtering, and navigation for user management.
 */
#[Layout('layouts.app')]
#[Title('Pengurusan Pentadbir Pengguna')]
class UserIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public ?string $filterRole = null; // Store role name
    public ?string $filterStatus = null; // Store user status
    public array $roleOptions = [];

    protected string $paginationTheme = 'bootstrap';

    /**
     * Mount and authorize, populate role options.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
        $this->roleOptions = Role::orderBy('name')->pluck('name', 'name')->all();
    }

    /**
     * Computed property: get paginated users, with search and filters.
     */
    public function getUsersProperty()
    {
        $query = User::with(['department:id,name', 'roles:id,name'])
            ->orderBy('name', 'asc');

        // Search logic for name, email, IC, or department name.
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

        // Role filter
        if ($this->filterRole !== null && $this->filterRole !== '' && $this->filterRole !== '0') {
            $query->whereHas('roles', function ($roleQuery): void {
                $roleQuery->where('name', $this->filterRole);
            });
        }

        // Status filter (ACTIVE/INACTIVE). This was added as per your Blade.
        if ($this->filterStatus !== null && $this->filterStatus !== '' && $this->filterStatus !== '0') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(15);
    }

    /**
     * Reset pagination on search update.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination on role filter update.
     */
    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination on status filter update.
     */
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Navigate to the create user page.
     */
    public function redirectToCreateUser(): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', User::class);
        return redirect()->route('settings.users.create'); // Assumes the route exists
    }

    /**
     * Navigate to the edit user page for a specific user.
     */
    public function redirectToEditUser(int $userId): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);
        return redirect()->route('settings.users.edit', $user);
    }

    /**
     * Example placeholder for user deletion confirmation.
     */
    public function confirmUserDeletion(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);
        $this->dispatch('toastr', type: 'info', message: sprintf('Penghapusan pengguna ID: %d memerlukan pengesahan (logik belum dilaksanakan sepenuhnya).', $userId));
    }

    /**
     * Render the user admin index view.
     */
    public function render()
    {
        return view('livewire.resource-management.admin.users.user-index', [
            'usersList' => $this->users,
            'rolesForFilter' => $this->roleOptions,
        ]);
    }
}
