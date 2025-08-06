<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * UsersShow Livewire Component
 * Displays a single user's details.
 */
#[Layout('layouts.app')]
#[Title('Maklumat Pengguna')]
class UsersShow extends Component
{
    use AuthorizesRequests;

    public User $user;

    /**
     * Authorize and eager load relationships.
     */
    public function mount(User $user): void
    {
        $this->authorize('view', $user);
        $this->user = $user->load(['department', 'position', 'grade', 'roles.permissions', 'creator', 'updater', 'deleter']);
    }

    /**
     * Render the user show view.
     */
    public function render()
    {
        return view('livewire.settings.users.users-show', [
            'userToShow' => $this->user,
        ]);
    }
}
