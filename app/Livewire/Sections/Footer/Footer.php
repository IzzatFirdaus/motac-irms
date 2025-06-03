<?php

namespace App\Livewire\Sections\Footer;

use Livewire\Component;
use App\Helpers\Helpers; // Import the Helpers class

class Footer extends Component
{
    public function render()
    {
        $configData = Helpers::appClasses(); // Fetches global app classes and settings

        // Determine the container class for the footer based on the application's layout settings.
        // 'myLayout' is a key returned by Helpers::appClasses().
        // Example logic: If 'myLayout' is 'horizontal', it might use 'container-xxl'.
        // Otherwise, for 'vertical' or other layouts, 'container-fluid' is used.
        // This logic should align with your theme's overall structure for consistency.

        $layoutType = $configData['myLayout'] ?? 'vertical'; // Default to 'vertical' if 'myLayout' is not set

        // Adjust this logic if your theme uses a different key from $configData
        // or has more complex rules for determining the footer container width.
        $containerNavForFooter = ($layoutType === 'horizontal') ? 'container-xxl' : 'container-fluid';

        // The previous error was due to accessing $configData['contentLayout'], which isn't
        // returned by Helpers::appClasses(). This revised logic uses $configData['myLayout'].

        return view('livewire.sections.footer.footer', [
            'containerNav' => $containerNavForFooter
        ]);
    }
}
