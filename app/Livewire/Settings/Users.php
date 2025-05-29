<?php

namespace App\Livewire\Settings; // Changed namespace to match the component's purpose

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role; // Assuming roles are still relevant for settings user view

#[Layout('layouts.app')]
class Users extends Component // Renamed class from Index to Users to match typical main component name
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public ?string $filterRole = null;
    public array $roleOptions = [];

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class); // General permission to view users in settings
        $this->roleOptions = Role::orderBy('name')->pluck('name', 'name')->all();
    }

    public function getUsersProperty() // Renamed from getUsersListProperty for convention
    {
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

        return $query->paginate(15); // Or your preferred pagination size
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function redirectToCreateUser(): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', User::class);
        // Assuming 'settings.users.create' route points to a Livewire component like App\Livewire\Settings\CreateUser
        return redirect()->route('settings.users.create');
    }

    public function redirectToEditUser(int $userId): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);
         // Assuming 'settings.users.edit' route points to a Livewire component like App\Livewire\Settings\EditUser
        return redirect()->route('settings.users.edit', $user);
    }

    public function confirmUserDeletion(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);
        // This would typically set properties to show a confirmation modal
        // For now, dispatching a toastr message as a placeholder
        $this->dispatch('toastr', type: 'info', message: "Penghapusan pengguna ID: {$userId} memerlukan pengesahan (sila laksanakan logik modal).");
        // Example:
        // $this->dispatch('open-delete-confirmation-modal', ['userId' => $userId, 'userName' => $user->name]);
    }

    public function render()
    {
        // This component now directly renders the user list view
        return view('livewire.settings.users', [ // Ensure this view path is correct
            'users' => $this->users, // Accesses getUsersProperty
            'rolesForFilter' => $this->roleOptions,
        ])->title(__('Pengurusan Pengguna Sistem'));
    }
}
