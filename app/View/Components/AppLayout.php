<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        // This tells the <x-app-layout> component to render the
        // resources/views/layouts/app.blade.php file.
        return view('layouts.app');
    }
}
