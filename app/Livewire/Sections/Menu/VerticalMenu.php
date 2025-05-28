<?php

namespace App\Livewire\Sections\Menu;

use App\Models\User; // Ensure this model exists and is correctly namespaced
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component; // Optional: for debugging roles

class VerticalMenu extends Component
{
    public ?string $role = null; // Type hint for clarity

    public function mount()
    {
        if (Auth::check()) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user(); // More direct way to get the authenticated user model
            if ($user && method_exists($user, 'getRoleNames')) {
                $this->role = $user->getRoleNames()->first(); // Assumes Spatie permissions or similar
                // Log::debug('VerticalMenu: User role set to ' . $this->role); // Optional debugging
            } else {
                // Log::warning('VerticalMenu: User authenticated but role could not be determined or getRoleNames method missing.'); // Optional
                $this->role = null; // Default if user has no roles or method is missing
            }
        }
    }

    public function render()
    {
        // The view will receive $this->role
        return view('livewire.sections.menu.vertical-menu');
    }
}
