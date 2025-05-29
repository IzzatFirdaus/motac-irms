<?php

namespace App\Livewire\Sections\Menu;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class VerticalMenu extends Component
{
    public ?string $role = null;
    public mixed $menuData = [];

    public function mount(): void
    {
        $this->initializeUserRole();
        $this->loadMenuData();
    }

    /**
     * Determine and set the current user's role.
     */
    protected function initializeUserRole(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && method_exists($user, 'getRoleNames')) {
            $this->role = $user->getRoleNames()->first();
        } else {
            $this->role = null;
        }
    }

    /**
     * Load menu data from config/menu.php or fallback.
     */
    protected function loadMenuData(): void
    {
        $this->menuData = config('menu') ?? []; // Assumes you define structured menu in config/menu.php
    }

    /**
     * Render the vertical menu component view.
     */
    public function render(): View
    {
        return view('livewire.sections.menu.vertical-menu', [
            'menuData' => $this->menuData,
        ]);
    }
}
