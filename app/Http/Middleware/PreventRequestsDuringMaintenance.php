<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
// Remove Illuminate\Support\Facades\Route and Illuminate\Support\Facades\File if not used elsewhere.

class PreventRequestsDuringMaintenance extends Middleware
{
  /**
   * The URIs that should be reachable while maintenance mode is enabled.
   * Add essential URIs here, like login for admin access, specific webhook URIs,
   * or any URI handled by AllowAdminDuringMaintenance.
   *
   * @var array<int, string>
   */
  protected $except = [
    // It's common to allow access to login routes for admins
    // The AllowAdminDuringMaintenance middleware will then check their role.
    'login',
    '/login', // If your login route might be accessed with or without leading slash
    'logout',
    '/logout',

    // Language switcher routes might be needed if your maintenance page itself is multi-language
    // or if login page needs language switching.
    'lang/*', // e.g., /lang/en, /lang/ms

    // Webhook for deployment if it needs to run during maintenance
    // Ensure this matches the route defined in web.php
    // 'webhooks/deploy', // Example path from your web.php, adjust if needed

    // Any other critical URIs that must function during maintenance.
    // For example, if you have a specific status page or an IP bypass mechanism.
  ];

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  // The handle method from the parent class will now be used,
  // which correctly processes the static $except array.
  // Remove the overridden handle() method that was adding all routes.
  // If you had other specific logic in handle(), it would need to be re-evaluated.
  // For now, reverting to the parent's handle() is the safest approach.
}
