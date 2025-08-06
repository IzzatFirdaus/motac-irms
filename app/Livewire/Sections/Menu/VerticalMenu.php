<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire VerticalMenu component
 * Loads the menu structure from config/menu.php and exposes it for the Blade view.
 * Handles role-based visibility and supports recursive submenus.
 */
class VerticalMenu extends Component
{
    public $menuData;
    public $configData = [];

    /**
     * Mount the component and initialize its properties.
     * Loads global app config and menu data.
     */
    public function mount(): void
    {
        // Load global theme configuration
        $this->configData = \App\Helpers\Helpers::appClasses();

        // Load menu from PHP config
        $menuConfig = config('menu');
        $this->menuData = (object) $menuConfig;

        // Ensure menuData->menu is always array
        if (!isset($this->menuData->menu) || !is_array($this->menuData->menu)) {
            $this->menuData->menu = [];
        }
    }

    /**
     * Exposes the user's primary role for use in Blade.
     * If multiple roles, returns the first.
     */
    public function getUserRoleProperty()
    {
        if (!Auth::check()) {
            return null;
        }
        return Auth::user()->getRoleNames()->first();
    }

    /**
     * Exposes the current route name for active checks in Blade.
     */
    public function getCurrentRouteNameProperty()
    {
        return \Route::currentRouteName();
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.sections.menu.vertical-menu', [
            'menuData' => $this->menuData,
            'configData' => $this->configData,
            'role' => $this->userRole,
            'currentRouteName' => $this->currentRouteName,
        ]);
    }
}
