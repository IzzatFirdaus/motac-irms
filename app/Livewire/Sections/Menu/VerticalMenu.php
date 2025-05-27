<?php

declare(strict_types=1);

namespace App\Livewire\Sections\Menu;

use App\Models\User; // As per System Design 4.1
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging potential issues
use Livewire\Component;
use Illuminate\View\View;

class VerticalMenu extends Component
{
    public ?string $role = null; // The primary role of the authenticated user
    // public bool $isMobile = false; // Kept from original if specific mobile rendering logic is needed here

    /**
     * Mount the component.
     * Fetches the authenticated user's primary role.
     * $menuData is assumed to be globally shared by MenuServiceProvider.
     * $configData is assumed to be globally shared by AppServiceProvider.
     */
    public function mount(/*bool $isMobile = false*/): void // $isMobile can be passed if needed
    {
        // $this->isMobile = $isMobile;

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            // Fetches the first role. Ensure this is the desired logic for menu filtering.
            // System Design 8.1 specifies role-based access.
            $this->role = $user->getRoleNames()->first();
            if (!$this->role) {
                Log::info('VerticalMenuComponent: User ID ' . $user->id . ' has no roles assigned for menu filtering.');
                // Optionally assign a default role like 'Employee' or 'Guest' if applicable
                // $this->role = 'Employee'; // Example default
            }
        } else {
            // Handle guest users - they might see a very limited menu or specific guest items
            // The verticalMenu.json should define what roles (including a potential 'Guest' role) see which items.
            $this->role = 'Guest'; // Example role for unauthenticated users
            Log::debug('VerticalMenuComponent: No authenticated user. Role set to Guest.');
        }
    }

    /**
     * Render the component's view.
     * The view will use the public $role property and globally shared $menuData / $configData.
     */
    public function render(): View
    {
        return view('livewire.sections.menu.vertical-menu');
    }
}
