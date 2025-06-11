<?php

namespace App\Livewire\Sections\Menu;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ensure Log facade is imported
use Livewire\Component;
use stdClass;

class VerticalMenu extends Component
{
    public $menuData = null;
    public $configData = [];

    /**
     * Mount the component and initialize its properties.
     * UPDATED: Now loads menu data from config/menu.php
     */
    public function mount()
    {
        Log::info('VerticalMenu component mounting...'); // Log when component mounts

        // Set the theme configuration data from the helper
        $this->configData = \App\Helpers\Helpers::appClasses();
        Log::debug('VerticalMenu configData loaded: ' . json_encode($this->configData)); // Log config data

        // Load menu data directly from the Laravel config file
        try {
            // config('menu') loads the entire array from config/menu.php
            // We cast it to an object to maintain consistency for the view.
            $menuConfig = config('menu');
            $this->menuData = (object) $menuConfig;
            Log::info('VerticalMenu menu data loaded from config/menu.php.'); // Log successful load
            Log::debug('Menu data content: ' . json_encode($this->menuData)); // Log menu data content
        } catch (\Exception $e) {
            Log::error('Failed to load menu data from config/menu.php: ' . $e->getMessage());
            $this->menuData = new stdClass();
        }

        // Ensure menuData->menu is always an array to prevent errors in the view
        if (!isset($this->menuData->menu) || !is_array($this->menuData->menu)) {
            $this->menuData->menu = [];
            Log::warning('menuData->menu was not an array or was not set. Initialized as empty array.'); // Log if menu is empty/invalid
        }

        Log::info('VerticalMenu component mounted successfully.'); // Log completion of mount
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        Log::info('VerticalMenu component rendering...'); // Log when render method is called
        return view('livewire.sections.menu.vertical-menu');
    }
}
