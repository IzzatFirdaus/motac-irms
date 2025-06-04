<?php

namespace App\Livewire\Sections\Menu;

use App\Models\User; // Used in VerticalMenu.php
use Illuminate\Support\Facades\Auth; // Used in VerticalMenu.php
use Livewire\Component;

class VerticalMenu extends Component
{
  public $role = null;

  public function mount()
  {
    // Fetches the first role name of the currently authenticated user.
    // This $role property will be available in the livewire view.
    $this->role = User::find(Auth::id())?->getRoleNames()->first(); // [cite: 1]
  }

  public function render()
  {
    // Renders the Livewire view for the vertical menu.
    return view('livewire.sections.menu.vertical-menu'); // [cite: 1]
  }
}
