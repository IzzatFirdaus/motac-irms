<?php

namespace App\Livewire\Sections\Menu;

use App\Models\User; // Ensure this is used if you use User::find or Auth::user()
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View as ViewFacade; // Use an alias
use Livewire\Component;
use stdClass; // For creating a default empty menu object

class VerticalMenu extends Component
{
    public $role = null;
    public $menuData = null;
    public $configData = [];

    public function mount($menuData = null, $role = null, $configData = [])
    {
        Log::info('[VerticalMenu MOUNT STARTING] --- Component ID: ' . $this->getId());

        // 1. Determine and set Role
        if ($role !== null) {
            $this->role = $role;
            Log::info('[VerticalMenu Component] Role received via parameter: \'' . $this->role . '\'');
        } else {
            $this->role = Auth::check() ? Auth::user()?->getRoleNames()->first() : null;
            Log::info('[VerticalMenu Component] Role determined from Auth: \'' . $this->role . '\'');
        }

        // 2. Determine and set ConfigData
        if (!empty($configData)) {
            $this->configData = $configData;
        } else {
            // Fallback if not passed - ensure App\Helpers\Helpers exists and is correct
            if (class_exists(\App\Helpers\Helpers::class) && method_exists(\App\Helpers\Helpers::class, 'appClasses')) {
                $this->configData = \App\Helpers\Helpers::appClasses();
                Log::info('[VerticalMenu Component] ConfigData set from App\Helpers\Helpers::appClasses().');
            } else {
                Log::warning('[VerticalMenu Component] App\Helpers\Helpers::appClasses() not available for ConfigData. Using empty array.');
                $this->configData = [];
            }
        }
        Log::info('[VerticalMenu Component] ConfigData navbarFull check: ' . ($this->configData['navbarFull'] ?? 'not_set'));

        // 3. Determine and set MenuData
        Log::info('[VerticalMenu Component] Initial $menuData parameter type: ' . gettype($menuData) . '. Is object with menu array? ' . (is_object($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu) ? 'Yes' : 'No'));

        if ($menuData !== null && is_object($menuData) && property_exists($menuData, 'menu') && is_array($menuData->menu)) {
            $this->menuData = $menuData;
            Log::info('[VerticalMenu Component] menuData successfully assigned from passed parameter. Item count: ' . count($this->menuData->menu));
        } else {
            Log::warning('[VerticalMenu Component] menuData parameter was null or invalid. Passed parameter content: ' . substr(json_encode($menuData), 0, 200) . '...'); // Log snippet
            Log::info('[VerticalMenu Component] Attempting to fetch menuData from View::shared(\'menuData\').');
            $sharedMenuData = ViewFacade::shared('menuData');

            if ($sharedMenuData instanceof stdClass && property_exists($sharedMenuData, 'menu') && is_array($sharedMenuData->menu)) {
                $this->menuData = $sharedMenuData;
                Log::info('[VerticalMenu Component] menuData successfully assigned from View::shared. Item count: ' . count($this->menuData->menu));
            } else {
                Log::error('[VerticalMenu Component] Failed to get valid menuData from View::shared. Data type: ' . gettype($sharedMenuData) . '. Will use empty menu.');
                $this->menuData = new stdClass();
                $this->menuData->menu = [];
            }
        }

        // Final check and detailed log of the menuData the component will use
        if (is_object($this->menuData) && property_exists($this->menuData, 'menu') && is_array($this->menuData->menu)) {
            $itemCount = count($this->menuData->menu);
            Log::info("[VerticalMenu Component] Final menuData->menu for rendering. Item count: {$itemCount}");
            if ($itemCount > 0) {
                Log::info('[VerticalMenu Component] Structure of FIRST menu item: ' . json_encode($this->menuData->menu[0]));
            } else {
                Log::info('[VerticalMenu Component] Final menuData->menu is an empty array.');
            }
        } else {
            Log::error('[VerticalMenu Component] Final $this->menuData is not the expected object with a menu array. Actual type: ' . gettype($this->menuData) . '; Content: ' . substr(json_encode($this->menuData),0,500));
            // Ensure $this->menuData is always an object with ->menu for the view
            if (!is_object($this->menuData) || !property_exists($this->menuData, 'menu') || !is_array($this->menuData->menu)) {
                $this->menuData = new stdClass();
                $this->menuData->menu = [];
            }
        }
        Log::info('[VerticalMenu MOUNT ENDING] ---');
    }

    public function render()
    {
        return view('livewire.sections.menu.vertical-menu');
    }
}
