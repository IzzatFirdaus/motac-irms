<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * UsersIndex Livewire Component
 * Handles listing, searching, filtering, and deleting users.
 */
#[Layout('layouts.app')]
#[Title('Pengurusan Pengguna Sistem')]
class UsersIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public ?string $filterRole = null;

    public ?string $filterStatus = null;

    public array $rolesForFilter = [];

    public array $statusOptions = [];

    protected string $paginationTheme = 'bootstrap';

    /**
     * On mount, authorize and load filter data.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
        $this->rolesForFilter = Role::orderBy('name')->pluck('name', 'name')->all();
        $this->statusOptions  = User::getStatusOptions();
    }

    /**
     * Computed property for paginated, filtered users list.
     */
    public function getUsersListProperty()
    {
        $query = User::select([
            'id', 'name', 'email', 'identification_number', 'status', 'title', 'department_id', 'profile_photo_path', /* , 'motac_email' */
        ])
            ->with(['department:id,name', 'roles:id,name'])
            ->orderBy('name', 'asc');

        if ($this->search !== '' && $this->search !== '0') {
            $query->where(function ($q): void {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function ($deptQuery): void {
                        $deptQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterRole !== null && $this->filterRole !== '' && $this->filterRole !== '0') {
            $query->whereHas('roles', function ($roleQuery): void {
                $roleQuery->where('name', $this->filterRole);
            });
        }

        if ($this->filterStatus !== null && $this->filterStatus !== '' && $this->filterStatus !== '0') {
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

    /**
     * Redirect to create user page.
     */
    public function redirectToCreateUser(): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', User::class);

        return redirect()->route('settings.users.create');
    }

    /**
     * Confirm deletion of the user by dispatching the event for modal.
     */
    public function confirmUserDeletion(int $userId, string $userName): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak boleh memadam akaun anda sendiri.'));

            return;
        }

        $this->dispatch('open-delete-modal', [
            'id'              => $userId,
            'itemDescription' => __('pengguna') . ' ' . $userName,
            'deleteMethod'    => 'deleteUser',
            'modelClass'      => User::class,
        ]);
    }

    /**
     * Actually delete the user.
     */
    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            session()->flash('error', __('Anda tidak boleh memadam akaun anda sendiri.'));

            return;
        }

        $userName = $user->name;
        $user->delete();

        session()->flash('message', __('Pengguna :name berjaya dipadam.', ['name' => $userName]));
    }

    /**
     * Render the users index view.
     */
    public function render()
    {
        return view('livewire.settings.users.users-index', [
            'usersList'      => $this->getUsersListProperty(),
            'rolesForFilter' => $this->rolesForFilter,
            'statusOptions'  => $this->statusOptions,
        ]);
    }
}
