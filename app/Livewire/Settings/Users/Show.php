<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import AuthorizesRequests trait

#[Layout('layouts.app')]
#[Title('Maklumat Pengguna')] // - Fixed: Use a static string for broader Livewire 3 compatibility
class Show extends Component
{
  use AuthorizesRequests; // - Use AuthorizesRequests trait for $this->authorize()

  public User $user;

  // Removed getPageTitle method as it's no longer used for the #[Title] attribute directly
  // public function getPageTitle(): string
  // {
  //   return __('Maklumat Pengguna') . ' - ' . $this->user->name;
  // }

  public function mount(User $user): void
  {
    // Use $this->authorize() for consistency and better error handling
    // System Design Reference: Policies define authorization logic for actions on specific models
    $this->authorize('view', $user);

    // Eager load relationships for display as per SDD
    // Ensure 'creator', 'updater', 'deleter' relationships are defined in User model (via Blameable trait)
    $this->user = $user->load(['department', 'position', 'grade', 'roles.permissions', 'creator', 'updater', 'deleter']);
  }

  public function render()
  {
    // View path assumes your Blade file is at resources/views/livewire/settings/users/show.blade.php
    return view('livewire.settings.users.show', [
      'userToShow' => $this->user, // Pass the loaded user model to the view
    ]);
  }
}
