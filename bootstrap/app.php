<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Confirmed usage for all web routes
        api: __DIR__.'/../routes/api.php', // Confirmed usage for API routes
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php', // Confirmed usage for broadcasting
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // This correctly appends the custom LocaleMiddleware to the 'web' group.
        // This was the source of the previous error.
        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);

        // This section registers all required middleware aliases from your System Design document.
        // These are used for route-specific authorization and validation.
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Custom middleware aliases from your project design
            'allow_admin_during_maintenance' => \App\Http\Middleware\AllowAdminDuringMaintenance::class,
            'validate.webhook.signature' => \App\Http\Middleware\ValidateSignature::class,
            'check.gradelevel' => \App\Http\Middleware\CheckGradeLevel::class,
            'check.usergrade' => \App\Http\Middleware\CheckUserGrade::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
