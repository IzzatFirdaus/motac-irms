<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

// ContactUs component - renders the contact-us page view
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
