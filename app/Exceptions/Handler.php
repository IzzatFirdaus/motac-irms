<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Exception types with custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // Example: \App\Exceptions\CustomNonCriticalException::class => \Psr\Log\LogLevel::INFO,
    ];

    /**
     * Exception types not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // Example: \Illuminate\Auth\AuthenticationException::class,
        // \Illuminate\Auth\Access\AuthorizationException::class,
        // \Symfony\Component\HttpKernel\Exception\HttpException::class,
        // \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        // \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Inputs never flashed to session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            // You may add custom reporting logic here, e.g., Sentry integration
        });

        // Example for custom error pages:
        // $this->renderable(function (CustomApplicationException $e, $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        //     }
        //     // Optionally: return redirect()->route('misc.error.show', ['errorCode' => 'custom_error_code']);
        // });
    }
}
