<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\ViewErrorBag;

class AlertErrors extends Component
{
    /**
     * The title to display for the alert.
     *
     * @var string|null
     */
    public ?string $title;

    /**
     * The validation errors bag.
     *
     * @var \Illuminate\Support\ViewErrorBag
     */
    public ViewErrorBag $errors;

    /**
     * Create a new component instance.
     *
     * @param string|null $title
     * @param \Illuminate\Support\ViewErrorBag|null $errors
     * @return void
     */
    public function __construct(?string $title = null, ?ViewErrorBag $errors = null)
    {
        // If a title is provided, use it. Otherwise, let the x-alert component use its default for 'danger'.
        $this->title = $title;
        // If no specific errors bag is passed, use the global $errors bag.
        $this->errors = $errors ?? session()->get('errors', new ViewErrorBag);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('components.alert-errors'); // This explicitly tells Laravel to look for resources/views/components/alert-errors.blade.php
    }
}
