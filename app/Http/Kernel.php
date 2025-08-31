<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * HTTP Kernel - Defines the middleware stack for the MOTAC IRMS application.
 *
 * This kernel includes middleware for:
 * - Multi-language support with automatic locale detection
 * - Role-based access control (RBAC) using Spatie Permission
 * - Custom authorization middleware for specific features
 */
class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * These middleware are run during every request to the application.
     */
    protected $middleware = [
        // Trust proxy headers for load balancer/reverse proxy setups
        \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,

        // Handle CORS (Cross-Origin Resource Sharing) requests
        \Illuminate\Http\Middleware\HandleCors::class,

        // Prevent requests during maintenance mode (except for allowed users)
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validate POST request size limits
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // String processing middleware
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     * These middleware groups are applied to specific route groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookie encryption and handling
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // Session management
            \Illuminate\Session\Middleware\StartSession::class,
            // Note: AuthenticateSession can be enabled if you want to invalidate
            // sessions when password changes
            // \Illuminate\Session\Middleware\AuthenticateSession::class,

            // Error handling and CSRF protection
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Route model binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // IMPORTANT: Locale detection middleware for multi-language support
            // This middleware works with the SuffixedTranslator to automatically
            // load the correct language files (forms_en.php, forms_ms.php, etc.)
            \App\Http\Middleware\LocaleMiddleware::class,

            // Optional: Custom theme defaults middleware
            // \App\Http\Middleware\SetMotacThemeDefaults::class,
        ],

        'api' => [
            // Uncomment if using Sanctum for SPA authentication
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // API rate limiting
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',

            // Route model binding for API routes
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Optional: API-specific locale middleware
            // \App\Http\Middleware\ApiLocaleMiddleware::class,
        ],
    ];

    /**
     * Route middleware aliases.
     * These middleware can be assigned to routes individually.
     */
    protected $middlewareAliases = [
        // --------------------------------------------------
        // Laravel Built-in Middleware
        // --------------------------------------------------

        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'              => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // --------------------------------------------------
        // Spatie Permission Package (RBAC - Role-Based Access Control)
        // --------------------------------------------------

        // Check if user has a specific role (e.g., 'role:Admin')
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,

        // Check if user has a specific permission
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,

        // Check if user has either a role OR a permission
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

        // --------------------------------------------------
        // Custom Application Middleware
        // --------------------------------------------------

        // Allow admin users to access the application during maintenance mode
        'allow_admin_during_maintenance' => \App\Http\Middleware\AllowAdminDuringMaintenance::class,

        // Validate webhook signatures for external integrations
        'validate.webhook.signature' => \App\Http\Middleware\ValidateSignature::class,

        // Grade-level authorization middleware for hierarchical permissions
        'check.gradelevel' => \App\Http\Middleware\CheckGradeLevel::class,
        'check.usergrade'  => \App\Http\Middleware\CheckUserGrade::class,

        // Log viewer authorization
        'authorize.logviewer' => \App\Http\Middleware\AuthorizeLogViewer::class,

        // Ensure user belongs to BPM (Information Management Division)
        'is.bpm' => \App\Http\Middleware\EnsureUserIsBpmStaff::class,

        // --------------------------------------------------
        // Permission-Based Access Control
        // --------------------------------------------------

        // Control access to application logs and debugging information
        'view_logs' => \App\Http\Middleware\ViewLogs::class,

        // Permission to view approval tasks - bind to ViewApprovalTasks middleware
        'view_approval_tasks' => \App\Http\Middleware\ViewApprovalTasks::class,

        // Permission to view approval history
        'view_approval_history' => \App\Http\Middleware\CheckPermission::class.':view_approval_history',

        // Optional: Fallback log viewer middleware if needed
        // 'logviewer.auth' => \Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer::class,
    ];
}
