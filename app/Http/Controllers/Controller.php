<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs; // Corrected trait name from source if it was 'Dispatchable'
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // You could add shared helper methods or properties here if needed by many controllers,
    // but often, custom traits or services are a better approach for shared functionality
    // to keep the base controller lean.

    // Example:
    // protected function getDefaultPerPage(): int
    // {
    //     return config('pagination.default_size', 15);
    // }
}
