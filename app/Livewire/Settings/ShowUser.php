<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')] // Standardized layout
class ShowUser extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        // $this->authorize('view', $user);
        // Eager load relationships that will be displayed
        $this->user = $user->load(['department', 'position', 'grade', 'roles.permissions']);
    }

    public function render()
    {
        return view('livewire.settings.show-user', [
          'user' => $this->user,
        ])->title(__('Maklumat Pengguna') . ' - ' . $this->user->name);
    }
}
