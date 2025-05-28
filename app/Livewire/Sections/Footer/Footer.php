<?php

namespace App\Livewire\Sections\Footer;

use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        // Pass the containerNav variable to the view, consistent with app.blade.php
        $configData = \App\Helpers\Helpers::appClasses();
        $containerNav = ($configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';

        return view('livewire.sections.footer.footer', [
            'containerNav' => $containerNav
        ]);
    }
}
