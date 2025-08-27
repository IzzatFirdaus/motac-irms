<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire VerticalMenu component
 * Loads the menu structure from config/menu.php and exposes it for the Blade view.
 * Handles role-based visibility, guest-only items, and recursive submenus.
 */
/**
 * @property-read string|null $userRole
 * @property-read string|null $currentRouteName
 * @property-read array $filteredMenuData
 */
class VerticalMenu extends Component
{
    public $menuData;

    public $configData = [];

    /**
     * Mount the component and initialize its properties.
     */
    public function mount(): void
    {
        // Load global theme configuration
        $this->configData = \App\Helpers\Helpers::appClasses();

        // Load menu from PHP config
        $menuConfig     = config('menu');
        $this->menuData = (object) $menuConfig;

        // Ensure menuData->menu is always an array
        if (! isset($this->menuData->menu) || ! is_array($this->menuData->menu)) {
            $this->menuData->menu = [];
        }
    }

    /**
     * Returns the first role of the authenticated user or null if guest.
     */
    public function getUserRoleProperty()
    {
        if (! Auth::check()) {
            return null;
        }

        return Auth::user()->getRoleNames()->first();
    }

    /**
     * Returns the current route name for active checks in Blade.
     */
    public function getCurrentRouteNameProperty()
    {
        return \Route::currentRouteName();
    }

    /**
     * Filters the menu based on guest/authenticated status and role.
     * - Guests only see items with 'guestOnly' => true.
     * - Authenticated users only see items with their role in 'role' and NOT guestOnly.
     * - Submenus and headers are recursively filtered.
     */
    public function getFilteredMenuDataProperty()
    {
        $role = $this->userRole;
        $menu = $this->menuData->menu ?? [];

        $filterMenu = function ($items) use (&$filterMenu, $role) {
            $filtered = [];

            foreach ($items as $item) {
                $item = (object) $item;

                // Show guestOnly items ONLY to guests
                if (! Auth::check()) {
                    if (isset($item->guestOnly) && $item->guestOnly) {
                        // Recursively filter submenu for guests (if any)
                        if (isset($item->submenu) && is_array($item->submenu)) {
                            $item->submenu = $filterMenu($item->submenu);
                        }
                        $filtered[] = $item;
                    }

                    continue;
                }

                // Hide guestOnly items from authenticated users
                if (isset($item->guestOnly) && $item->guestOnly) {
                    continue;
                }

                // Authenticated users: Admin sees all, others according to role
                $canView = false;
                if (
                    $role === 'Admin' || (isset($item->role) && in_array($role, (array) $item->role))
                ) {
                    $canView = true;
                } elseif (! isset($item->role) && ! isset($item->guestOnly)) {
                    // If item has no role and no guestOnly, allow for all authenticated users
                    $canView = true;
                }

                if (! $canView) {
                    continue;
                }

                // Recursively filter submenu if present
                if (isset($item->submenu) && is_array($item->submenu)) {
                    $item->submenu = $filterMenu($item->submenu);
                }
                $filtered[] = $item;
            }

            return $filtered;
        };

        return $filterMenu($menu);
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.sections.menu.vertical-menu', [
            'menuData'         => (object) ['menu' => $this->filteredMenuData],
            'configData'       => $this->configData,
            'role'             => $this->userRole,
            'currentRouteName' => $this->currentRouteName,
        ]);
    }
}
