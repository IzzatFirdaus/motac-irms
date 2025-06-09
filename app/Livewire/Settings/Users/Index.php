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

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
        $this->rolesForFilter = Role::orderBy('name')->pluck('name', 'name')->all();
        $this->statusOptions = User::getStatusOptions();
    }

    public function getUsersListProperty()
    {
        // Corrected query chain:
        // 1. Use an array for User::select([...]) for clarity and robustness.
        // 2. Removed duplicated ->with() and ->orderBy() calls.
        // 3. Ensured the method chaining is continuous.
        $query = User::select([
            'id',
            'name',
            'email',
            'motac_email',
            'identification_number',
            'status',
            'title',
            'department_id',
            'profile_photo_path',
        ])
            ->with(['department:id,name', 'roles:id,name'])
            ->orderBy('name', 'asc');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('identification_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('department', function ($deptQuery) {
                        $deptQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if (! empty($this->filterRole)) {
            $query->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', $this->filterRole);
            });
        }

        if (! empty($this->filterStatus)) {
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
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak boleh memadam akaun anda sendiri.'));

            return;
        }

        $this->dispatch('open-delete-modal', [
            'id' => $userId,
            'itemDescription' => __('pengguna').' '.$userName,
            'deleteMethod' => 'deleteUser',
            'modelClass' => User::class, // Note: 'modelClass' is dispatched but not used by your Alpine modal's x-data
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

        $userName = $user->name;
        $user->delete();

        session()->flash('message', __('Pengguna :name berjaya dipadam.', ['name' => $userName]));
    }

    public function render()
    {
        return view('livewire.settings.users.index', [
            'usersList' => $this->usersList,
            'rolesForFilter' => $this->rolesForFilter, // This was already being passed correctly
            'statusOptions' => $this->statusOptions,   // This was already being passed correctly
        ]);
    }
}
