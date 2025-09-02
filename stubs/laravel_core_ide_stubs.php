<?php

// IDE-only stubs for Laravel core classes and middleware.
// These empty class declarations exist only to help static analysis tools
// (Intelephense, PHPStan) resolve types used in app/Http/Kernel.php and routes.

namespace Illuminate\Foundation\Http {
    class Kernel {}
}

namespace Illuminate\Http\Middleware {
    class HandleCors {}
    class SetCacheHeaders {}
}

namespace Illuminate\Foundation\Http\Middleware {
    class ValidatePostSize {}
    class ConvertEmptyStringsToNull {}
}

namespace Illuminate\Cookie\Middleware {
    class AddQueuedCookiesToResponse {}
}

namespace Illuminate\Session\Middleware {
    class StartSession {}
    class AuthenticateSession {}
}

namespace Illuminate\View\Middleware {
    class ShareErrorsFromSession {}
}

namespace Illuminate\Routing\Middleware {
    class SubstituteBindings {}
    class ThrottleRequests {}
    class ValidateSignature {}
}

namespace Illuminate\Auth\Middleware {
    class AuthenticateWithBasicAuth {}
    class RequirePassword {}
    class Authorize {}
    class EnsureEmailIsVerified {}
}

namespace Spatie\Permission\Middleware {
    class RoleMiddleware {}
    class PermissionMiddleware {}
    class RoleOrPermissionMiddleware {}
}
