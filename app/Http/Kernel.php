<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  /**
   * The application's global HTTP middleware stack.
   * These middleware are run during every request to your application.
   * System Design Reference: 3.1 Middleware.
   *
   * @var array<int, class-string|string>
   */
  protected $middleware = [
    \App\Http\Middleware\TrustHosts::class,
    \App\Http\Middleware\TrustProxies::class, // Important if behind a load balancer or reverse proxy
    \Illuminate\Http\Middleware\HandleCors::class, // Handles Cross-Origin Resource Sharing
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class, // Trims whitespace from string inputs
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Converts empty strings to null
  ];

  /**
   * The application's route middleware groups.
   *
   * @var array<string, array<int, class-string|string>>
   */
  protected $middlewareGroups = [
    'web' => [
      \App\Http\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      // \Illuminate\Session\Middleware\AuthenticateSession::class, // Handled by Fortify/Jetstream if used
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \App\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
      \App\Http\Middleware\LocaleMiddleware::class, // Crucial for localization
      // Middleware for MOTAC specific theme/settings if needed globally after session start
      // \App\Http\Middleware\SetMotacThemeDefaults::class, // Example
    ],

    'api' => [
      // Consider \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class if using Sanctum for SPA cookies
      \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api', // API rate limiting
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
      // Consider adding LocaleMiddleware here too if your API needs to be localized based on headers
      // \App\Http\Middleware\ApiLocaleMiddleware::class, // Example for API-specific locale handling
    ],
  ];

  /**
   * The application's middleware aliases.
   * Aliases may be used to conveniently assign middleware to routes and groups.
   * System Design Reference: 3.1 Middleware, 8.1 RBAC using Spatie.
   *
   * @var array<string, class-string|string>
   */
  protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class, // Default Laravel auth redirect middleware
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class, // For Jetstream/Fortify session validation
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class, // For policy checks
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // For sensitive actions requiring password re-entry
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // For signed URLs
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // For email verification protected routes

    // Spatie Permission Middleware Aliases - System Design 8.1
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

    // Custom Application Middleware Aliases
    // System Design 3.1
    'allow_admin_during_maintenance' => \App\Http\Middleware\AllowAdminDuringMaintenance::class,

    // System Design 3.2 (Webhook security)
    'validate.webhook.signature' => \App\Http\Middleware\ValidateSignature::class,

    // Custom grade-based authorization middleware
    // System Design 3.1 (Middleware for grade levels)
    'check.gradelevel' => \App\Http\Middleware\CheckGradeLevel::class,
    'check.usergrade' => \App\Http\Middleware\CheckUserGrade::class,

    'authorize.logviewer' => \App\Http\Middleware\AuthorizeLogViewer::class,

    'is.bpm' => \App\Http\Middleware\EnsureUserIsBpmStaff::class,
  ];
}
