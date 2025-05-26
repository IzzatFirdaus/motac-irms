<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View; // Added for return type hint consistency

// This is a simple Livewire component to render the contact us view.
// It currently doesn't contain form handling logic.

class ContactUs extends Component
{
    /**
     * Render the component's view.
     */
    public function render(): View // Added return type hint
    {
        // This method simply returns the Blade view for the contact us page.
        // The contact form UI and logic would typically be within the Blade file.
        return view('livewire.contact-us');
    }
}
