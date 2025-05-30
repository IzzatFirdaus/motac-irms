<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class VerticalMenu extends Component
{
    /**
     * Role of the current authenticated user.
     *
     * @var string|null
     */
    public ?string $role = null;

    /**
     * Lifecycle hook: Called once when the component is initialized.
     * Determines and sets the user role.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->initializeUserRole();

        // Optional for debugging or system audit trails
        Log::debug('[VerticalMenu] Component mounted. Role: ' . ($this->role ?? 'None'));
    }

    /**
     * Sets the role property based on the authenticated user's first assigned role.
     * Defaults to null if no user or role data is found.
     *
     * @return void
     */
    protected function initializeUserRole(): void
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'getRoleNames')) {
            $this->role = $user->getRoleNames()->first();
        } else {
            $this->role = null;
        }
    }

    /**
     * Renders the vertical menu view, which uses globally shared $menuData.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('livewire.sections.menu.vertical-menu');
    }
}
