<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Livewire VerticalMenu component
 * Loads the menu structure from config/menu.php and exposes it for the Blade view.
 * Handles role-based visibility, guest-only items, and recursive submenus.
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
        $menuConfig = config('menu');
        $this->menuData = (object) $menuConfig;

        // Ensure menuData->menu is always an array
        if (!isset($this->menuData->menu) || !is_array($this->menuData->menu)) {
            $this->menuData->menu = [];
        }
    }

    /**
     * Returns all roles of the authenticated user or empty collection if guest.
     */
    public function getUserRolesProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        // Check roles for both guards like Dashboard component does
        $webUser = \Auth::guard('web')->user();
        $sanctumUser = \Auth::guard('sanctum')->user();
        $webRoles = $webUser && $webUser->roles ? $webUser->roles->pluck('name') : collect();
        $sanctumRoles = $sanctumUser && $sanctumUser->roles ? $sanctumUser->roles->pluck('name') : collect();
        return $webRoles->merge($sanctumRoles)->unique();
    }

    /**
     * Returns the current route name for active checks in Blade.
     */
    public function getCurrentRouteNameProperty()
    {
        return \Route::currentRouteName();
    }

        /**
     * Get the filtered menu data based on user roles.
     * - Guests only see items with 'guestOnly' => true and NOT requiring roles.
     * - Authenticated users only see items with their role in 'role' and NOT guestOnly.
     * - Submenus and headers are recursively filtered.
     */
    public function getFilteredMenuDataProperty()
    {
        $menuData = config('menu.menu', []);
        $userRoles = $this->getUserRolesProperty();

        // Convert collection to array if needed
        if ($userRoles instanceof \Illuminate\Support\Collection) {
            $userRoles = $userRoles->toArray();
        }

    \Log::info('VerticalMenu getFilteredMenuDataProperty - User roles:', ['roles' => $userRoles, 'count' => count($userRoles)]);

        $filteredMenu = collect($menuData)->map(function ($item) use ($userRoles) {
            return $this->filterMenuItem($item, $userRoles);
        })->filter(function ($item) {
            return $item !== null;
        })->toArray();

        return $filteredMenu;
    }

    /**
     * Filter a single menu item and its submenus recursively.
     */
    private function filterMenuItem($item, $userRoles)
    {
        // Convert array to object if needed for consistent access
        if (is_array($item)) {
            $item = (object) $item;
        }

        // Check if this item should be shown
        if (!$this->shouldShowMenuItem($item, $userRoles)) {
            return null;
        }

        // If the item has submenus, filter them recursively
        if (isset($item->submenu) && is_array($item->submenu)) {
            $filteredSubmenu = collect($item->submenu)->map(function ($subItem) use ($userRoles) {
                return $this->filterMenuItem($subItem, $userRoles);
            })->filter(function ($subItem) {
                return $subItem !== null;
            })->toArray();

            // If all submenu items are filtered out, hide the parent menu
            if (empty($filteredSubmenu)) {
                return null;
            }

            // Update the item with filtered submenu
            $item = clone $item;
            $item->submenu = $filteredSubmenu;
        }

        return $item;
    }

    /**
     * Check if a menu item should be shown based on roles and guest access.
     */
    private function shouldShowMenuItem($item, $userRoles)
    {
        // Convert array to object if needed for consistent access
        if (is_array($item)) {
            $item = (object) $item;
        }

        // Convert collection to array if needed
        if ($userRoles instanceof \Illuminate\Support\Collection) {
            $userRoles = $userRoles->toArray();
        }

        // Special handling for Admin role - can see everything
        if (in_array('Admin', $userRoles)) {
            return true;
        }

        // Check if user is authenticated
        $isAuthenticated = Auth::check();

        // Handle guest-only items
        if (isset($item->guestOnly) && $item->guestOnly) {
            return !$isAuthenticated; // Only show to guests
        }

        // If guest user and item requires roles, hide it
        if (!$isAuthenticated && isset($item->role) && !empty($item->role)) {
            return false;
        }

        // If no role restrictions, show to authenticated users
        if (!isset($item->role) || empty($item->role)) {
            return $isAuthenticated;
        }

        // Check role intersection
        $itemRoles = is_array($item->role) ? $item->role : [$item->role];

        // CRITICAL BUG FIX: User with 'User' role should NOT see Reports or System Settings
        // Force explicit denial for these sections if user only has 'User' role
        if ((isset($item->name) && in_array($item->name, ['menu.reports.title', 'menu.system_settings.title'])) ||
            (isset($item->menuHeader) && in_array($item->menuHeader, ['menu.section.reports_analytics', 'menu.section.system_settings']))) {

            // If user ONLY has 'User' role, explicitly deny access to Reports and System Settings
            if (count($userRoles) === 1 && in_array('User', $userRoles)) {
                return false;
            }
        }

        $hasMatchingRole = count(array_intersect($userRoles, $itemRoles)) > 0;

        return $hasMatchingRole;
    }    /**
     * Render the component's view.
     */
    public function render()
    {
        // Use a distinct key to avoid shadowing the component public property $menuData
        return view('livewire.sections.menu.vertical-menu', [
            'filteredMenu' => (object) ['menu' => $this->filteredMenuData],
            'configData' => $this->configData,
            'roles' => $this->userRoles,
            'currentRouteName' => $this->currentRouteName,
        ]);
    }
}
