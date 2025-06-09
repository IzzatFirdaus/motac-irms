<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View as ViewFacade;
use Livewire\Component;
use stdClass;

class VerticalMenu extends Component
{
    public $role = null;
    public $menuData = null;
    public $configData = [];

    /**
     * Mount the component and initialize its properties.
     */
    public function mount($menuData = null, $role = null, $configData = [])
    {
        // 1. Set the user role from parameter or authenticated user
        $this->role = $role ?? (Auth::check() ? Auth::user()?->getRoleNames()->first() : null);

        // 2. Set the theme configuration data
        $this->configData = !empty($configData) ? $configData : \App\Helpers\Helpers::appClasses();

        // 3. Set the menu data from parameter or fallback to globally shared data
        $this->menuData = $menuData ?? ViewFacade::shared('menuData', new stdClass());

        // 4. Ensure menuData->menu is always an array to prevent errors in the view
        if (!isset($this->menuData->menu) || !is_array($this->menuData->menu)) {
            $this->menuData->menu = [];
        }
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.sections.menu.vertical-menu');
    }
}
