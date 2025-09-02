
<?php

declare(strict_types=1);

// Editor-only stubs for Laravel core classes and common middleware used in Kernel
// Use eval() inside guarded class_exists checks to declare namespaced classes
// without introducing namespace declarations inside conditional blocks.

if (! class_exists('\\Illuminate\\Foundation\\Http\\Kernel')) {
    eval('namespace Illuminate\\Foundation\\Http { class Kernel {} }');
}

if (! class_exists('\\Illuminate\\Http\\Middleware\\HandleCors')) {
    eval('namespace Illuminate\\Http\\Middleware { class HandleCors {} class SetCacheHeaders {} }');
}

if (! class_exists('\\Illuminate\\Foundation\\Http\\Middleware\\ValidatePostSize')) {
    eval('namespace Illuminate\\Foundation\\Http\\Middleware { class ValidatePostSize {} class ConvertEmptyStringsToNull {} }');
}

if (! class_exists('\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse')) {
    eval('namespace Illuminate\\Cookie\\Middleware { class AddQueuedCookiesToResponse {} }');
}

if (! class_exists('\\Illuminate\\Session\\Middleware\\StartSession')) {
    eval('namespace Illuminate\\Session\\Middleware { class StartSession {} class AuthenticateSession {} }');
}

if (! class_exists('\\Illuminate\\View\\Middleware\\ShareErrorsFromSession')) {
    eval('namespace Illuminate\\View\\Middleware { class ShareErrorsFromSession {} }');
}

if (! class_exists('\\Illuminate\\Routing\\Middleware\\SubstituteBindings')) {
    eval('namespace Illuminate\\Routing\\Middleware { class SubstituteBindings {} class ThrottleRequests {} class ValidateSignature {} }');
}

if (! class_exists('\\Illuminate\\Auth\\Middleware\\AuthenticateWithBasicAuth')) {
    eval('namespace Illuminate\\Auth\\Middleware { class AuthenticateWithBasicAuth {} class RequirePassword {} class Authorize {} class EnsureEmailIsVerified {} }');
}

// Spatie permission middleware stubs
if (! class_exists('\\Spatie\\Permission\\Middleware\\RoleMiddleware')) {
    eval('namespace Spatie\\Permission\\Middleware { class RoleMiddleware {} class PermissionMiddleware {} class RoleOrPermissionMiddleware {} }');
}
