<?php

namespace App\Livewire\Sections\Footer;

use App\Helpers\Helpers;
use Livewire\Component;

/**
 * Footer Livewire Component
 *
 * Renders the application footer and determines the Bootstrap container class
 * based on the application's layout settings.
 */
class Footer extends Component
{
    /**
     * Render the footer view.
     * Determines the container class based on the layout type.
     */
    public function render()
    {
        // Fetch global app classes and settings
        $configData = Helpers::appClasses();

        // Determine the container class for the footer based on the application's layout settings.
        // 'myLayout' is a key returned by Helpers::appClasses().
        $layoutType = $configData['myLayout'] ?? 'vertical'; // Default to 'vertical' if not set

        // Use 'container-xxl' for horizontal layouts, 'container-fluid' otherwise
        $containerNavForFooter = ($layoutType === 'horizontal') ? 'container-xxl' : 'container-fluid';

        return view('livewire.sections.footer.footer', [
            'containerNav' => $containerNavForFooter,
        ]);
    }
}
