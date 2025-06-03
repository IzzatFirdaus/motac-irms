<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title(method: 'getPageTitle')] // Using method for dynamic title
class Show extends Component
{
  public User $user;

  public function getPageTitle(): string
  {
    return __('Maklumat Pengguna') . ' - ' . $this->user->name;
  }

  public function mount(User $user): void
  {
    abort_unless(Auth::user()->can('view', $user), 403, __('Tindakan tidak dibenarkan.'));
    // Eager load relationships for display
    $this->user = $user->load(['department', 'position', 'grade', 'roles.permissions']);
  }

  public function render()
  {
    // View path assumes your Blade file is at resources/views/livewire/settings/users/show.blade.php
    return view('livewire.settings.users.show', [
      'userToShow' => $this->user, // Pass the loaded user model to the view
    ]);
  }
}
