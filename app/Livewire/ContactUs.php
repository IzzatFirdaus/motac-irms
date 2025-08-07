<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * ContactUs Livewire Component
 *
 * Renders the "Hubungi Kami" (Contact Us) page view.
 * In the future, could be extended to handle direct message/contact forms.
 */
class ContactUs extends Component
{
    /**
     * Render the contact us page.
     */
    public function render(): View
    {
        return view('livewire.contact-us');
    }
}
