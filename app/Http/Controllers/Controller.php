<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs; // Standard Laravel trait
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    // Shared helper methods or properties can be added here if needed by many controllers.
    // However, for more complex shared functionality, custom traits or services are often preferred
    // to maintain a lean base controller.

    // Example:
    // protected function getDefaultPerPage(): int
    // {
    //     return config('pagination.default_size', 15);
    // }
}
