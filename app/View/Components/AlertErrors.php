<?php

namespace App\View\Components;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;
use Illuminate\View\View;

class AlertErrors extends Component
{
    /**
     * The title to display for the alert.
     */
    public ?string $title;

    /**
     * The validation errors bag.
     */
    public ViewErrorBag $errors;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title = null, ?ViewErrorBag $errors = null)
    {
        // If a title is provided, use it. Otherwise, let the x-alert component use its default for 'danger'.
        $this->title = $title;
        // If no specific errors bag is passed, use the global $errors bag.
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.alert-errors'); // This explicitly tells Laravel to look for resources/views/components/alert-errors.blade.php
    }
}
